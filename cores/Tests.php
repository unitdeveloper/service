<?


/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    9/20/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cores;


use Yii;
use yii\helpers\Json;
use yii\rbac\DbManager;
use zetsoft\dbcore\ALL\CoreRoleCore;
use zetsoft\dbcore\ALL\UserCore;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\former\auth\AuthRegisterForm;
use zetsoft\models\core\CoreRole;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


/**
 * Class ZAuth
 * @package zetsoft\service
 *
 * @property DbManager $auth
 * @property User $identity
 */
class Tests extends ZFrame
{


    #region Vars

    public const duration = 3600 * 24 * 1; // 1 Day
    public $auth;

    /* @var AuthLoginForm $model */
    public $model;

    /* @var UserCore $_identity */
    private $_identity;
    private $_user;


    #endregion


    #region App

    public function init()
    {
        parent::init();
        $this->auth = Az::$app->authManager;
    }


    public function photos()
    {

        $zoft = $this->thisGet();
        if ($zoft instanceof User)
            $user = $zoft;
        else
            $user = $this->userIdentity();

        $photos = $user->photo;
        $return = [];

        if (empty($photos) || $photos !== null)
            $return[] = '/webhtm/shop/image/profile.jpg';
        else {

            foreach ($photos as $key => $photo) {
                $path = "/upload/User/photo/$user->id/$photo";
                $return[] = $path;
            }
        }

        return $return;
    }

    public function photo()
    {
        $zoft = $this->thisGet();
        $this->thisSet($zoft);
        $img = ZArrayHelper::getValue($this->photos(), 0);

        if (!file_exists(Az::getAlias('@webroot' . $img)))
            $img = '/webhtm/shop/image/profile.jpg';

        return $img;
    }


    public function getIdentity()
    {
        $this->_identity = new User();

        if (!$boot->isCLI()) {
            if (!Az::$app->user->isGuest)
                return Az::$app->user->identity;

            $this->_identity->id = 0;
            $this->_identity->name = Az::l('Гость');
            return $this->_identity;
        }

        /**
         *
         * For CMD
         */

        $userID = $boot->env('userID');

        $this->_identity->id = (!empty($userID)) ? $userID : 1;
        $this->_identity->name = 'Cmd';

        return $this->_identity;

    }


    public function user($email)
    {
        if ($this->_user === null)
            $this->_user = User::find()
                ->where([
                    'email' => $email,
                ])
                ->limit(1)
                ->one();

        return $this->_user;
    }

    #endregion

    #region Check

    public function hashGet($str)
    {
        return hash('sha256', $str);
    }


    public function hashCheck($str, $hash)
    {
        $mineHash = $this->hashGet($str);
        return $mineHash === $hash;
    }

    public function isHash($hash)
    {
        $len = strlen($hash);
        return $len === 64;
    }

    #endregion


    #region Main

    /**
     *
     * Function  login
     * @param $user User
     * @return  bool
     */
    public function login(&$user)
    {
        Az::$app->user->logout();
        return Az::$app->user->login($user, self::duration);
    }

    public function register($model)
    {
        /** @var UserCore $user */
        /*
                $user = new User();
                $user->name = $model->name;

                $user->title = $model->title;
                $user->email = $model->email;
                $user->password = $model->password;

                // $user->save();
                // vdd($user->save());

                if ($boot->env('verifyEmail')) {
                    $user->verify_code = random_int(100000, 999999);
                    $user->verified_email = false;
                } else {
                    $user->verify_code = 0;
                    $user->verified_email = true;
                }*/

        /*if (!empty($model->service) && !empty($model->custom_field)) {
            $user->{$model->service} = $model->custom_field;
        }*/

        /* $role = CoreRole::find()
             ->where(['name' => 'monitor'])
             ->limit(1)
             ->one();

         //vdd($role);

         if ($role !== null)

             $user->role = $role->id;

         $isSaved = $user->save();

         if ($isSaved && $boot->env('verifyEmail')) {
             $this->sendConfirmMail($user->verify_code, $user->id, $model->email);
         }

         return $isSaved ? $user : false;*/
    }

