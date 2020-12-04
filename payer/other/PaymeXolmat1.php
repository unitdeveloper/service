<?php
/**
 *
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\payer;

use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;



class PaymeXolmat1 
{


    #region Vars

    public $test = true;
    public $number = '8600060921090234';


    /*
     * 8600 0609 2109 0842	03/20	SMS-informing is not connected.
       3333 3364 1580 4657	03/15	The card has expired.
       4444 4459 8745 9073	03/20	The card is blocked.
       8600 1434 1777 0323	03/20	Unknown system error.
       8600 1343 0184 9596	03/20	Simulate a processing delay of 10 seconds. Ends with an error.
       8600 4954 7331 6478	03/20
       8600 0691 9540 6311	03/20
     */


    /**
     * @var string
     *
     */
    //public $expire = '0320';
    private $token;
    private $authBackend = '5f2fe4c73ddb59936a2e76sfdsdec:vCZh2b0o&3ea5Pjkt4PdH3gQs1kpY?ZfsdfSJ7NK';
    private $authRecepy = '5f2fe4c73ddb59936a2e76ec:Ka8TGpAXtwqjBHU9dCdF63u8gqRIKwRvj7Gz';
    private $authFront = '5f2fe4c73ddb59936a2e76ec';
    public $save = true;
    public $code = "666666";

    public $id = "comapany_id"; // paymedan reg dan o'tgan company id

    public $order_id = 106; // merchant tomonidan beriladigan id

    public $phone = "998901234567"; // company phone

    public $result; // qaytadigan natija
    public $error;   // qaytadigan error

    public $amount = 250000;

    public $baseUri;  // asosiy endpoint
    public $client;

    public const testCards = [

    ];

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

        $this->client = new Client(['base_uri' => $this->baseUri]);
    }


    /**
     *
     * Requirements for the form of data entry of a plastic card
     * Input elements on a form must not contain an attribute name
     * The tag formmust not contain an attributeaction...
     *
     */

    public function run()
    {
        $this->create();

        $this->getVerifyCode();

        $this->verify();

        $this->checkCard();

        $this->createReceipts();

        $this->payReceipts();

        $this->sendReceipts();

        $this->checkReceipts();

        $this->getReceipts();


    }

    // to'lov uchun card yaratadi 
    #region Create
    public function create()
    {
        $method = 'cards.create';

        $response = $this->client->request('POST', '/api', [

            'headers' => [
                'X-authBackend' => $this->authFront,
            ],

            'json' => [
                'method' => $method,
                'params' => [

                    'card' => [
                        'number' => $this->number,
                        'expire' => $this->expire,
                    ],

                    'amount' => $this->amount,

                    'save' => true,

                    'customer' => 'shop_order_id',

                ],
            ]
        ]);


        $body = $response->getBody();

        $decode = json_decode($body, true);


        if (ZArrayHelper::keyExists('token', $decode))
            $this->token = ArrayHelper::getValue($decode, 'result.card.token');
        else
            return $this->error = ArrayHelper::getValue($decode, 'error.message');


    }

    #endregion


    // sms yuboradi agar card exists bo'lsa
    #region getVerifyCode
    public function getVerifyCode()
    {
        $method = 'cards.get_verify_code';


        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authFront,

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

        
    }

    #endregion


    // yuborilgan smsni tekshiradi agar yuborilgan sms bilan tog'ri bo'lsa true qaytaradi
    #region Verify
    public function verify()
    {
        $method = 'cards.verify';


        $response = $this->client->request('POST', '/api', [

            'headers' => [
                'X-authBackend' => $this->authFront,
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
        return $result->result->card->verify;
    }

    #endregion

    public function checkCard()
    {
        $method = 'cards.check';


        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authFront,
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $this->token
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body, true);

        return $result;

    }
    #endregion


    #region removeCard
    public function removeCard()
    {
        $method = "cards.remove";


        $response = $this->client->request('POST', '/api', [

            'headers' => [
                'X-authBackend' => $this->authFront,
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

    #region createReceipts
    public function createReceipts()
    {

        $method = 'receipts.create';


        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authBackend,
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

        return $result;

    }

    #region payReceipts
    public function payReceipts()
    {
        $method = "receipts.pay";

        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authRecepy,
            ],
            'json' => [
                'method' => $method,
                'params' => [
                    'id' => $this->id,
                    'token' => $this->token,
                    'payer' => [
                        'phone' => $this->phone,
                    ]
                ],
            ]
        ]);

        $body = $response->getBody();
        $result = json_decode($body);

        return $result;

        /* if (isset($result->result->success) == true) {
             return "Успешно оплачено";
         } else {
             return $result->error->message;
         }*/
    }
    #endregion


    #endregion
    public function sendReceipts()
    {
        $method = "receipts.send";


        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authFront,
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


    public function checkReceipts()
    {
        $method = "receipts.check";


        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authFront,
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
    public function getReceipts()
    {

        $method = "receipts.get";


        $response = $this->client->request('POST', '/api', [

            'headers' => [
                'X-authBackend' => $this->authRecepy,
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


    #region cancelReceipts
    public function cancelReceipts()
    {
        $method = "receipts.cancel";


        $response = $this->client->request('POST', '/api', [
            'headers' => [
                'X-authBackend' => $this->authFront,
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


}
