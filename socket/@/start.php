<?php

namespace zetsoft\service\socket;
require Root . '/vendori/netapp/vendor/autoload.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use zetsoft\system\kernels\ZFrame;
/*
 * Author by Keldiyor
 * https://packagist.org/packages/workerman/phpsocket.io
 */

class start extends Zframe {

    public function example(){

     //   require_once __DIR__ . '/vendor/autoload.php';


// SSL context
        $context = array(
            'ssl' => array(
                'local_cert' => '/your/path/of/server.pem',
                'local_pk' => '/your/path/of/server.key',
                'verify_peer' => false
            )
        );
        $io = new SocketIO(2021, $context);

        $io->on('connection', function ($connection) use ($io) {
            echo "New connection coming\n";
        });

       vd(Worker::runAll());

    }


}



?>
