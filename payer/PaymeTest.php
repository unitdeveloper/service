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


class PaymeTest extends ZFrame
{


    #region Vars

    public $test = true;
    public $number = '8600060921090842';

    /**
     * @var string
     *
     */
    public $expire = '0399';
    public $token;
    public $save = true;
    public $code = "666666";

    public $id = "2e0b1bc1f1eb50d487ba268d";
    public $order_id = 106;
    public $phone = "998901304527";

    public $result;
    public $error;


    public $amount = 250000;

    public $baseUri;

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


    #region  Client_side


    #region Create

    public function createTest()
    {
        /*$this->number;
        $this->expire;*/
        //$this->amount = '350000';

        $token = Az::$app->payer->payme->create();
         //ZTest::assertEquals(1, $token);
         vdd($token);
//        vdd(Az::$app->payer->payme->checkCard($token));
    }

    /**
     *
     * Function  create
     * Methods for the client side of the merchant application:
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     *  https://help.paycom.uz/ru/metody-subscribe-api/cards.create
     * 
     *
     */
    public function create()
    {
        $method = 'cards.create';
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [

            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],

            'json' => [
                "method" => $method,
                "params" => [
                    "card" => [
                        "number" => $this->number,
                        "expire" => $this->expire
                    ],
                    "amount" => $this->amount,
                    "save" => true,
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

    #region getVerifyCode
    /**
     *
     * Function  getVerifyCode
     * @param $token
     * @return  string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.get_verify_code
     */
    public function getVerifyCodeTest()
    {
        /*$this->number = '';
        $this->expire = '';*/
//        $this->amount = '350000';

        $token = Az::$app->payer->payme->create();
        vdd(Az::$app->payer->payme->getVerifyCode());
    }



    public function getVerifyCode()
    {
        $method = 'cards.get_verify_code';
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',

            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $this->token
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->sent) === true) {
            return "Успешно";
        } else {
            return $result->error->message;
        }

    }

    #endregion

    #region Verify
    /**
     *
     * Function  verify
     * @param $token
     * @param $code
     * @return  string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.verify
     */

    public function verifyTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        $token = Az::$app->payer->payme->create();
        //Az::$app->payer->payme->getVerifyCode();
        vdd(Az::$app->payer->payme->verify());
    }

    public function verify()
    {
        $method = 'cards.verify';
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $this->token,
                    "code" => $this->code
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->sent) === true) {
            return "Успешно";
        } else {
            return $result->error->message;
        }
    }
    #endregion


    #endregion

    #region Server_side

    
    #region checkCard
    /**
     *
     * Function  checkCard
     * Methods for the server side of the trading application:
     * @param $token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.check
     */

    public function checkCardTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        $token = Az::$app->payer->payme->create();
//        Az::$app->payer->payme->getVerifyCode();
//        Az::$app->payer->payme->verify();
        vdd(Az::$app->payer->payme->checkCard());
    }

    public function checkCard()
    {
        $method = 'cards.check';
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $this->token
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->sent) === true) {
            return "Успешно";
        } else {
            return $result->error->message;
        }

    }
    #endregion

    #region removeCard

    /**
     *
     * Function  removeCard
     * @param $token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/cards.remove
     */

    public function removeCardTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        $token = Az::$app->payer->payme->create();
        vdd(Az::$app->payer->payme->removeCard());
    }

    public function removeCard()
    {
        $method = "cards.remove";
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $this->token
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        /*if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }*/

        vdd($result);
    }

    #endregion


    #region Checks

    #region createReceipts
    /**
     *
     * Function  createReceipts
     * @param $amount 50 000 kam bosa request bormidi
     * @param $order_id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.create
     */

    public function createReceiptsTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        // ZTest::assertEquals(1, $token);
        $token = Az::$app->payer->payme->create();

        vdd(Az::$app->payer->payme->createReceipts());
    }

    public function createReceipts()
    {
        $method = 'receipts.create';
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "amount" => $this->amount,
                    "account" => [
                        "order_id" => $this->order_id
                    ],
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);
        //$this->id = $result->result->receipt->order_id;
        return $result;


    }

    #endregion


    #region payReceipts

    /**
     *
     * Function  payReceiptsTest
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.pay
     */
    public function payReceiptsTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        $token = Az::$app->payer->payme->create();
        /*Az::$app->payer->payme->getVerifyCode();
        Az::$app->payer->payme->verify();
        Az::$app->payer->payme->checkCard();*/
        Az::$app->payer->payme->createReceipts();

        vdd(Az::$app->payer->payme->payReceipts());
    }


    public function payReceipts()
    {
        $method = "receipts.pay";
        $client = new Client(['base_uri' => $this->baseUri]);
        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => 'Ka8TGpAXtwqjBHU9dCdF63u8gqRIKwRvj7Gz',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $this->id,
                    "token" => $this->token,
                    "payer" => [
                        "phone" => $this->phone,
                    ]
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }
    }
    #endregion

    #region sendReceipts

    /**
     *
     * Function  sendReceipts
     * @param $id
     * @param $phone
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.send
     */

    public function sendReceiptsTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        // ZTest::assertEquals(1, $token);
        vdd(Az::$app->payer->payme->sendReceipts());
    }

    public function sendReceipts()
    {
        $method = "receipts.send";
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $this->id,
                    "phone" => $this->phone,
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }

    }
    #endregion

    #region cancelReceipts

    /**
     *
     * Function  cancelReceipts
     * @param $id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.cancel
     */


    public function cancelReceiptsTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        // ZTest::assertEquals(1, $token);
        vdd(Az::$app->payer->payme->cancelReceipts());
    }

    public function cancelReceipts()
    {
        $method = "receipts.cancel";
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $this->id,
                    ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }

    }
    #endregion

    #region checkReceipts

    /**
     *
     * Function  checkReceipts
     * @param $id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.check
     */

    public function checkReceiptsTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        // ZTest::assertEquals(1, $token);
        vdd(Az::$app->payer->payme->checkReceipts());
    }

    public function checkReceipts()
    {
        $method = "receipts.check";
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $this->id,
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }

    }
    #endregion

    #region getReceipts

    /**
     *
     * Function  getReceipts
     * @param $id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Muminov Umid
     *
     * https://help.paycom.uz/ru/metody-subscribe-api/receipts.get
     */

    public function getReceiptsTest()
    {
        /*$this->number;
        $this->expire;
        $this->amount = '350000';*/
        // ZTest::assertEquals(1, $token);
        //$this->id = 'bc1f1eb502e0b1d487ba268d';
        vdd(Az::$app->payer->payme->getReceipts());
    }

    public function getReceipts()
    {
        $method = "receipts.get";
        $client = new Client(['base_uri' => $this->baseUri]);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $this->id,
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }

    }
    #endregion


    #endregion
}
