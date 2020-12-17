<?php

namespace zetsoft\service\socket;

require Root . '/vendors/netter/ALL/vendor/autoload.php';

use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use zetsoft\models\chat\ChatMessage;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZJsonHelper;

class RatchetchatTest implements MessageComponentInterface
{
    private array $connections = [];
    private array $connectionUserMap = [];

    public function onOpen(ConnectionInterface $connection)
    {
        echo "New connection! ({$connection->resourceId})\n";
        $this->registerConnection($connection);
    }

    public function onMessage(ConnectionInterface $from, $rawMessage)
    {
        $message = json_decode($rawMessage);

        if (!$message) {
            $reply = [
                'type' => 'ERROR',
                'payload' => [
                    'errorMessage' => "JSON decryption error! {$rawMessage} cannot be decoded. Sent message might be too long or invalid JSON."
                ]
            ];
            $from->send(json_encode($reply));
            return;
        }

        $this->handleMessage($from, $message);
    }

    public function onClose(ConnectionInterface $connection)
    {
        echo "Connection {$connection->resourceId} has disconnected\n";

        $this->forgetConnection($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $this->forgetConnection($connection);
        $connection->close();
    }

    public function run()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new RatchetchatTest()
                )
            ),
            2000,
            '94.158.52.244'
        );
        $server->run();
    }

    private function handleMessage(ConnectionInterface $from, $data)
    {
        switch ($data->type) {
            case 'AUTHORIZATION':
            {
                $this->handleAuthorization($from, $data->payload);
                break;
            }
            case 'QUERY':
            {
                $this->handleQuery($from, $data->payload);
                break;
            }
            case 'SEND_MESSAGE':
            {
                $chatMessageDto = new ChatMessage();
                $chatMessage = $data->payload;
                $chatMessageDto->sender = $this->getConnectedUserId($from);
                $chatMessageDto->receiver = $chatMessage->to;
                $chatMessageDto->text = $chatMessage->text;
                $chatMessageDto->time = Az::$app->cores->date->dateTime();
                $chatMessageDto->save();

                $userId = $this->getConnectedUserId($from);
                $fellowId = $chatMessage->to;

                $messagesDto = ChatMessage::find()
                    ->where(['sender' => $userId, 'receiver' => $fellowId])
                    ->orWhere(['sender' => $fellowId, 'receiver' => $userId])
                    ->orderBy('modified_at')
                    ->all();

                $dataForSender = [
                    'type' => 'NEW_MESSAGE',
                    'payload' => [
                        'fellow' => $fellowId,
                        'conversation' => array_map(array($this, 'transformMessageDto'), $messagesDto),
                    ],
                ];

                $data = [
                    'type' => 'NEW_MESSAGE',
                    'payload' => [
                        'fellow' => $userId,
                        'conversation' => array_map(array($this, 'transformMessageDto'), $messagesDto),
                    ],
                ];

                $from->send(json_encode($dataForSender));
                $this->sendToOthers($from, $data);
            }
        }
    }

    private function handleAuthorization(ConnectionInterface $from, $identity)
    {
        $this->mapUserToConnection($identity->id, $from);
    }

    private function handleQuery(ConnectionInterface $from, $query)
    {
        switch ($query->target) {
            case 'CONVERSATION':
            {
                $userId = $this->getConnectedUserId($from);
                $fellowId = $query->userId;

                $messagesDto = ChatMessage::find()
                    ->where(['sender' => $userId, 'receiver' => $fellowId])
                    ->orWhere(['sender' => $fellowId, 'receiver' => $userId])
                    ->orderBy('modified_at')
                    ->all();

                $reply = [
                    'type' => 'QUERY_RESULT',
                    'payload' => [
                        'target' => $query->target,
                        'result' => [
                            'fellow' => $fellowId,
                            'conversation' => array_map(array($this, 'transformMessageDto'), $messagesDto),
                        ],
                    ],
                ];

                $from->send(json_encode($reply));

                break;
            }

            case 'USERS':
            {
                $userId = $this->getConnectedUserId($from);
                $fellowDtos = User::find()->where('id != :id', ['id' => $userId])->all();

                $fellows = array_map(array($this, 'transformUserDto'), $fellowDtos);

                $reply = [
                    'type' => 'QUERY_RESULT',
                    'payload' => [
                        'target' => $query->target,
                        'result' => $fellows,
                    ],
                ];

                $from->send(json_encode($reply));

                break;
            }

            case 'USER':
            {
                $userDto = User::findOne($query->id);

                $reply = [
                    'type' => 'QUERY_RESULT',
                    'payload' => [
                        'target' => $query->target,
                        'result' => [
                            'id' => $userDto->id,
                            'name' => $userDto->name,
                            'photo' => $userDto->userPhoto(),
                        ],
                    ],
                ];

                $from->send(json_encode($reply));

                break;
            }
        }
    }

    private function sendToOthers($from, $data)
    {
        foreach ($this->connections as $connection) {
            if ($connection == $from) {
                continue;
            }

            $connection->send(json_encode($data));
        }
    }

    private function registerConnection(ConnectionInterface $connection)
    {
        $this->connections[] = $connection;
    }

    private function mapUserToConnection($userId, ConnectionInterface $connection)
    {
        $this->connectionUserMap[(string)$connection->resourceId] = $userId;
    }

    private function forgetConnection(ConnectionInterface $connection)
    {
        unset($this->connectionUserMap[$connection->resourceId]);
        array_splice($this->connections, array_search($connection, $this->connections), 1);
    }

    private function getConnectedUserId(ConnectionInterface $connection)
    {
        return $this->connectionUserMap[$connection->resourceId];
    }

    private function transformUserDto(User $userDto)
    {
        return [
            'id' => $userDto->id,
            'name' => $userDto->title,
            'photo' => $userDto->userPhoto(),
        ];
    }

    private function transformMessageDto(ChatMessage $messageDto)
    {
        return [
            'from' => $messageDto->sender,
            'to' => $messageDto->receiver,
            'text' => $messageDto->text,
            'when' => $messageDto->created_at,
        ];
    }
}
