<?php

namespace zetsoft\service\webs;
require Root . '/vendori/utility/league/vendor/autoload.php';

use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Instagram;
use zetsoft\models\user\User;
use zetsoft\models\user\UserOauth;
use zetsoft\service\cores\Auth;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Retur extends ZFrame
{

    public $result;
    public $data;

    /**
     *
     * Runs the action.
     */
    public function run()
    {
        global $boot;
        $service = $this->sessionGet('service');
        switch ($service) {
            case 'github':
                $provider = new Github([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['github']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['github']])->client_secret,
                    'redirectUri' => 'https://market.zetsoft.uz/core/core/return.aspx',
                ]);
                break;

            case 'google':
                $provider = new Google([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['google']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['google']])->client_secret,
                    'redirectUri' => 'https://market.zetsoft.uz/core/core/return.aspx',
                ]);
                break;
            case 'instagram':
                $provider = new Instagram([
                    'clientId' => UserOauth::findOne(['type' => UserOauth::type['instagram']])->client_id,
                    'clientSecret' => UserOauth::findOne(['type' => UserOauth::type['instagram']])->client_secret,
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
        }

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $this->httpGet('code')
        ]);

        $this->data = $provider->getResourceOwner($token);

        $result = $this->data->toArray();

        $this->sessionSet('oauthData', $result);
        if (!$this->checkUser($service)) {
            $this->userSave($service);
        }
        if (!empty($this->httpGet('redirectUrl'))) $this->urlRedirect($this->httpGet('redirectUrl'));
        Az::$app->params['redirect'] = true;
        $this->urlRedirect($this->urlGetBase());
    }

    public function userSave($service)
    {
        $result = $this->data->toArray();

        $model = new User();
        $model->configs->rules = validatorSafe;
        $model->columns();
        switch ($service) {
            case 'github':
                $model->role = 'client';
                $model->title = $result['login'];
                $model->photo = $result['avatar_url'];
                $model->password = 'defaultPassword';
                $model->oauth = $result;
                $model->oauth_type = $service;
                $model->save();
                return $this->SignIn($model->id);
                break;
            case 'instagram':
                $model->role = 'client';
                $model->title = $result['username'];
                $model->oauth = $result;
                $model->oauth_type = $service;
                $model->save();
                return $this->SignIn($model->id);
                break;
            case 'google':
                $model->role = 'client';
                $model->title = $result['name'];
                $model->email = $result['email'];
                $model->photo = $result['picture'];
                $model->verified_email = true;
                $model->oauth = $result;
                $model->oauth_type = $service;
                $model->save();
                return $this->SignIn($model->id);
                break;
            case 'facebook':
                $model->role = 'client';
                $model->title = $result['name'];
                $model->email = $result['email'];
                $model->photo = $result['picture_url'];
                $model->verified_email = true;
                $model->oauth = $result;
                $model->oauth_type = $service;
                $model->save();
                return $this->SignIn($model->id);
                break;
        }
    }

    public function checkUser($service)
    {
        switch ($service) {
            case 'github':
                $oauthData = $this->sessionGet('oauthData');
                $login = $oauthData['login'];
                $user = User::find()->where("oauth @> '{\"login\": \"$login\"}'")
                    ->andWhere(['oauth_type' => $service])
                    ->one();
                if ($user === null) return false;
                return $this->SignIn($user->id);
                break;
            case 'instagram':
                $oauthData = $this->sessionGet('oauthData');
                $login = $oauthData['username'];
                $user = User::find()->where("oauth @> '{\"username\": \"$login\"}'")
                    ->andWhere(['oauth_type' => $service])
                    ->one();
                if ($user === null) return false;
                return $this->SignIn($user->id);
                break;
            case 'google':
            case 'facebook':
                $oauthData = $this->sessionGet('oauthData');
                $login = $oauthData['email'];
                $user = User::find()->where("oauth @> '{\"email\": \"$login\"}'")
                    ->andWhere(['oauth_type' => $service])
                    ->one();
                if ($user === null) return false;
                return $this->SignIn($user->id);
                break;
        }
    }

    public function SignIn($id)
    {
        $this->sessionDel('oauthData');
        $this->sessionDel('service');
        return Az::$app->cores->session->set('login', true, Auth::duration, $id);
    }

}
