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
use PAMI\Message\Action\StatusAction;
use zetsoft\system\kernels\ZFrame;

class ReactAmi extends ZFrame
{
    #region Vars

    public const ip = [
        '41' => '10.10.3.41',
        '31' => '10.10.3.31',
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
        'conn' => 100,
        'read' => 100
    ];

    public $callerId = '202';

    public $ext = '204';

    public $context = 'from-internal';

    public $priority = '1';

    private $options;
    private $client;
    private $response;
    private $originate;
    private $orgresp;
    private $orgStatus;

    #endregion

    #region Cores

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

    }


    public function agentlogoff()
    {
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
