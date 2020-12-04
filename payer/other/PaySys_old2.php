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
use zetsoft\system\kernels\ZFrame;

class PaySysOld2 extends ZFrame
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

    public function test()
    {
        $this->testPreparePayment();

    }

    #endregion


    #region preparePayment

    public function preparePayment()
    {

        $method = 'pam.prepare_payment';
        $client = new Client(['base_uri' => 'https://agr.uz/gateway/']);
        $response = $client->request('POST', '', [

            'headers' => [
                'Auth' => $this->generateAuth(),
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
        //$content = $body->getContents();
        $decode = json_decode($body);
        vdd($decode);
//        $this->token = $decode->result->card->token;
        return $decode->result->card;


    }

    public function testPreparePayment()
    {
        $this->card_number = '8600020393138354';
        $this->card_expire = '0224';
        $this->amount = 100000;
        $this->product = 'product';
        $this->id = 22;

        vdd($this->preparePayment());
    }

    #endregion

    public function confirmPayment()
    {
        $method = 'pam.confirm_payment';
        $response = $this->client->request('POST', '/gateway', [
            'headers' => [
                'Auth' => $this->generateAuth(),

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

        return json_decode($response->getBody());
    }

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

}
