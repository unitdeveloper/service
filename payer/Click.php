<?php
/**
 *
 *
 * Author:  Xolmat Ravshanov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payer;

use GuzzleHttp\Client;
use service\payer\Click\click\models\Payments;
use Paycom\Application;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Click extends ZFrame
{
    #region Vars
    private $client;
    private $params;
    public $model;
    public $response;
    public $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $url =
        "https://my.click.uz/services/pay?service_id={service_id}&merchant_id={merchant_id}&amount={amount}&return_url={return_url}&card_type={card_type}";

    /**
     * @var string[] Merchant API urls
     */
    private $urls = [
        'createInvoice' => 'https://api.click.uz/v2/merchant/invoice/create',
        'invoiceStatus' => 'https://api.click.uz/v2/merchant/invoice/status/:service_id/:invoice_id',
        'paymentStatus' => 'https://api.click.uz/v2/merchant/payment/status/:service_id/:payment_id',
        'paymentStatusMerchant' => 'https://api.click.uz/v2/merchant/payment/status_by_mti/:service_id/:merchant_trans_id',
        'paymentReversal' => 'https://api.click.uz/v2/merchant/payment/reversal/:service_id/:payment:id',
        'createCardToken' => 'https://api.click.uz/v2/merchant/card_token/request',
        'verifyCardToken' => 'https://api.click.uz/v2/merchant/card_token/verify',
        'paymentWithToken' => 'https://api.click.uz/v2/merchant/card_token/payment',
        'deleteCardToken' => 'https://api.click.uz/v2/merchant/card_token/:service_id/:card_token',
    ];
    //&transaction_param={transaction_param}
    private $password = 'qwerty';

    private $SERVICE_ID = 16409;
    private $MERCHANT_ID = 11864;
    private $MERCHANT_USER_ID = '17334';
    private $SECRET_KEY = 'ZA2PTQi6b1qS';

    private $token = '';
    private $amount = 10000;
    private $phone = '998971412636';
    private $invoice_id = '4222';
    private $card_number = '8600140224387663';
    private $expire_date = '1024';
    private $temporary = 1;
    private $sms_code = '';
    private $card_token = '';
    private $payment_id = 1121;
    private $merchant_trans_id = 11213;

    #endregion

    //8600020393138354
    //0224
    //+998974012297

    #region Cores
