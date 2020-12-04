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


use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\QueueAddAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\ShowDialPlanAction;
use PAMI\Message\Action\StatusAction;
use zetsoft\system\kernels\ZFrame;

class Pami extends ZFrame
{
    #region Vars

     public const ip = [
     '41' => '10.10.3.41',
     '30' => '10.10.3.30'
     ];

     public $ip = Self::ip['41'];

     public $scheme = 'tcp://';

     public $port = '5038';

     public $user = [
     'user' => 'amiuser',
     'pass' => 'amiuser'
     ];

     public $timeout = [
     'conn' => 10,
     'read' => 10
     ];

     public $callerId = '1111';

     public $ext = '204';

     public $context = 'from-internal';

     public $priority = '1';



     public $stateInterface = 'StateInterface';

     public $interface = 'Interface';

     public $penalty = '';

     private $options;
     private $client;
     private $response;
     private $originate;
     private $orgresp;
     private $orgStatus;


     private $memberName;
     private $paused;
     private $queue = '202';

    #endregion

    #region Cores

    public function test()
    {

        $this->extension = '300';
        $status = $this->originate();
        vd($status);

    }

    public function init()
    {
        parent::init();

        $this->options = array(
            'host' => $this->ip,
            'scheme' => $this->scheme,
            'port' => $this->port,
            'username' => $this->user['user'],
            'secret' => $this->user['pass'],
            'connect_timeout' => $this->timeout['conn'],
            'read_timeout' => $this->timeout['read']
        );
        $this->client = new ClientImpl($this->options);
        echo "Init" . PHP_EOL;
    }

    #endregion

    #region Actions

    public function originate()
    {
        $this->client->open();

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction("SIP/{$this->ext}");
        $this->originate->setCallerId($this->callerId);
        $this->originate->setContext($this->context);
        $this->originate->setExtension($this->callerId);
        $this->originate->setPriority($this->priority);


        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
        $this->client->close();
        echo 'Process' . PHP_EOL;
    }

    public function showDialPlan($context = false, $ext = false)
    {
        $this->client->open();

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->showDialPlan = new ShowDialPlanAction();

        $this->showDialPlan->setCallerId($this->callerId);
        $this->showDialPlan->setContext($this->context);
        $this->showDialPlan->setExtension($this->callerId);
        $this->showDialPlan->setPriority($this->priority);

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();
        
        if ($context != false) {
            $this->setKey('Context', $this->context);
        }
        if ($ext != false) {
            $this->setKey('Extension', $this->ext);
        }


        $this->client->process();
        $this->client->close();
        echo 'Process' . PHP_EOL;
    }

    public function queues()
    {
        $this->client->open();

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->originate = new OriginateAction("SIP/{$this->ext}");
        $this->originate->setCallerId($this->callerId);
        $this->originate->setContext($this->context);
        $this->originate->setExtension($this->callerId);
        $this->originate->setPriority($this->priority);


        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
        $this->client->close();
    }

    public function queuePause()
    {
        $this->client->open();

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->queueAdd = new QueuePauseAction($interface, $queue = false, $reason = false);


        $this->client->process();
        $this->client->close();
    }

    public function queueAdd()
    {
        $this->client->open();

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());
        $this->queueAdd = new QueueAddAction("1111", "202");

                  //vdd($this->queueAdd());

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
        $this->client->close();

        echo 'Process' . PHP_EOL;
    }


    public function agentlogoff(){
        $this->client->open();

        usleep(1000);
        $this->response = $this->client->send(new StatusAction());

        //agentlogoff->

        $this->orgresp = $this->client->send($this->originate);
        $this->orgStatus = $this->orgresp->getKeys();

        $this->client->process();
        $this->client->close();

    }





    #endregion



}
