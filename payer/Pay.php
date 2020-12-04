<?php
/**
 *
 *
 * Author:  Abdurakhmonov Umid
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payer;

use yii\httpclient\Client;
use zetsoft\system\kernels\ZFrame;


class Pay extends ZFrame
{
    #region Vars
    private $client;
    public  $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $id_company = '5f2fe4c73ddb59936a2e76ec';
    private $url = [
        'hostUrl' => "https://checkout.test.paycom.uz/api",

    ];
    private $password = 'qwerty';

    #endregion


    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client();
    }

    #endregion
    public function test(){
        return "test";
    }

}
