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
use PAMI\Message\Action\StatusAction;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Event\DialEvent;

class AppAmi1EventTest
{   private $client;

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
        $this->client = new ClientImpl($options);

        $this->client->open();
        $time = time();


        usleep(1000);
        $response = $this->client->send(new StatusAction());
        $originate = new OriginateAction("SIP/{$ext}");
        $originate->setCallerId($callerId);
        $originate->setContext($context);
        $originate->setExtension($callerId);
        $originate->setPriority('1');


        $orgresp = $this->client->send($originate);
        $orgStatus = $orgresp->getKeys();

        var_dump($orgStatus);
        $listener = 'Listener1';
        $this->client->registerEventListener(function ($event) {
        echo 'Event Name: ' . $event . PHP_EOL;
        });

        $this->client->registerEventListener(array($listener, 'OriginateResponse'), function ($event) {
            echo $event instanceof DialEvent;
            echo 'Event Started';
        });

        $this->client->registerEventListener($listener);

        $this->client->process();
        $this->client->close();
        
    }
    
}
$test = new AppAmi1EventTest;
$test->run('204', '1111');
$test->EventTest();
