<?php
namespace zetsoft\service\calls;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use zetsoft\system\kernels\ZFrame;


class Ad extends ZFrame
{

    public function run($extension, $callerId, $maxRetries = '1',$retryTime ='15', $waitTime = '10' , $context = 'from-internal', $priority = '1', $alwaysDelete = 'Yes'){

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

        $fileName = rand(10, 1000) . time();
        $filedest = "/var/spool/asterisk/outgoing/" . $fileName . ".call";


        $content =  "Channel: SIP/{$extension}\n";
        $content .= "Callerid: {$callerId}\n";
        $content .= "MaxRetries: {$maxRetries}\n";
        $content .= "RetryTime: {$retryTime}\n";
        $content .= "WaitTime: {$waitTime}\n";
        $content .= "Context: {$context}\n";
        $content .= "Extension: {$callerId}\n";
        $content .= "Priority: {$priority}\n";
        $content .= "AlwaysDelete: {$alwaysDelete}\n";
        $filesystem->write($filedest, $content);

        }





}




