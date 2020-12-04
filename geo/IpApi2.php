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

class IpApi2 extends ZFrame
{

    #region Vars
    private $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $format = [
        'json' => "json",
        'jsonp'=>"jsonp",
        'xml'=>"xml",
        'csv'=>"csv",
        'yaml'=>"yaml",


    ];

    private $apiKey = "";

    public $url = "http://ipapi.co/";

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
            ->setUrl($this->url . $ip . '/' . $this->format['json'])
            ->send();
        return $response->data;
    }


    #endregion

}
