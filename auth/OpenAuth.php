<?php

namespace zetsoft\service\auth;

require Root . '/vendors/utility/league/vendor/autoload.php';

use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Instagram;
use zetsoft\models\user\UserOauth;
use zetsoft\system\kernels\ZFrame;

/**
 *
 * Author:  Shakhrizod Nurmukhammadov
 *
 */
class OpenAuth extends ZFrame
{
    public function run()
    {
        global $boot;
        $service = $this->httpGet('service');
        $this->sessionSet('service', $service);

        switch ($service) {

            case 'github':
                $provider = new Github([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['github']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['github']])->client_secret,
                    'redirectUri' => 'https://market.zetsoft.uz/core/core/return.aspx',
                ]);
                break;

            case 'facebook':
                $provider = new Facebook([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['facebook']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['facebook']])->client_secret,
                    'redirectUri' => 'https://market.zetsoft.uz/core/core/return.aspx',
                    'graphApiVersion' => 'v2.10',
                ]);
                break;

            case 'google':
                $provider = new Google([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['google']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['google']])->client_secret,
                    'redirectUri' => 'https://market.zetsoft.uz/core/core/return.aspx',
                    'hostedDomain' => 'https://market.zetsoft.uz', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
                ]);
                break;

            case 'instagram':
                $provider = new Instagram([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['instagram']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['instagram']])->client_id,
                    'graphApiVersion' => 'v2.10',
                    'redirectUri' => 'https://market.zetsoft.uz/core/core/return.aspx',
                    'host' => 'https://api.instagram.com',  // Optional, defaults to https://api.instagram.com
                    'graphHost' => 'https://graph.instagram.com'
                ]);
                break;

            case 'yandex':
                $appClass = Yandex::class;
                $clientId = 'b4d543fb0f2940a1846cd2b327378d54';
                $clientSecret = '27b70d45aa144341820300d8c2203ae7';
                break;
        }


//         $provider = new $appClass([
//             'clientId' => $clientId,
//             'clientSecret' => $clientSecret,
//             'graphApiVersion' => 'v2.10',
////             'redirectUri' => 'http://mplace.zetsoft.uz/core/core/return.aspx',
//         ]);
//     vdd($provider);
        $authUrl = $provider->getAuthorizationUrl();
// vdd('d');
        return $this->urlRedirect($authUrl);

    }
}
