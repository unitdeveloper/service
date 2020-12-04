<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */


namespace zetsoft\service\reacts;

use GuzzleHttp\Psr7\LimitStream;
use React\Socket\Connector;
use React\Socket\LimitingServer;
use React\Socket\Server;
use zetsoft\system\kernels\ZFrame;

class Socket1 extends ZFrame
{
       public function run(){

//
//           $loop = \React\EventLoop\Factory::create();
//           $socket = new Server('127.0.0.1:8080', $loop);
//           $socket->on('connection', function (\React\Socket\ConnectionInterface $connection){
//               echo $connection->getRemoteAddress(). PHP_EOL;
//          });


           /*$socket->on('connection', function (\React\Socket\ConnectionInterface $connection){
                $connection->write("hello". $connection->getRemoteAddress(). "!\n");
               $connection->write("Welcome to this amazing server!\n");
               $connection->write("Here's a tip: don't say anything.\n");

               $connection->on('data', function ($data) use ($connection){
                    $connection->close();
               });
           });*/





//           $loop->run();
       }
}
                                           
