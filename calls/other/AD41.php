<?php

namespace zetsoft\service\calls;
use  PAMI\Message\Action\OriginateAction;
use  PAMI\Message\Action\StatusAction;
use  PAMI\Client\Impl\ClientImpl;





$options = array(
    'host' => '10.10.3.41',
    'scheme' => 'tcp://',
    'port' => 5038,
    'username' => 'amiuser',
    'secret' => 'amiuser',
    'connect_timeout' => 100
);
$client = new ClientImpl($options);


$client->open();
$time = time();



    usleep(1000);
    $response = $client->send(new StatusAction());
    $originate = new OriginateAction('SIP/204');
    $originate->setCallerId('1111');
    $originate->setContext('from-internal');
    $originate->setExtension('1111');
    $originate->setPriority('1');
    //$originate->setApplication('Queue');
    //$originate->setData('support');


    $orgresp = $client->send($originate);
    $orgStatus = $orgresp->getKeys();

    var_dump($orgStatus);

    $client->process();
$client->close();










