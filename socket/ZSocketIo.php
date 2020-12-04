<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 29.06.2017 19:06
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\socket;
require Root . '/vendori/netter/ALL/vendor/autoload.php';

use Workerman\Worker;
use zetsoft\apisys\apps\rest;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class ZSocketIo extends ZFrame
{
    public function run()
    {
        $this->cors();
        
        $io = new \PHPSocketIO\SocketIO($this->bootEnv('socketPort'));

        $files = $this->scan();

        foreach ($files as $key => $methods) {
            $io->of("/$key")->on('connection', function ($socket) use ($io, $methods) {
                foreach ($methods as $list) {
                    $method = str_replace('.php', '', bname($list));
                    $socket->on("$method", require $list);
                }
            });
        }
        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }

    private function cors()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            /*header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");*/
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
// Access-Control headers are received during OPTIONS requests
        if (ZArrayHelper::getValue($_SERVER, 'REQUEST_METHOD') === 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                /*header("Access-Control-Allow-Methods: GET, POST, OPTIONS, POST, PUT");*/
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT, PATCH");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    private function scan()
    {
        $folders = ZFileHelper::scanFolder(Root . '/socket');
        $files = [];
        foreach ($folders as $folder) {
            $name = bname($folder);
            $files[$name] = ZFileHelper::scanFilesPHP(Root . '/socket/' . $name);
        }

        return $files;
    }
}
