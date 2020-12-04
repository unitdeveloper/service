<?php

/**
 * Author:  jamshid Ismoilov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */


namespace zetsoft\service\geo;

use yii\httpclient\Client;
use zetsoft\dbitem\geo\IPItem;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ExtremeIp extends ZFrame
{
    #region Vars
    private $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    public $url = "http://extreme-ip-lookup.com/json/";
    private $client;
    #endregion

    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client();
    }
    #endregion


    //
    #region Test
    public function test()
    {
        $this->getInfoTest();
    }
    #endregion

    /**
     *
     * Function  getInfo
     * @param $ip
     * @return  mixed array
     */
    #region

    public function getInfoTest()
    {
        $res = $this->getInfo('94.158.52.244');
        vdd($res);
    }


    public function getInfo($ip)
    {
        $response = $this->client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->url . $ip)
            ->send();

        $data = $response->data;

        $item = new IPItem();

        $item->businessName = $data['businessName'];
        return $item;
    }

    #endregion

}
