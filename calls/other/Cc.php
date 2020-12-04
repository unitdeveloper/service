<?php

namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use zetsoft\system\kernels\ZFrame;


class Cc extends ZFrame
{

    public function call($extension = '', $callerId, $maxRetries = '2',$retryTime ='10', $waitTime = '30' , $context = 'from-internal', $priority = '1'){

        $adapter = new SftpAdapter([
            'host' => '10.10.3.41',
            'port' => 22,
            'username' => 'root',
            'password' => 'Formula1',
            'root' => '/',
            'timeout' => 10,
        ]);

        $filesystem = new Filesystem($adapter);
        $adapter->connect();
        if ($adapter->isConnected()) {
            $fileName = rand(10, 1000) . time();
            $filedest = "/var/spool/asterisk/outgoing/" . $fileName . ".call";
            

            $content =  "Channel: SIP/{$extension}\n";
            $content .= "Callerid: {$callerId}\n";
            $content .= "MaxRetries: {$maxRetries}\n";
            $content .= "RetryTime: {$retryTime}\n";
            $content .= "WaitTime: {$waitTime}\n";
            $content .= "Context: $context\n";
            $content .= "Extension: {$callerId}\n";
            $content .= "Priority: {$priority}\n";
            $filesystem->write($filedest, $content);
            error_reporting(0);
        }else{
            var_dump('Something went wrong');
        }


    }




}

?>

