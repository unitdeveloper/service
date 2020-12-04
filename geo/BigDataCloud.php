<?php

/**
 *
 *
 * Author:  Axrorbek Nisonboyev
 *
 */

namespace zetsoft\service\geo;


use Filterus\Filters\URL;
use yii\httpclient\Client;
use Zend\Validator\Ip;
use zetsoft\dbitem\geo\IPItem;
use zetsoft\system\kernels\ZFrame;

class BigDataCloud extends ZFrame
{

    #region Vars
    private $methods = [
        'post' => 'POST',
        'get' => 'GET',
        'delete' => 'DELETE',
        'put' => 'PUT',
        'patch' => 'PATCH'
    ];

    public $url = "http://api.bigdatacloud.net/data/ip-geolocation-full?ip=";
    private $client;

    private $apiKey = "dbac9eccd48c4230a03315f243b790ff";


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

    public function getInfo($ip)
    {
        $response = $this->client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->url . $ip . '&key=' . $this->apiKey)
            ->send();


        $data = $response->data;

        $item = new IPItem();

        $item->businessName = $data['business'];
        $item->businessWebsite = $data['site'];
        $item->city = $data['city'];
        return $item;
    }

    #endregion

}
