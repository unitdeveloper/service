<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\sms;

use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;
use yii\httpclient\Client;

class Eskiz2 extends ZFrame{
   #region Vars
    private $client;
    private $methods = [
        'post'=> "POST",
        'get' => "GET",
        'delete'=>"DELETE",
        'put'=>"PUT",
        'patch'=>"PATCH"
    ];
    
    public $sendMessageToContactUrl ="http://notify.eskiz.uz/api/message/sms/send-batch";
   #endregion

   #region Cores
    public function init()
    {
        parent::init();

        $this->client = new Client();
    }
    #endregion

    public function sendSmsToContacts($token, $phone = null, $msg = null, $from = null, $dispatch_id = null){
        $client  =  new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['post'])
            ->setUrl($this->sendMessageToContactUrl)
            ->addHeaders(['Authorization' => 'Bearer '."$token"])
            ->setData([
                'messages' =>
                    [
                        "to" => $phone,
                        "text" => $msg
                    ],
                'from' => $from,
                'dispatch_id' => $dispatch_id
            ])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        vdd($array);
        //return $array;

    }

}