//    public function init()
//    {
//        //parent::init();
//        $this->token = $this->getToken();
//        $this->model = new \zetsoft\service\payer\Click\click\models\Payments();
//    }
    #endregion

    public function getTokenTest(){

    }

    public function getToken()
    {
        $clientId = '';
        $secret = '';
        $client = new Client();

        $response = $client->request('POST', $this->urls['createInvoice'], [
                'form_params' => [
                    'grant_type'=>''
                ],
            ]
        );

        $response->getBody();

        return $response;
    }



    public function create_invoice(){
        $this->model->create_invoice([
            'token' => $this->token,
            'phone_number' => $this->phone,
        ]);
    }

    public function check_invoice(){
        $this->model->check_invoice([
            'token' => $this->token,
            'invoice_id' => $this->invoice_id
        ]);
    }

    public function create_card_token(){
        $this->model->create_card_token([
            'token' => $this->token,
            'card_number' => $this->card_number,
            'expire_date' => $this->expire_date,
            'temporary' => $this->temporary,
        ]);
    }

    public function verify_card_token(){
        $this->model->verify_card_token([
            'token' => $this->token,
            'sms_code' => $this->sms_code,
        ]);
    }

    public function payment_with_card_token(){
        $this->model->payment_with_card_token([
            'token' => $this->token,
            'card_token' => $this->card_token,
        ]);
    }

    public function delete_card_token(){
        $this->model->delete_card_token([
            'token' => $this->token,
            'card_token' => $this->card_token,
        ]);
    }

    public function check_payment(){
        $this->model->check_payment([
            'token' => $this->token,
            'payment_id' => $this->payment_id,
        ]);
    }

    public function merchant_trans_id(){
        $this->model->merchant_trans_id([
            'token' => $this->token,
            'merchant_trans_id' => $this->merchant_trans_id,
        ]);
    }

    public function cancel(){

        $this->model->cancel([
            'token' => $this->token,
            'payment_id' => $this->payment_id,
        ]);
    }

    public function test()
    {
        $application = new Application([
            'type' => 'json',
            'model' => $this->model,
            'configs' => [
                'click' => [
                    'merchant_id' => 'YOUR MERCHANT ID',
                    'service_id' => 'YOUR SERVICE ID',
                    'user_id' => 'YOUR MERCHANT USER ID',
                    'secret_key' => 'SECRET KEY'
                ]
            ]
        ]);
        $application->run();

        //$this->auth();
    }

    public function send()
    {

        //"https://my.click.uz/services/pay?service_id={service_id}&merchant_id={merchant_id}&amount={amount}&transaction_param={transaction_param}&return_url={return_url}&card_type={card_type}";

        $this->url = strtr($this->url, [

            '{service_id}' => $this->SERVICE_ID,
            '{merchant_id}' => $this->MERCHANT_ID,
            '{amount}' => '1000',
            '{return_url}' => '/',
            '{card_type}' => 'uzcard',

        ]);

        $res = $this->client->request('GET', $this->url, [

        ]);

        vdd($res->getBody());

    }

    public function auth()
    {
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);

        $res = $this->client->request('POST', $this->urls['create'], [
            'headers' =>
                [
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],

        ]);
        vdd($res->getBody());
        return $res;
    }

    public function createInvoice()
    {
        $timestamp = time();

        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => $this->urls['createInvoice']]);
        $response = $client->request('POST', '', [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ],
            'json' =>
                [
                    'service_id' => $this->SERVICE_ID,
                    'amount' => $this->amount,
                    'phone_number' => '+998907435206',
                    'merchant_trans_id' => $this->merchant_trans_id
                ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);
        return $result->invoice_id;
    }

    public function invoiceStatusCheck()
    {
        $this->invoice_id = $this->createInvoice();
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('GET ', '/v2/merchant/invoice/status/'.$this->SERVICE_ID.'/'.$this->invoice_id, [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result;
    }

    public function paymentStatusCheck()
    {
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('GET ', '/v2/merchant/payment/status/'.$this->SERVICE_ID.'/'.$this->payment_id, [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result;
    }

    public function paymentStatusCheckByMerchant()
    {
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('GET ', '/v2/merchant/payment/status_by_mti/'.$this->SERVICE_ID.'/'.$this->merchant_trans_id, [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result;
    }

    public function paymentReversal()
    {
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('DELETE ', '/v2/merchant/payment/reversal/'.$this->SERVICE_ID.'/'.$this->payment_id, [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result;
    }

    public function createCardTokenTest()
    {

        vdd(Az::$app->payer->click->createCardToken());
    }

    public function createCardToken()
    {
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('POST', '/v2/merchant/card_token/request', [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
            'json' => [
                'service_id' => $this->SERVICE_ID,
                'card_number' => $this->card_number,
                'expire_date' => $this->expire_date,
                'temporary' => $this->temporary
            ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        return $result->card_token;
    }

    public function verifyCardToken()
    {
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('POST', '/v2/merchant/card_token/verify', [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ],
            'json' => [
                'service_id' => $this->SERVICE_ID,
                'card_token' => 'A9832D48-8509-4F4C-B264-6670D020766B',
                'sms_code' => 64537
            ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result;
    }

    public function paymentWithToken()
    {
        $timestamp = time();

        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('POST', '/v2/merchant/card_token/payment', [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ],
            'json' => [
                'service_id' => $this->SERVICE_ID,
                'card_token' => $this->card_token,
                'amount' => $this->amount,
                'transaction_parameter' => $this->merchant_trans_id,
            ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result->invoice_id;
    }

    public function deleteCardToken()
    {
        $timestamp = time();
        $digest = sha1($timestamp . $this->SECRET_KEY);
        $client = new Client(['base_uri' => 'https://api.click.uz/']);
        $response = $client->request('DELETE ', '/v2/merchant/card_token/'.$this->SERVICE_ID.'/'.$this->card_token, [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
                ]
        ]);
        $body = $response->getBody();
        $result = json_decode($body);
        vdd($result);
        return $result;
    }

}

