<?php
/**
 *
 *
 * Author: Xolmat Ravshanov
 *
 *
 */

namespace zetsoft\service\payer;

require Root.'/vendori/netapp/vendor/autoload.php';
use GuzzleHttp\Client;
use zetsoft\system\kernels\ZFrame;

class Uzcard extends ZFrame
{
    #region Vars
    private $client;
    public  $response;
    private $username = '998974037243';
    private $password = '12345';
    public $eposId = '996040';
    private $auth;

    public $userId;
    public $cardNumber;
    public $expireDate;

    public $session;
    public $amount;
    public $transactionId;
    public $cardLastSix;
    public $phoneLastNine;
    public $beginDate;
    public $endDate;
    public $page;
    public $count;
    public $transactionStatus;
    public $IsWithRegistration;
    public $isTrusted = 0;
    public $userCardId;
    public $cardName = "MyUzcard";

    public $otp;

    public $cardNumberUse;

    public $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    public $test = true;
    public $baseUri;

    public const baseUri = [
        'test' => 'https://www.getpostman.com/apps',
        'main' => 'https://myuzcard.uz/api/PaymentBusiness/paymentsWithOutRegistrationNew',
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



    private $url = "https://pay.myuzcard.uz/api/userCard";
    #endregion

    #region Vars Development
    public $isTest = true;

    public const testCards = [
        0 => '86003129639034222',
    ];

    public const testExpires = [
        0 => '1219'
    ];

    public const testUserIds = [
        0 => '123',
    ];


    #endregion

/*    #region Cores
    public function init()
    {
        parent::init();

        $this->client = new Client(['base_uri' => $this->url]);

        $this->auth = ['auth' => [$this->username, $this->password]];
        
        if ($this->isTest) {
            $this->cardNumber = self::testCards[0];
            $this->expireDate = self::testExpires[0];
            $this->userId = self::testUserIds[0];
        }


    }*/

    #endregion


    public function test()
    {
        $this->createUserCard();
    }

    /**
     * Function  createUserCard
     * @param $this ->userId
     * @param $this ->cardNumber
     * @param $this ->expireDate
     * @return  mixed
     *
     */
    /*public function createUserCard()
    {

        $this->response = $this->client->request($this->methods['post'], "/createUserCard", [

            $this->auth,

            'form_params' => [
                'userId' => $this->userId,
                'cardNumber' => $this->cardNumber,
                'expireDate' => $this->expireDate
            ]
        ]);

        vdd($this->response);
    }*/
    public function createUserCard()
    {

        $method = "receipts.pay";
        
        $client = new Client(['base_uri' => $this->baseUri]);
        
        $response = $client->request('POST', '/api', [
        
            'headers' => [
            
                'Auth' => $this->auth,

            ],
            
            'json' => [
                "method" => $method,
                "params" => [
                    "key" => $this->id,
                    "eposId" => $this->token,
                        ],
                        "phone" => $this->phone,
                ],
                
        ]);

        vdd($this->response);
    }

    /**
     *
     * Function  confirmUserCardCreate
     * @param int $this ->isTrusted
     * @param string $this ->cardName
     * @return  mixed
     */

    public function confirmUserCardCreate()
    {
        $this->response = $this->client->request($this->methods['post'], "/confirmUserCardCreate", [
            $this->auth,
            'form_params' => [
                'session' => $this->session,
                'otp' => $this->otp,
                'isTrusted' => $this->isTrusted,
                'cardName' => $this->cardName
            ]
        ]);

    }


    /**
     *
     * Function  confirmPayment
     * @param $this ->session   Сессия OTP (код подтверждения)
     * @param $this ->otp Код подтверждения
     * @return  mixed
     */
    public function confirmPayment()
    {
        $this->response = $this->client->request($this->methods['post'], "/confirmPayment", [
            $this->auth,
            'form_params' => [
                'session' => $this->session,
                'otp' => $this->otp,

            ]
        ]);


    }

    /**
     *
     *
     * Function  payment
     * @param $this ->userId mplace user_id or product_id
     * @param $this ->cardId uzcard card_id
     * @param $this ->amount so'ralayot pul summasi
     * @return  mixed
     */

    public function payment()
    {
        $this->response = $this->client->request($this->methods['post'], "/payment", [
            $this->auth,
            'form_params' => [
                'userId' => $this->userId,
                'cardId' => $this->cardId,
                'amount' => $this->amount,
            ]
        ]);


    }


    /**
     *
     * Function  getAllUserCards
     * @param $this ->userId  Идентификатор поставшика
     */
    public function getAllUserCards()
    {

        $this->response = $this->client->request($this->methods['post'], "/getAllUserCards", [
            $this->auth,

            'form_params' => [
                'userId' => $this->userId,
            ]
        ]);


    }

    public function deleteUserCard()
    {

        $this->response = $this->client->request($this->methods['post'], "/deleteUserCard", [
            $this->auth,

            'form_params' => [
                'userCardId' => $this->userCardId,
            ]
        ]);


    }

    /**
     *
     * Function  getTransactions
     * @param $this ->userId Идентификатор пользователя на стороне клиента
     * @param $this ->transactionId    Идентификатор транзакции в системе MyUzcard
     * @param $this ->beginDate        Дата начала (yyyy-mm-dd)
     * @param $this ->endDate          Дата конца (yyyy-mm-dd)
     * @param $this ->page             Страница
     * @param $this ->count            Параметр показывает сколько элементов на странице
     * @param $transactionStatus      Статус транзакции
     * @param $this ->IsWithRegistration
     * @return  mixed
     */

    public function getTransactions()
    {

        $this->response = $this->client->request($this->methods['post'], "/getTransactions", [
            $this->auth,

            'form_params' => [
                'userId' => $this->userId,
                'transactionId' => $this->transactionId,
                'beginDate' => $this->beginDate,
                'endDate' => $this->endDate,
                'page' => $this->page,
                'count' => $this->count,
                'transactionStatus' => $this->transactionStatus,
                'IsWithRegistration' => $this->IsWithRegistration,
            ]

        ]);


    }


    /**
     *
     * Function  paymentReverse
     * @param $this ->transactionId    Идентификатор транзакции в системе MyUzcard
     * @return  mixed
     */

    public function paymentReverse()
    {
        $this->response = $this->client->request($this->methods['post'], "/paymentReverse", [
            $this->auth,
            'form_params' => [
                'transactionId' => $this->transactionId,
            ]
        ]);

    }


    /**
     *
     * Function  paymentWithoutRegistration
     * @param $this ->amount Сумма
     * @param $this ->cardLastSix  Последние 6 цифры (PAN) номера карты
     * @param $this ->expireDate   Срок действия карты (YYMM)
     * @param $this ->phoneLastNine Номер телефона, привязанный к карте
     * @return  mixed
     */

    public function paymentWithoutRegistration()
    {
        $this->response = $this->client->request($this->methods['post'], "/paymentWithoutRegistration", [
            $this->auth,
            'form_params' => [
                'amount' => $this->amount,
                'cardLastSix' => $this->cardLastSix,
                'expireDate' => $this->expireDate,
                'phoneLastNine' => $this->phoneLastNine,

            ]
        ]);


    }


}
