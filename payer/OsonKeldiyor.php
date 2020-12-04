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
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;


class OsonKeldiyor extends ZFrame
{


    public $amount = 3000.00;
    public $account = 333222444;
    public $merchant_id = 123;



    public const baseUri = [
        'test' => 'https://checkout.test.paycom.uz',
        'main' => 'https://checkout.paycom.uz',
    ];

    #endregion

    public function init()
    {
        parent::init();

        if ($this->test)
            $this->baseUri = self::baseUri['test'];
        else
            $this->baseUri = self::baseUri['main'];
    }





    #region Create

    public function checkTest()
    {
        /*$this->number;
        $this->expire;*/
        //$this->amount = '3000.00';

        $token = Az::$app->payer->OsonKeldiyor->create();
        vdd($token);

    }

    /**
     *
     * Function  create
     * Methods for the client side of the merchant application:
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     *
     *
     */
    public function create()
    {
        $method = 'check';
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [

            'headers' => [
                'X-Auth' => $this->auth2,
            ],

            'json' => [
                "method" => $method,
                "params" => [

                    "amount" => $this->amount,
                    "account" => $this->account,
                    "merchant_id" => $this->merchant_id
                ],
            ]
        ]);

        $body = $response->getBody();
        //$content = $body->getContents();
        $decode = json_decode($body);
        $this->token = $decode->result->card->token;
        return $decode->result->card;

    }

    #endregion







}
