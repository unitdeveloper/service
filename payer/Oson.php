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

use GuzzleHttp\Client;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Oson extends ZFrame
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
    public $token;
    public $account = "333222444";
    public $merchant_id = 123;
    public $amount = 3000.00;
    public $transaction_id = "87967231";

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

        /*
           public $test = true;
        if ($this->test)
            $this->baseUri = self::baseUri['test'];
        else
            $this->baseUri = self::baseUri['main'];
        */
    }

    #endregion



   /* #region create
    public function create()
    {
        $response = $this->client->request($this->methods['post'], "/api/payment/check", [
            'headers' => [
                'Content-Type' => 'application/json',
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
    #endregion*/



    #region check

    /**
     * @return mixed
     * @author Muminov Umid
     *
     * https://oson.uz/docs/pratakol-merchant-api
     *
     */

    public function checkTest(){
        $token = Az::$app->payer->oson->check();

        vdd($token);
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

    #endregion

    #region check

    /**
     * @return mixed
     * @author Muminov Umid
     *
     * https://oson.uz/docs/pratakol-merchant-api
     *
     */

    public function payTest()
    {
        $token2 = Az::$app->payer->oson->pay();

        vdd($token2);
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

    #endregion

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
