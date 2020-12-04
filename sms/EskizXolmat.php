<?php

/**
 *
 *
 * Author:  Umid Abdurakhmonov
 * Author:  Abdulloh Tursunaliyev
 *
 */

namespace zetsoft\service\sms;


use GuzzleHttp\Client;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;

class EskizXolmat extends ZFrame
{
    #region Vars
    private $client;
    private $methods = [
        'post' => "POST",
        'get' => "GET",
        'delete' => "DELETE",
        'put' => "PUT",
        'patch' => "PATCH"
    ];

    public $contactId = '';
    public $url = "http://notify.eskiz.uz/api/message/sms/send";
    public $tokenUrl = "http://notify.eskiz.uz/api/auth/login";
    public $refreshTokenUrl = "http://notify.eskiz.uz/api/auth/refresh";
    public $deleteTokenUrl = "http://notify.eskiz.uz/api/auth/invalidate";
    public $userInfoUrl = "http://notify.eskiz.uz/api/auth/user";
    public $contactsUrl = "http://notify.eskiz.uz/api/contact";
    public $templateUrl = "http://notify.eskiz.uz/api/template";
    public $sendMessageToContactUrl = "http://notify/api/message/sms/send-batch";
    public $summaryOfSentSMSUrl = "https://notify.eskiz.uz/api/user/totals";
    public $getLimitUrl = "http://notify.eskiz.uz/api/user/get-limit";

    public $phone;
    public $message;
    #endregion

    #region Cores
    public function init()
    {
        parent::init();

        $this->client = new Client();
    }

    #endregion




    public function getTokenTest()
    {
        $email = "test@eskiz.uz";
        $password = "j6DWtQjjpLDNjWEk74Sx";
        return $this->getToken($email, $password);
    }

    
    public function getToken($email, $password)
    {

        $client = new Client();

        $response = $client->request('POST', $this->tokenUrl, [
        
              'form_params' => [
                  'email' => $email,
                  'password' => $password
              ]
              
        ]);

        vdd($response->getHeaders());
    }


    
    public function sendSmsTest()
    {
        $phone = "998998113264";

        $msg = "salom from zet";

        //$token = $this->getToken('test@eskiz.uz','j6DWtQjjpLDNjWEk74Sx');
          $token = "84edb95fde58ef1952ca4f9dcec9a4161e8c8c9a";
          
        $from = "bahodir";

        $this->sendSms($phone, $msg, $token, $from);
    }



    public function sendSms($phone, $msg, $token = null, $from = '') {
    
        $client = new Client();

        $response = $client->request('POST', $this->url, [

            'headers' => [
                'Authorization' => "Bearer {$token}"
            ],
            
            'form_parms' => [
                    'mobile_phone' => $phone,
                    'message' => $msg,
                    'from' => $from
            ],
                
            ]);

        return $response;

    }



    public function refreshToken($token)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['patch'])
            ->setUrl($this->refreshTokenUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = ZJsonHelper::decode($response->content);

        $array = $json['data'];
        $newToken = $array['token'];
        /*foreach ($array as $key => $value) {
            $newToken = $value;
        }*/

        return $newToken;
    }

    public function userInfo($token)
    {

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->userInfoUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function addContacts($token, $name, $email, $group, $phone)
    {

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['post'])
            ->setUrl($this->contactsUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->setData([
                'name' => $name,
                'email' => $email,
                'group' => $group,
                'mobile_phone' => $phone,
            ])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;

    }

    public function refreshContact($token, $name, $email, $group, $phone, $id)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['put'])
            ->setUrl($this->contactsUrl . $id)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->setData([
                'name' => $name,
                'email' => $email,
                'group' => $group,
                'mobile_phone' => $phone,
            ])
            ->send();
        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function getContact($token, $id)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->contactsUrl . $id)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;

    }

    public function getAllContacts($token)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->contactsUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;

    }

    public function removeContact($token, $id)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['delete'])
            ->setUrl($this->contactsUrl . $id)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function addTemplate($token, $name, $text)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['post'])
            ->setUrl($this->templateUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->setData([
                'name' => $name,
                'text' => $text,
            ])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function refreshTemplate($token, $name, $text, $id)
    {

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['put'])
            ->setUrl($this->templateUrl . $id)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->setData([
                'name' => $name,
                'text' => $text,
            ])
            ->send();
        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function getTemplate($token, $id)
    {

        $client = new Client();

        $response = $client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->templateUrl . $id)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function getAllTemplate($token)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->templateUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;
    }

    public function removeTemplate($token, $id)
    {

        $client = new Client();

        $response = $client->createRequest()
            ->setMethod($this->methods['delete'])
            ->setUrl($this->templateUrl . $id)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;

        $array = ZJsonHelper::decode($json);

        return $array;

    }



    public function deleteToken($token)
    {

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['delete'])
            ->setUrl($this->deleteTokenUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = $response->content;
        $array = ZJsonHelper::decode($json);
        $msg = '';

        foreach ($array as $v)
            $msg = $v;

        return $msg;

    }
    
    public function sendSmsToContacts($token, $phone = null, $msg = null, $from = null, $dispatch_id = null)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod($this->methods['post'])
            ->setUrl($this->sendMessageToContactUrl)
            ->addHeaders(['Authorization' => 'Bearer ' . "$token"])
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

        return $array;

    }

    public function summaryOfSentSMS($token, $year, $user_id)
    {

        $client = new Client();

        $response = $client->createRequest()
            ->setMethod($this->methods['post'])
            ->setUrl($this->summaryOfSentSMSUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->setData([
                'year' => $year,
                'user_id' => $user_id
            ])
            ->send();

        $json = ZJsonHelper::decode($response->content);

        $array = ZArrayHelper::getValue($json, 'data');

        return ZArrayHelper::getValue($array, 'token');
    }

    public function getLimit($token)
    {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod($this->methods['get'])
            ->setUrl($this->getLimitUrl)
            ->addHeaders(['Authorization' => 'Bearer' . "$token"])
            ->send();

        $json = ZJsonHelper::decode($response->content);

        $array = ZArrayHelper::getValue($json, 'data');

        return ZArrayHelper::getValue($array, 'token');
    }


}
