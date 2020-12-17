<?php

namespace zetsoft\service\socket;

require Root . '/vendors/netapp/vendor/autoload.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use zetsoft\system\kernels\ZFrame;

class start1 extends ZFrame{

    public function  example(){
      //  require_once __DIR__ . '/vendor/autoload.php';

// Listen port 2021 for socket.io client
        $io = new SocketIO(2021);
        $io->on('connection', function ($socket) use ($io) {
            $socket->on('chat message', function ($msg) use ($io) {
                $io->emit('chat message', $msg);
            });
        });

        Worker::runAll();
    }
}
