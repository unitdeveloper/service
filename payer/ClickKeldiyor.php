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
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;


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

    public const baseUri = [
        'urls' => [
            'createInvoice' => 'https://api.click.uz/v2/merchant/invoice/create',
            'invoiceStatus' => 'https://api.click.uz/v2/merchant/invoice/status/:service_id/:invoice_id',
            'paymentStatus' => 'https://api.click.uz/v2/merchant/payment/status/:service_id/:payment_id',
            'paymentStatusMerchant' => 'https://api.click.uz/v2/merchant/payment/status_by_mti/:service_id/:merchant_trans_id',
            'paymentReversal' => 'https://api.click.uz/v2/merchant/payment/reversal/:service_id/:payment:id',
            'createCardToken' => 'https://api.click.uz/v2/merchant/card_token/request',
            'verifyCardToken' => 'https://api.click.uz/v2/merchant/card_token/verify',
            'paymentWithToken' => 'https://api.click.uz/v2/merchant/card_token/payment',
            'deleteCardToken' => 'https://api.click.uz/v2/merchant/card_token/:service_id/:card_token',
        ],
        'test' => 'https://checkout.test.paycom.uz',
        'main' => 'https://checkout.paycom.uz',
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



//    public function Create_invoice()
//    {
//
//        $timestamp = time();
//
//        $digest = sha1($timestamp . $this->SECRET_KEY);
//        $client = new Client(['base_uri' => $this->urls['createInvoice']]);
//        $response = $client->request('POST', '', [
//            'headers' =>
//                [
//                    'Accept' => 'application/json',
//                   'Content-Type' => 'application/json',
//                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
//                ],
//            'params' => [
//                'service_id' => $this->SERVICE_ID,
//                'amount' => $this->amount,
//                'phone_number' => '+998907435206',
//                'merchant_trans_id' => $this->merchant_trans_id
//            ]
//        ]);
//
//        return ($this->invoice_id);
//    }

    public function Create_invoice(){
        $method = 'create';
        $client = new Client(['base_uri' => $this->urls['createInvoice']]);

        $response = $client->request('POST', '',  [

            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Auth' => '123:356a192b7913b04c54574d18c28d46e6395428ab:1519051543',

            'json' => [
                "method" => $method,
                "params" => [
                    'service_id' => $this->SERVICE_ID,
               'amount' => $this->amount,
               'phone_number' => '+998907435206',
               'merchant_trans_id' => $this->merchant_trans_id,
                ],
            ]
        ]]);

        $body = $response->getBody();
        //$content = $body->getContents();
        $decode = json_decode($body);
        $this->token = $decode->result->card->token;
        return $decode->result->card;
    }

//    public function invoiceStatusCheck()
//    {
//        $this->invoice_id = $this->createInvoice();
//        $timestamp = time();
//        $digest = sha1($timestamp . $this->SECRET_KEY);
//        $client = new Client(['base_uri' => 'https://api.click.uz/']);
//        $response = $client->request('GET ', '/v2/merchant/invoice/status/'.$this->SERVICE_ID.'/'.$this->invoice_id, [
//            'headers' =>
//                [
//                    'Accept' => 'application/json',
//                    'Content-Type' => 'application/json',
//                    'Auth' => $this->MERCHANT_USER_ID . ":$digest:" . $timestamp,
//                ]
//        ]);
//        $body = $response->getBody();
//        $result = json_decode($body);
//        vdd($result);
//        return $result;
//    }

}

