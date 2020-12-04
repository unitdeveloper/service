<?php

/**
 *
 *
 * Author:  Axrorbek Nisonboyev
 *
 */

namespace zetsoft\service\geo;


use yii\httpclient\Client;
use zetsoft\system\kernels\ZFrame;

class IpGeoLocation extends ZFrame
{

    #region Vars
    private $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $apiKey = "87ab2b56ef544353a4b3c2fdbd35debe";

    public $url = "https://api.ipgeolocation.io/ipgeo?apiKey=87ab2b56ef544353a4b3c2fdbd35debe&ip=";

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
        $res = $this->getInfo("94.158.52.15");
        vdd($res);
    }
    #endregion

    #region Event

    public function getInfo($ip){
        $response = $this->client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->url . $ip)
            ->send();
        return $response->data;
    }


    #endregion

}
