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

use Goodoneuz\PayUz\Models\PaymentSystem;
use Goodoneuz\PayUz\Services\PaymentSystemService;
use GuzzleHttp\Client;
use zetsoft\system\kernels\ZFrame;

class PaymeUz extends ZFrame
{


    #region Vars
    private $client;
    private $params;
    public $response;
    public $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $id_company = '5f2fe4c73ddb59936a2e76ec';
    private $url = [
        'baseUrl' => "https://checkout.test.paycom.uz/api",

    ];
    private $password = 'qwerty';

    #endregion

    #region Cores
    public function init()
    {
        parent::init();
        $this->params = PaymentSystemService::getPaymentSystemParamsCollect(PaymentSystem::PAYME);

        $this->client = new Client('base_uri',$this->url['baseUrl']);
    }
    #endregion


    
    public function test()
    {
        return 'dsds';
        /*$this->CreateTransaction();
        $this->CheckPerformTransaction();

        $this->assertTrue(true);*/
    }


    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function CheckPerformTransaction()
    {
        if(!isset($this->params['login']) || !isset($this->params['password']))
        {
            return false;
        }

        $str = '{
            "method" : "CheckPerformTransaction",
            "params" : {
                "amount" : 500000,
                "account" : {
                    "key" : 5
                }
            }
        }';

        $headers = [
            'Content-Type'  => 'text/json; charset=UTF-8',
            'HTTP-Authorization' => 'Basic ' . base64_decode($this->params['login'] . ':' .$this->params['password'])
        ];
        $response = $this->withHeaders($headers)->json('POST','/handle/payme',['request' => $str]);
        // dd($response);
        $response
            ->assertStatus(200)
            ->assertExactJson(json_decode('{"jsonrpc":"2.0","id":null,"result":{"allow":true},"error":null}',true));
    }

    public function CreateTransaction()
    {
        $str = '{
                "method" : "CreateTransaction",
                "params" : {
                "id" : "5305e3bab097f420a62ced0b",
                "time" : 1399114284039,
                "amount" : 500000,
                "account" : {
                    "key" : "5"
                }
            }
        }';
        $headers = [
            'Content-Type'  => 'text/json; charset=UTF-8',
        ];
        $response = $this->withHeaders($headers)->json('POST','/handle/payme',['request' => $str]);
        $response
            ->assertStatus(200)
            ->assertExactJson(json_decode('{
                "result" : {
                    "create_time" : 1399114284039,
                    "transaction" : "5123",
                    "state" : 1,
                    "receivers" : null
                }
            }'
            ,true));
    }
}
