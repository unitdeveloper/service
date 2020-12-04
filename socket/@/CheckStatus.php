<?php

namespace zetsoft\service\socket;

use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class CheckStatus extends ZFrame implements MessageComponentInterface
{
    protected $clients;
    public $users;

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

        $data = json_decode($msg);

        $this->users[$from->resourceId] = $data->id;
        $this->users[$from->resourceId] = $data->id;

        $status = $data->status;

        $model = User::findOne($data->id);

        $model->status = $status;
        $model->save();


        foreach ($this->clients as $client) {
            if ($from === $client) {

                $client->send($data->status);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $model = User::findOne($this->users[$conn->resourceId]);
        $model->status = "offline";
        $model->lastseen = date("Y-m-d H:i:s", time());
        $model->save();
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
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
                    new CheckStatus()
                )
            ),
            7777,
            '0.0.0.0'
        );
        $server->run();
    }
}
