<?php
/**
 *
 *
 * Author: Xolmat Ravshanov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\payer;


use GuzzleHttp\Client;
use zetsoft\system\kernels\ZFrame;

class Paysysnew extends ZFrame
{
    #region Vars

    private $client;
    public $response;
    private $username = 'zetsoft';
    private $auth;
    private $password = 'zetsoft0202';


    public $vendor_id = 101443;  //Идентификатор сайта в системе
    public $vendor_trans_id;
    public $status;
    public $merchant_trans_id;  //Уникальный идентификатор заказа в системе Мерчанта.
    public $merchant_trans_amount; //Сумма платежа
    public $merchant_currency;  //Валюта покупки.
    public $merchant_trans_note; //Текстовый комментарий к проведенной операции
    public $sign_time;
    public $sign_string;    //Подпись запроса, cм. Правила формирования подписи запроса
    public $merchant_trans_data; //Детали платежа для Мерчанта. Возвращается Мерчанту
    public $merchant_trans_return_url; //Ссылка для возврата, cм. Возврат пользователя с Host-терминала
    public $merchant_card_number; //Номер карты плательщика.
    public $merchant_card_expire; //Дата окончания действия карты.
    public $payment_name;
    public $payment_id;
    public $agr_trans_id;
    public $verification_code;


    public $environment = [
        'live' => 'live',
        'sandbox' => 'sandbox'
    ];
    private $url = 'https://agr.uz/';
    private $SECRET_KEY = 'PrEe1zL-IY1E4YvdCkawZ0rt61dBPayD';
    public $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    #endregion


    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client(['base_uri' => $this->url]);
        $this->auth = ['auth' => [$this->username, $this->password]];

    }

    #endregion

    #region test
    public function test()
    {
        $this->testGetInfoMerchant();
    }

    #endregion

    #region paymentProcess

    /**
     * Function  paymentProcessing
     * @param $this ->vendor_id
     * @param $this ->sign_time
     * @param $this ->merchant_trans_id
     * @param $this ->merchant_trans_amount
     * @param $this ->merchant_card_number
     * @param $this ->merchant_card_expire
     * @param $this ->merchant_currency
     * @param $this ->merchant_trans_note
     * @return mixed
     */


    public function paymentPreparation()
    {
        $response = $this->client->request($this->methods['post'], "payment_gateway_api/preparation_payment", [
            'json' => [
                'VENDOR_ID' => $this->vendor_id,
                'MERCHANT_TRANS_ID' => $this->merchant_trans_id,
                'MERCHANT_TRANS_AMOUNT' => $this->merchant_trans_amount,
                'MERCHANT_CARD_NUMBER' => $this->merchant_card_number,
                'MERCHANT_CARD_EXPIRE' => $this->merchant_card_expire,
                'MERCHANT_CURRENCY' => $this->merchant_currency,
                'MERCHANT_TRANS_NOTE' => $this->merchant_trans_note,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->vendor_id. $this->merchant_trans_id. $this->merchant_trans_amount. $this->merchant_card_number. $this->merchant_card_expire. $this->merchant_currency. $this->sign_time),

            ]
        ]);

        return json_decode($response->getBody());
    }

    #region testPaymentProcessing
    public function testPaymentPreparation()
    {
       $this->merchant_trans_id = "AB12";
       $this->merchant_trans_amount = 1000;
       $this->merchant_card_number = "8600140224387663";
       $this->merchant_card_expire = "0";
       $this->merchant_currency = "sum";
       $this->merchant_trans_note = "salom";
       vd($this->generateSignTime());
       vd(md5($this->SECRET_KEY. $this->vendor_id. $this->merchant_trans_id. $this->merchant_trans_amount. $this->merchant_card_number. $this->merchant_card_expire. $this->merchant_currency. $this->sign_time));
       $a = $this->paymentPreparation();
       vdd($a);
    }

    #endregion

    #endregion


    #region confirmationPayment
    /**
     * Function  confirmationPayment
     * @param $this ->agr_trans_id
     * @param $this ->verification_code
     * @param $this ->sign_time
     * @return mixed
     */

    public function confirmationPayment()
    {
        $response = $this->client->request($this->methods['post'], "payment_gateway_api/confirm_payment", [
            'json' => [
                'AGR_TRANS_ID' => $this->agr_trans_id,
                'VERIFICATION_CODE' => $this->verification_code,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->agr_trans_id.$this->verification_code. $this->sign_time)
            ]
        ]);

        return json_decode($response->getBody());
    }

    public function testConfirmationPayment()
    {
        $this->agr_trans_id = 346272;
        $this->verification_code = 456231;
        vd($this->generateSignTime());
        vd(md5($this->SECRET_KEY. $this->agr_trans_id.$this->verification_code. $this->sign_time));
        $a = $this->confirmationPayment();
        vdd($a);
    }
    #endregion




    public function webPayment()
    {

        $response = $this->client->request($this->methods['post'], "sandbox", [
            'json' => [
                'VENDOR_ID' => $this->vendor_id,
                'MERCHANT_TRANS_ID' => $this->merchant_trans_id,
                'MERCHANT_TRANS_AMOUNT' => $this->merchant_trans_amount,
                'MERCHANT_CURRENCY' => $this->merchant_currency,
                'MERCHANT_TRANS_NOTE' => $this->merchant_trans_note,
                'MERCHANT_TRANS_DATA' => $this->merchant_trans_data,
                'MERCHANT_TRANS_RETURN_URL' => $this->merchant_trans_return_url,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->vendor_id. $this->merchant_trans_id. $this->merchant_trans_amount. $this->merchant_currency. $this->sign_time),

            ]
        ]);

        return json_decode($response->getBody());
    }

    #region getInfoPayment

    /**
     * Function  getInfoPayment
     * @param $this ->merchant_trans_id
     * @param $this ->sign_time
     * @return mixed
     */


    public function getInfoPayment()
    {
        $response = $this->client->request($this->methods['post'],'sandbox', [
            'json' => [
                'MERCHANT_TRANS_ID' => $this->merchant_trans_id,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->merchant_trans_id. $this->sign_time)
            ]
        ]);

        return json_decode($response->getBody());
    }

    public function testGetInfoPayment()
    {
        $this->merchant_trans_id = 'Ad1232';
        $a = $this->getInfoPayment();
        vdd($a);
    }


    #endregion

    #region receivingPayment

    /**
     * Function  receivingPayment
     * @param $this ->environment
     * @param $this ->vendor_id
     * @param $this ->payment_id
     * @param $this ->payment_name
     * @param $this ->agr_trans_id
     * @param $this ->merchant_trans_id
     * @param $this ->merchant_trans_amount
     * @param $this ->merchant_trans_data
     * @param $this ->sign_time
     * @return mixed
     */

    public function receivingPayment()
    {
        $response = $this->client->request($this->methods['post'], "sandbox", [
            'json' => [
                'ENVIRONMENT' => $this->environment['sandbox'],
                'VENDOR_ID' => $this->vendor_id,
                'PAYMENT_ID' => $this->payment_id,
                'PAYMENT_NAME' => $this->payment_name,
                'AGR_TRANS_ID' => $this->agr_trans_id,
                'MERCHANT_TRANS_ID' => $this->merchant_trans_id,
                'MERCHANT_TRANS_AMOUNT' => $this->merchant_trans_amount,
                'MERCHANT_TRANS_DATA' => $this->merchant_trans_data,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY .$this->agr_trans_id . $this->vendor_id.$this->payment_id. $this->payment_name. $this->merchant_trans_id. $this->merchant_trans_amount. $this->environment['live']. $this->sign_time),
            ]
        ]);

        return $response;
    }

    public function testReceivingPayment()
    {
        $this->payment_id = 1;
        $this->payment_name = 'click';
        $this->agr_trans_id = 1503639319870;
        $this->merchant_trans_id = "7";
        $this->merchant_trans_amount = 1000;
        $this->merchant_trans_data = "eyJwYXJhbV9rZXlfMSI6InBhcmFtX3ZhbHVlXzEiLCJwYXJhbV9rZXlfMiI6InBhcmFtX3ZhbHVlXzIif
Q==";

        $a = $this->receivingPayment();
        vdd($a);
    }

    #endregion

    #region getInfoMerchant

    /**
     * Function  getInfoMerchant
     * @param $this ->vendor_id
     * @param $this ->sign_time
     * @param $this ->expireDate
     * @return mixed
     */
    public function getInfoMerchant()
    {

        $response = $this->client->request($this->methods['post'], 'payment_gateway_api/get_vendor', [
            'json' => [
                'VENDOR_ID' => $this->vendor_id,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->vendor_id. $this->sign_time)
            ]
        ]);

        return json_decode($response->getBody());
    }


    public function testGetInfoMerchant()
    {
        $a = $this->getInfoMerchant();
        vdd($a);
    }

    #endregion



    #region paymentNotify


    /**
     * Function  paymentNotify
     * @param $this ->agr_trans_id
     * @param $this ->vendor_trans_id
     * @param $this ->status
     * @param $this ->sign_time
     * @return mixed
     */
    public function paymentNotify()
    {
        $response = $this->client->request($this->methods['post'], 'pay' , [
            'json' => [
                'AGR_TRANS_ID' => $this->agr_trans_id,
                'VENDOR_TRANS_ID' => $this->vendor_trans_id,
                'STATUS' => $this->status,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->agr_trans_id. $this->vendor_trans_id. $this->status. $this->sign_time)
            ]
        ]);

        return json_decode($response->getBody());
    }

    public function testPaymentNotify()
    {
        $this->agr_trans_id = 1503642925905;
        $this->vendor_trans_id = 1503642925906;
        $a = $this->paymentNotify();
        vdd($a);
    }
    #endregion

    #region checkPaymentCancellation

    /**
     * Function  checkPaymentCancellation
     * @param $this ->agr_trans_id
     * @param $this ->vendor_trans_id
     * @param $this ->sign_time
     * @return mixed
     */

    public function checkPaymentCancellation()
    {
        $response = $this->client->request($this->methods['post'], 'pay' , [
            'json' => [
                'AGR_TRANS_ID' => $this->agr_trans_id,
                'VENDOR_TRANS_ID' => $this->vendor_trans_id,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->agr_trans_id. $this->vendor_trans_id. $this->sign_time)
            ]
        ]);

        return json_decode($response->getBody());
    }

    public function testCheckPaymentCancellation()
    {
        $this->agr_trans_id = 1503642925905;
        $this->vendor_trans_id = 1503642925906;
        vdd($this->checkPaymentCancellation());
    }

    #endregion

    #region checkPaymentStatus

    /**
     * Function  checkPaymentSatatus
     * @param $this ->vendor_id
     * @param $this ->agr_trans_id
     * @param $this ->payment_id
     * @param $this ->sign_time
     * @return mixed
     */


    public function checkPaymentSatatus()
    {
        $response = $this->client->request($this->methods['post'], 'pay_api/payment_status' , [
            'json' => [
                'VENDOR_ID' => $this->vendor_id,
                'AGR_TRANS_ID' => $this->agr_trans_id,
                'PAYMENT_ID' => $this->payment_id,
                'SIGN_TIME' => $this->generateSignTime(),
                'SIGN_STRING' => md5($this->SECRET_KEY. $this->agr_trans_id. $this->vendor_id. $this->payment_id .$this->sign_time)
            ]
        ]);

        return json_decode($response->getBody());
    }

    public function testCheckPaymentStatus()
    {
        $this->agr_trans_id = 1503642925905;
        $this->payment_id = 1;
        vd($this->generateSignTime());
        vd(md5($this->SECRET_KEY. $this->agr_trans_id. $this->vendor_id. $this->payment_id .$this->sign_time));
        vdd($this->checkPaymentSatatus());
    }
    #endregion



    #region generateSignTime

    /**
     * Function  generateSignTime
     * @param $this ->sign_time
     * @return mixed
     */

    public function generateSignTime()
    {
        return $this->sign_time = intval(microtime(true) * 1000);
    }

    #endregion

}
