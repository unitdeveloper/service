<?php

namespace zetsoft\service\calls;
use  PAMI\Message\Action\OriginateAction;
use  PAMI\Message\Action\StatusAction;
use  PAMI\Client\Impl\ClientImpl;
use zetsoft\system\kernels\ZFrame;

class AppAmi extends ZFrame
{
    public const ip = [
        '41' => '10.10.3.41',
        '39' => '10.10.3.39'
    ];
    public $ip = self::ip['10.10.3.31'];

    public function run($ext, $callerId, $context = 'from-internal')
    {
    
        $options = array(
            'host' => '10.10.3.41',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'amiuser',
            'secret' => 'amiuser',
            'connect_timeout' => 100,
            'read_timeout' => 100
        );
        
        $client = new ClientImpl($options);
        $client->open();
        $response = $client->send(new StatusAction());
        $originate = new OriginateAction("SIP/{$ext}");
        $originate->setCallerId($callerId);
        $originate->setContext($context);
        $originate->setExtension($callerId);
        $originate->setPriority('1');
        $orgresp = $client->send($originate);
        $orgStatus = $orgresp->getKeys();
        var_dump($orgStatus);
        $client->close();
    }
}
