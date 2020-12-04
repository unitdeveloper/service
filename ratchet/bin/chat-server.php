<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use zetsoft\service\ratchet\Chat;

require __DIR__ . '/../../../vendor/autoload.php';


$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )

    ),
    1997
);

$server->run();