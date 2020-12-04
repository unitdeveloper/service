<?php

namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use zetsoft\models\App\eyuf\db2\Cel;
use zetsoft\system\kernels\ZFrame;


class Adp extends ZFrame
{
    public $filesystem;

    public $extension;

    public function filesystem()
    {

        $adapter = new SftpAdapter([
            'host' => '10.10.3.41',
            'port' => 22,
            'username' => 'root',
            'password' => 'Formula1',
            'root' => '/',
            'timeout' => 10,
        ]);

        $this->filesystem = new Filesystem($adapter);
        $adapter->connect();
        return $this->filesystem;
    }


    public function write($extension, $callerId, $maxRetries = '1', $retryTime = '15', $waitTime = '25', $context = 'from-internal', $priority = '1', $archive = 'yes')
    {

        $fileName = rand(10, 1000) . time();
        $filedest = "/var/spool/asterisk/outgoing/" . $fileName . ".call";

        $content = "Channel: SIP/{$this->extension}\n";
        $content .= "Callerid: {$callerId}\n";
        $content .= "WaitTime: {$waitTime}\n";
        $content .= "Context: {$context}\n";
        $content .= "Extension: {$callerId}\n";
        $content .= "Priority: {$priority}\n";
        $content .= "Archive: {$archive}\n";


        /** @var Cel[] $cels */
        $cels = Cel::find()
            ->where([
                'cid_ani' => 100
            ])
            ->all();

        $this->filesystem()->write($filedest, $content);

    }


    public function count()
    {
        // count files
            $path = '/var/spool/asterisk/outgoing/';
            $contents = $this->filesystem()->listContents($path);
            $count = count($contents);
            return $count;
        // returns number of files
    }


    public function check()
    {
        $database = ['701', '204', '233', '333', '444', '445', '555', '535', '5544', '222', '5441', '645', '4234', '11111', '2222'];

        $dataCount = count($database);
        $k = 0;
        while (true) {
            foreach ($database as $key => $val) {
                $i = $this->count();
                if ($k == $key) {
                    if ($i <= 1) {
                        var_dump('value $k' . $k);
                        var_dump('written' . $i);
                        $this->write($val, '1111');
                        if ($k < $dataCount) {
                            ++$k;
                        } else {
                            $k = 0;
                        }
                    }
                }

            }
        }


    }


}
