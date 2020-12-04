<?php
/**
 *
 *
 * Author:  Muminov Umid
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payer;

use yii\httpclient\Client;
use zetsoft\system\kernels\ZFrame;


class OsonOld extends ZFrame
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
    private $token;
    public $account;
    public $merchant_id;
    public $amount;
    public $transaction_id;

    public $method = [
        'check' => 'check',
        'pay' => 'pay',
        'check_status' => 'check_status',
    ];

    private $url = 'https://oson.uz/';

    #endregion


    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client(['base_uri' => $this->url]);
    }

    #endregion
    public function test(){

    }

    public function check()
    {
        $response = $this->client->request($this->methods['post'], "/api/payment/check", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'token' => $this->token,
                ],
                'json' => [
                    'method' => $this->method['check'],
                    'merchant_id' => $this->merchant_id,
                    'account' => $this->account,
                    'amount' => $this->amount,
                    'params' => [

                    ],
                ],
        ]);

        return json_decode($response->getBody());
    }

    public function pay()
    {
        $response = $this->client->request($this->methods['post'], "api/payment/pay", [
            'headers' => [
                'Content-Type' => 'application/json',
                'token' => $this->token,
            ],
            'json' => [
                'method' => $this->method['pay'],
                'merchant_id' => $this->merchant_id,
                'account' => $this->account,
                'amount' => $this->amount,
                'params' => [

                ],
            ],
        ]);

        return json_decode($response->getBody());
    }
    public function checkSatatus()
    {
        $response = $this->client->request($this->methods['post'], "api/payment/pay", [
            'headers' => [
                'Content-Type' => 'application/json',
                'token' => $this->token,
            ],
            'json' => [
                'method' => $this->method['pay'],
                'transaction_id' => $this->transaction_id
            ],
        ]);

        return json_decode($response->getBody());
    }






}
