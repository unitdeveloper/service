<?php

namespace zetsoft\service\socket;

use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use zetsoft\models\core\CoreQueue;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class Logger extends ZFrame implements MessageComponentInterface
{
    protected $clients;
    public $data;

    public function init()
    {
        parent::init();
        $this->clients = new \SplObjectStorage;
    }


    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $message = json_decode($msg);

        $send = null;
        $data = CoreQueue::find()->where([
            'session' => $message->cookie,
            'status' => CoreQueue::status['process']
        ])->one();
        if ($data !== null) {
            $data = $data->cmd;
            $res = ZArrayHelper::getValue($this->data, $from->resourceId);

            if ($res === null)
                $this->data[$from->resourceId] = $data;
            switch ($message->type) {
                case "all":
                    $this->data[$from->resourceId] = $data;
                    $send = $data;
                    break;
                case "one":
                    $send = substr($data, strlen($res), strlen($data));
                    $this->data[$from->resourceId] = $data;
                    break;
                case "stop":
                    $model = CoreQueue::find()->where([
                        'session' => $message->cookie,
                        'status' => CoreQueue::status['process']
                    ])->one();
                    Az::$app->utility->execs->exec('taskkill /F /PID '. $model->pid);
                    $model->status = CoreQueue::status['finished'];
                    $send = Az::l('Процесс закончено');
                    break;
                default:
                    break;
            }
        } else {
            $send = 'empty';
        }
        foreach ($this->clients as $client) {
            if ($from === $client) {
                $client->send($send);
            }
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        unset($this->data[$conn->resourceId]);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function run()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Logger()
                )
            ),
            5555,
            '0.0.0.0'
        );
        $server->run();
    }
}