    public function oauth($registerMode = false)
    {
        $model = $registerMode ? new AuthRegisterForm() : null;

        $cookies = Yii::$app->request->cookies;
        if ($cookies->has('service_name') && $cookies->has('service_data')) {
            $service = $cookies->getValue('service_name');
            if ($model !== null)
                $model->service = $service;
            $service_data = Json::decode($cookies->getValue('service_data'));
            switch ($service) {
                case 'google':
                    if ($model === null) {
                        $user = User::findOne([$service => $service_data['email']]);
                    }
                    $model->email = $service_data['email'];
                    $model->name  = $service_data['given_name'];
                    $model->title = $service_data['name'];
                    $model->custom_field = $service_data['email'];
                    break;
                case 'github':
                    if ($model === null) {
                        $user = User::findOne([$service => $service_data['login']]);
                    }
                    $model->email = $service_data['email'];
                    $model->name  = $service_data['login'];
                    $model->title = $service_data['login'];
                    $model->custom_field = $service_data['login'];
                    break;

                case 'yandex':
                    if ($model === null) {
                        $user = User::findOne([$service => $service_data['default_email']]);
                    }
                    $model->email = $service_data['default_email'];
                    $model->name  = $service_data['login'];
                    $model->title = $service_data['display_name'];
                    $model->custom_field = $service_data['default_email'];
                    break;
            }

            if ($model === null && $user !== null) {
                Az::$app->user->login($user, 3600);
                Az::$app->response->cookies->remove('oauth_action');
                Az::$app->response->cookies->remove('service_name');
                Az::$app->response->cookies->remove('service_data');

                return $this->urlRedirect(Az::$app->homeUrl);
            }

            return $this->urlRedirect(['core/register']);
        }

        return $model;

    }


    #endregion

    #region PassReset

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     *
     * @see https://gist.github.com/tylerhall/521810
     *
     * @param $length
     *
     * @return string
     */


//     public function

    public static function generate($length)
    {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        return $password;
    }


    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }


    #endregion

    /**
     *
     *
     * RBAC Components
     */


    #region Confirm

    public function sendConfirmMail($code, $user_id, $email)
    {


    }

    #endregion

    public function checkUser()
    {
        $cookies = Yii::$app->response->cookies;

        if ($cookies->has('service_name') && $cookies->has('service_data')) {
            $service = $cookies->getValue('service_name');
            $service_data = Json::decode($cookies->getValue('service_data'));

            switch ($service) {
                case 'google':
                    $user = User::findOne([$service => $service_data['email']]);
                    break;
                case 'yandex':
                    $user = User::findOne([$service => $service_data['default_email']]);
                    break;
                case 'github':
                    $user = User::findOne([$service => $service_data['login']]);
                    break;
            }

            if ($user !== null) {
                Az::$app->user->login($user, 3600);
                $this->cookieGet()->remove('oauth_action');
                $this->cookieGet()->remove('service_name');
                $this->cookieGet()->remove('service_data');

                return $this->urlRedirect(Az::$app->homeUrl);
            }

            $this->urlRedirect(['core/register']);

        }

    }

    public function fillRegisterModel(AuthRegisterForm $model)
    {
        $cookies = Yii::$app->response->cookies;

        if ($cookies->has('service_name') && $cookies->has('service_data')) {
            $service = $cookies->getValue('service_name');
            $service_data = Json::decode($cookies->getValue('service_data'));
            switch ($service) {
                case 'google':
                    $model->email = $service_data['email'];
                    $model->name = $service_data['given_name'];
                    $model->title = $service_data['name'];

                    $custom_field = $service_data['email'];
                    break;
                case 'github':
                    $model->email = $service_data['email'];
                    $model->name = $service_data['login'];
                    $model->title = $service_data['login'];

                    $custom_field = $service_data['login'];
                    break;
                case 'yandex':
                    $model->email = $service_data['default_email'];
                    $model->name = $service_data['login'];
                    $model->title = $service_data['display_name'];
                    $custom_field = $service_data['default_email'];
                    break;
            }
        }

        return $model;

    }

    /**
     *
     * Function  assignDefaultRole
     * @param string $name
     * @param User $user
     */
    public function defaultRole(&$user, string $name)
    {
        $user->role = $name;

        if (!$user->save())
            $this->notifyError('Cannot Assign Default Role', $user->id);

    }


    public function verify(&$user)
    {
        /** @var Models $user */

        if ($user->verified_email)
            return true;

        if ($boot->env('verifyEmail') && !$user->verified_email) {
            $user->verify_code = random_int(100000, 999999);
            $user->verified_email = false;

            $host = $this->urlGetBase();

            $text = "<b>This is email confirmation message.<br />Please click to follow link for confirmation:<br> <a href='" . $host . "/cores/auth/verify.aspx?code=" . $user->verify_code . "&user_id=" . $user->id . "'>" . $host . "/cores/auth/verify.aspx?code=" . $user->verify_code . "&user_id=" . $user->id . "</a></br>";

            $data = [
                'text' => $text,
            ];

            $this->mailAll('Подтверждение регистрация', $user->id, 'test', $data, $file = "");
        } else {
            $user->verify_code = 0;
            $user->verified_email = true;
        }

        return $user->save();
    }

}

