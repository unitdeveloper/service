<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\geo;

use yii\httpclient\Client;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;

class sypexGeo extends ZFrame
{
    #region Vars
    private $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    public $url = "http://api.sypexgeo.net/json/";
    private $client;
    #endregion

    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client();
    }
    #endregion


    //http://api.ipinfodb.com/v3/ip-city/?key=228a27f772482b6e2460bdcd81c7137c7c6f2269049b6828e9a84e6ea3b0d1b4&ip=94.158.52.244&format=json
    #region Test
    public function test()
    {
        return $this->getInfo("94.158.52.244");
    }
    #endregion


    /**
     *
     * Function  getInfo
     * @param $ip
     * @return  mixed json
     */
    #region
    public function getInfo($ip)
    {
        $response = $this->client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->url . $ip)
            ->send();
        return $response->data;
    }
    #endregion


}
