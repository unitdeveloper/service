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

use yii\httpclient\Client;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;


class PaymeOld extends ZFrame
{
    #region Vars
    private $client;
    public  $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    private $id_company = '5f2fe4c73ddb59936a2e76ec';
    private $url = [
        'hostUrl' => "https://checkout.test.paycom.uz/api",

    ];
    private $password = 'qwerty';

    #endregion


    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client();
    }

    #endregion
    public function test(){

        return "test";
    }

    public function cardsCreate($id)
    {

        /*
         *
         *
         * POST /api HTTP/1.1
Host: checkout.test.paycom.uz
X-Auth: 100fe486b33784292111b7dc
Cache-Control: no-cache

{
    "id": 123,
    "method": "cards.create",
    "params": {
        "card": { "number": "4444444444444444", "expire": "0420"},
        "amount": 350000, //Запрашиваемая сумма платежа в тийинах.
        "save": true
    }
}
         *
         * */

        $client = new Client();

        $method = 'cards.create';

        $response = $client->createRequest()
            ->setMethod('POST /api HTTP/1.1')
            ->setUrl($this->url)
            ->addHeaders([
                'X-Auth' => $this->id_company
            ])
            ->setData([
                'id ' => $id,
                'method' => $method,
                'params' => [
                    'card' => [
                        'number' => '8600495473316478',
                        'expire' => '0320'
                    ],
                    'amount' => 350000, //Запрашиваемая сумма платежа в тийинах.
                    'save' => true
                ]
            ])
            ->send();

        vdd($response);
        //return $array;
    }


    public function create(){

        $client = new Client();

        //$method = 'cards.create';

        $response = $client->createRequest()
            ->setMethod($this->methods['post'])
            ->setUrl($this->url['hostUrl'])
            ->addHeaders([
                'X-Auth' => $this->id_company.':'.$this->password
            ])
            ->setData([
                'id ' => 123,
                'method' => 'cards.create',
                'params' => [
                    'card' => [
                        'number' => '8600495473316478',
                        'expire' => '0320'
                    ],
                    'amount' => 350000, //Запрашиваемая сумма платежа в тийинах.
                    'save' => true
                ]
            ])
            ->send();

        vdd($response);
        //return $array;

    }
}
