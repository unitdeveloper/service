<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\calls;
use PAMI\Message\Action\OriginateAction;
use PAMI\Client\Impl\ClientImpl;
use zetsoft\system\kernels\ZFrame;

class AmiAppMultiple extends ZFrame 


{
   private $client;

   private $time;

    function connect(){
        $options = array(
            'host' => '10.10.3.41',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'amiuser',
            'secret' => 'amiuser',
            'connect_timeout' => 100,
            'read_timeout' => 100
        );
        $this->client = new ClientImpl($options);
        return $this->client;
    }
    public function call($ext, $callerId, $context = 'from-internal')
    {
        sleep(2);
        $originate = new OriginateAction("SIP/{$ext}");
        $originate->setCallerId($callerId);
        $originate->setContext($context);
        $originate->setExtension($callerId);
        $originate->setPriority('1');
        $orgresp = $this->client->send($originate);
        $orgStatus = $orgresp->getKeys();
        var_dump($orgStatus);
        $this->client->process();
    }
    function disconnect()
    {
        $this->client->close();
    }
}
