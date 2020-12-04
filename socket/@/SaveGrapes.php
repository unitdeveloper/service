<?php

namespace zetsoft\service\socket;

use Ratchet\App;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use zetsoft\models\page\PageAction;
use zetsoft\service\smart\Widget;
use zetsoft\service\utility\Pregs;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class SaveGrapes implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
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
//        $info = explode(",", $msg);
        $data = json_decode($msg);
        $html = null;
        $actionId = null;
        $file = null;

        $html = $data->content;
        $actionId = $data->actionId;
        $file = $data->config;
        $PageAction = null;

        if (!empty($actionId))
            $PageAction = PageAction::findOne($actionId);
        $process = new Widget();
        $content = $process->pregMatchFix($html);


        if ($PageAction)
            $content = Az::$app->smart->widget->writeFile($PageAction, $content);

        foreach ($this->clients as $client) {
            if ($from === $client) {
                if (file_put_contents($file, $content)) {
                    $client->send(1);
                }else{
                    $client->send(0);
                }

            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

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
                    new SaveGrapes()
                )
            ),
            1997
        );
        $server->run();
    }
}
