<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payer;

use GuzzleHttp\Client;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function Safe\json_decode;

class PaySys extends ZFrame
{

    public $client;
    public $timestamp;
    private $service_id = '70f2638b6decc63dc3556f0c99f1505e';
    public $hash;
    public $card_number;
    public $card_expire;
    public $amount;
    public $product;
    public $id;
    public $bank_transaction_id;
    public $confirmation_code;

    /*
     *
     * https://agr.uz/gateway/
    Авторизация и безопасность
    Для
     * */

    const URL = 'https://agr.uz';
    const VENDOR_ID = 101443;
    const SECRET_KEY = 'L3zkQBNUF1WU7wAh8FHcTojSMPCIlVzA';

    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client(['base_url' => self::URL]);
    }

    #endregion


    #region preparePayment

    public function preparePayment()
    {

        $method = 'pam.prepare_payment';
//        $client = new Client(['base_uri' => 'https://agr.uz/sandbox/']);
        $client = new Client(['base_uri' => 'https://agr.uz/gateway/']);
        $Auth = $this->generateAuth();
        $response = $client->request('POST', '', [

            'headers' => [
                'Auth' => $Auth,
            ],

            'json' => [
                "method" => $method,
                "params" => [
                    'card_number' => $this->card_number,
                    'card_expire' => $this->card_expire,
                    'amount' => $this->amount,
                    'vendor_id' => self::VENDOR_ID,
                    'product' => $this->product
                ],
                'id' => $this->id
            ]
        ]);
        $body = $response->getBody();
        $decode = \GuzzleHttp\json_decode($body);
        if (isset($decode->error->code)) {
            return $decode->error->message;
        }
        //vdd($decode->error->code);
//        return [
//            'Auth'=> $Auth,
//            'card_number' => $this->card_number,
//            'card_expire' => $this->card_expire,
//            'amount' => $this->amount,
//            'vendor_id' => self::VENDOR_ID,
//            'product' => $this->product
//            ];


    }

    public function testPreparePayment()
    {
        $this->card_number = '8600********1932';     // type your card here
        $this->card_expire = '****';            // your card expire data, type here
        $this->amount = 100000;
        $this->product = 'bahodirproduct27';
        $this->id = 1;
        /*$this->card_number = '8601312960622192';
        $this->card_expire = '2302';
        $this->amount = 100000;
        $this->product = 'maximus';
        $this->id = 207;*/

        Az::$app->payer->paysys->preparePayment();
    }

    #endregion

    #region confirmPayment

    public function confirmPayment()
    {
        $method = 'pam.confirm_payment';
        $client = new Client(['base_uri' => 'https://agr.uz/gateway/']);
        $Auth = $this->generateAuth();
        $response = $client->request('POST', '', [

            'headers' => [
                'Auth' => $Auth,
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    'bank_transaction_id' => $this->bank_transaction_id,
                    'confirmation_code' => $this->confirmation_code,
                    'amount' => $this->amount,
                    'vendor_id' => self::VENDOR_ID,
                    'product' => $this->product
                ],
                'id' => $this->id
            ]
        ]);
        $body = $response->getBody();
        $decode = \GuzzleHttp\json_decode($body);
        if (isset($decode->error->code)) {
            return $decode->error->message;
        }
        //return json_decode($response->getBody());
    }

    public function testconfirmPayment()
    {
        $this->amount = 100000;
        $this->product = 'bahodirproduct27';
        $this->id = 1;

        $this->confirmPayment();
    }

    #endregion

    public function generateAuth()
    {
        $time = time();
        return $this->service_id. '-'.sha1(self::SECRET_KEY.$time). '-'. $time;
    }
    public function generateHash()
    {

        return $this->hash = sha1(self::SECRET_KEY.time());
    }
    public function generateTimestamp()
    {
        return $this->timestamp = intval(microtime(true) * 1000);
    }

    public function generateSignString($merchant_trans_id, $sign_time)
    {
        return sha1(self::SECRET_KEY.$merchant_trans_id.$sign_time);
    }



}
