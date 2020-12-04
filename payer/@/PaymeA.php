<?php
/**
 *
 *
 * Author:  Abdurakhmonov Umid
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payme;

use yii\httpclient\Client;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;


class PaymeA extends ZFrame
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
    
    #endregion
    public function test(){
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://checkout.test.paycom.uz/api']);
        $res = $client->request('GET', '/redirect/3', ['allow_redirects' => false]);
        echo $res->getStatusCode();
        return $client;
    }





}
