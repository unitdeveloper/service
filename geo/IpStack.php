<?php

/**
 *
 *
 * Author:  Shaxzoda Khomidova
 *
 */

namespace zetsoft\service\geo;


use yii\httpclient\Client;
use zetsoft\system\kernels\ZFrame;

class IpStack extends ZFrame
{

    #region Vars
    private $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $apiKey = "6d3f46050b5898a98efa55765f7e7a14";

    public $url = "http://api.ipstack.com/";

    private $client;
    #endregion

    #region Core

    public function init()
    {
        parent::init();
        $this->client = new Client();
    }

    #endregion

    #region Test
    public function test()
    {
        return $this->getInfo("94.158.52.244");
    }
    #endregion

    #region Event

    public function getInfo($ip){
        $response = $this->client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->url . $ip . '?access_key=' . $this->apiKey)
            ->send();
        return $response->data;
    }


    #endregion

}
