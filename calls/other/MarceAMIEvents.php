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
use zetsoft\system\kernels\ZFrame;

class MarceAMIEvents extends ZFrame
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
        'conn' => 10000,
        'read' => 10000
    ];
    public $options;

    public $client;



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
        $this->client->open();
    }

    #endregion

    #region Events

    public function absoluteListener()
    {
        $this->client->registerEventListener(function ($event) {
            var_dump($event);
        });
        while(true) {
            $this->client->process();
            usleep(1000);
        }
    }
    

    #endregion



}
