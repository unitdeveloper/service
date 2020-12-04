<?php
namespace zetsoft\service\payer;

use GuzzleHttp\Client;
use zetsoft\system\kernels\ZFrame;


class PaymeOld1 extends ZFrame
{

    #region  Client_side

    /**
     *
     * Function  create
     * Methods for the client side of the merchant application:
     * @param $number
     * @param $expire
     * @param $amount
     * @return  mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Aziz and Abdurakhmonov Umid
     *
     */
    public function create($number, $expire, $amount)
    {
        $method = 'cards.create';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [

            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],

            'json' => [
                "method" => $method,
                "params" => [
                    "card" => [
                        "number" => $number,
                        "expire" => $expire
                    ],
                    "amount" => $amount,
                    "save" => true,
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);
        $token = $result->result->card->token;
        vdd($result);
        //return $token;

    }

    public function getVerifyCode($token)
    {
        $method = 'cards.get_verify_code';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',

            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $token
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        if (isset($result->result->sent) == true) {
            return "Успешно";
        } else {
            return $result->error->message;
        }

        //vdd($result);

    }

    public function verify($token, $code)
    {
        $method = 'cards.verify';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',

            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $token,
                    "code" => $code
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        if (isset($result->result->card->verify) == true) {
            return "Успешно проверена";
        } else {
            return $result->error->data->message;
        }


        //vdd($result);
    }

    #endregion

    #region Server_side

    /**
     *
     * Function  checkCard
     * Methods for the server side of the trading application:
     * @param $token
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Aziz and Abdurakhmonov Umid
     */

    public function checkCard($token)
    {
        $method = 'cards.check';
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "token" => $token
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        if (isset($result->result->success) == true) {
            return "Успешно удален";
        } else {
            return $result->error->message;
        }

        //vdd($result);
    }

    #endregion


    #region Checks
    /**
     *
     * Function  createReceipts
     * @param $amount 50 000 kam bosa request bormidi
     * @param $order_id
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createReceipts($amount, $order_id)
    {
        $method = "receipts.create";
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2fe4c73ddb59936a2e76ec',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "amount" => $amount,
                    "account" => [
                        "order_id" => $order_id
                    ],
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);

        return $result->result->receipt;

        //vdd($result);

    }

    public function payReceipts($token, $phone, $id)
    {
        $method = "receipts.pay";
        $client = new Client(['base_uri' => 'https://checkout.test.paycom.uz']);

        $response = $client->request('POST', '/api', [
            'headers' => [
                'X-Auth' => '5f2d650cdb2875332a0f133d',
            ],
            'json' => [
                "method" => $method,
                "params" => [
                    "id" => $id,
                    "token" => $token,
                    "payer" => [
                        "phone" => $phone,
                    ]
                ],
            ]
        ]);

        $qwer = $response->getBody();
        $result = json_decode($qwer);
        vdd($result);

    }


    #endregion
}
