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
use yii\web\UnauthorizedHttpException;
use zetsoft\dbcore\ALL\CoreRoleCore;
use zetsoft\dbcore\ALL\UserCore;
use zetsoft\dbitem\core\ServiceItem;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\former\auth\AuthRegisterForm;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\core\CoreRole;
use zetsoft\models\core\CoreSession;
use zetsoft\models\core\CoreSessionUser;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use function Amp\File\exists;
use function Dash\Curry\find;


/**
 * Class ZAuth
 * @package zetsoft\service
 *
 * @property DbManager $auth
 * @property User $identity
 */
class Auth extends ZFrame
{


    #region Vars
    public $loginUrl = [
        'site/login'
    ];

    public const duration = 3600 * 24 * 1; // 1 Day
    public $auth;
    private $singleUser = false;
    /* @var AuthLoginForm $model */
    public $model;

    /* @var User $_identity */
    private $_identity;


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
            $user = $this->identity;

        $photos = $user->photo;
        $return = [];

        if (empty($photos) || $photos !== null)
            $return[] = '/imagez/default/user/avatar_circle_blue.png';
        else {

            foreach ($photos as $key => $photo) {
                $path = "/uploaz/User/photo/$user->id/$photo";
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
        $exists = file_exists(Az::getAlias('@webroot' . $img));

        //  vd($exists);
        if (!$exists)
            $img = '/imagez/default/user/avatar_circle_blue.png';

        return $img;
    }

    public function getIdentity()
    {
        global $boot;

        if ($this->_identity !== null)
            return $this->_identity;

        

        switch (true) {
            //case $this->moduleId === 'api':

            //    break;
            case !$boot->isCLI():
                if (!$this->isGuest()) {
                    $this->_identity = Az::$app->cores->auth->user();
                }
                if ($this->_identity === null) {
                    $this->_identity = new User();
                    $this->_identity->id = 0;
                    $this->_identity->name = Az::l('Гость');
                    $this->_identity->role = 'user';
                    $this->_identity->user_company_id = 0;
                }
                break;
            /**
             * For CMD
             */
            case $boot->isCLI():
                $userID = $boot->env('userID');
                $this->_identity = new User();
                $this->_identity->id = (!empty($userID)) ? $userID : 1;
                $this->_identity->name = 'Cmd';
                break;

        }
        return $this->_identity;
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
    #ShakhrizodNurmukhammadov
    /**
     *
     * Function  login
     * @param $user
     * @param float|int $duration
     * @return  bool
     * @throws \Exception
     */
    public function login(&$user, $duration = self::duration)
    {
        global $boot;
        if ($this->isEmail($user->login)) {
            $newUser = User::find()->where([
                'password' => $this->hashGet($user->password),
                'email' => $user->login
            ])->one();

        } else if ($this->isPhone($user->login)) {
            $newUser = User::find()->where([
                'password' => $this->hashGet($user->password),
                'phone' => $user->login
            ])->one();
        } else {
            return false;
        }


        /** @var User $newUser */
        if ($newUser !== null) {
            if ($newUser->verified_email || $newUser->verified_phone || !$this->bootEnv('verify')) {
                if (!$this->singleUser) {
                    if ($boot->env('auth_keyExpire')) {
                        $newUser->auth_key = $this->generate(64, true);
                    }
                    $newUser->lastseen = date('Y-m-d H:i:s');
                    $newUser->status = 'online';
                    $newUser->configs->rules = [
                        [validatorSafe]
                    ];
                    $newUser->save();
                    $this->setLogin($newUser->id, $duration);
                    $this->_identity = $newUser;
                    return true;
                }
                $this->notifyError(Az::l('User already signed in'));
                return false;
            } else {
                if ($newUser->email) {
                    $this->sessionSet('registerUserEmail', $newUser->id);
                    return 'NeedEmail';
                }
                if ($newUser->phone) {
                    $rand = random_int(1000, 9999);
                    $this->sessionSet('phoneRegisterCode', $rand, 300, $user->id);
                    $this->sessionSet('phoneRegister', (int)$user->phone, 3600, $user->id);
                    return 'NeedPhone';
                }

            }
        }


        return false;
    }

    public function logout()
    {
        if (Az::$app->cores->session->delete('login')) {
            $this->_identity = null;
            return true;
        } else
            return false;
    }


    public function userCheck($value)
    {
        return User::find()->where(['email' => $value])->orWhere(['phone' => $value])->one();
    }

    public function user()
    {
        if ($this->_identity === null) {
            $session = $this->sessionGetAll('login');
            if ($session)
                return User::findOne($session->user_id);
            else
                return null;

        }
        return $this->_identity;
    }

    public function isGuest()
    {
        /** @var CoreSession $login */
        if ($login = $this->sessionGetAll('login')) {
            return !User::find()->where(['id' => $login->user_id])->exists();
        }
        return true;
    }

    public function register($model)
    {


     /*       $exisrts = User::find()->where([
                'email' => strip_tags($model->login),
            ])->orWhere(['phone' => strip_tags($model->login)])->exists();*/




        $exisrts = User::find()->where([
            'email' => strip_tags($model->login),
        ])
            ->exists();


        // vdd ($model);
        /** @var User $user */
        if (!$exisrts ) {

            $user = new User();
            $user->email = $this->isEmail($model->login) ? $model->login : null;
            $user->password = $model->password;
            $user->auth_key = $this->generate(64, true);
            $user->phone = $this->isPhone($model->login) ? $model->login : null;
            $user->role = $model->role;
            $user->verified_email = false;
           $data = $user->save();

            return $this->verify($user);
        }

        $this->notifyError('This email is already in use. Try another', $model->errors);
        return false;
    }

#endregion

    public function getUserByToken($token)
    {
        if ($token !== null) {
            return User::find()
                ->where(['auth_key' => $token])
                ->one();
        }
        return false;
    }

    public function generate($length = 64, $auth_key = false)
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
        if ($auth_key) {
            if (User::find()->where(['auth_key' => $password])->exists()) {
                $this->generate(64, true);
            }
        }
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

    public function verify(User $user)
    {
        global $boot;

        if ($user->verified_email)
            return true;
            
        if ($boot->env('verify')) {
            if (!$user->verified_email && $user->email) {
                $user->verify_code = $this->hashGet(random_int(1000000, 9999999));
                $user->save();
                $this->sessionSet('registerUserEmail', $user->id);
                //$this->sendEmail($user->email, $user->verify_code);
                Az::$app->utility->swiftMailer->verifyWithEmail($user->email, $user->verify_code);
                return true;
            }

            if (!$user->verified_phone && $user->phone) {
                $rand = random_int(1000, 9999);
                
                if (Az::$app->sms->eskiz->sendSms($user->phone, $rand)) {
                    $user->verify_code = $rand;
                    $user->save();
                    $this->sessionSet('phoneRegisterCode', $rand, 300, $user->id);
                    $this->sessionSet('phoneRegister', (int)$user->phone, 3600, $user->id);
                    return true;
                }
            }
        }
        $user->verify_code = 0;
        $user->verified_email = true;
        $user->save();
        $this->_identity = $user;
        return $this->setLogin($user->id);
    }

    public function resendPhoneCode($phone = null)
    {
        if ($phone === null)
            $phone = $this->sessionGetAll('phoneRegister');

        $rand = random_int(1000, 9999);
        if (Az::$app->sms->eskiz->sendSms((int)$phone->value, $rand)) {
            $this->sessionSet('phoneRegisterCode', $rand, 300, $phone->user_id);
            return true;
        }
        return false;
    }

    /**
     *
     * Function  resendEmail
     * @param null $email
     * @param string $text
     * @return  bool
     * @throws \Exception
     */
    public function resendEmail($email = null, $text = ''): bool
    {
        if ($email === null)
            $email = $this->sessionGet('registerUserEmail');

        $user = User::findOne($email);
        //vdd($this->isEmail($user->email));
        $rand = $this->hashGet(random_int(1000000, 9999999));
        $user->verify_code = $rand;
        if ($this->isEmail($user->email)) {

            if ($this->sendEmail($user->email, $rand)) {
                $user->save();
                return true;
            }
        }
        return false;
    }

    public function sendVerifyEmail($to, $verify_code)
    {
        $messageNote = 'Пожалуйста нажмите на ссылку чтобы активировать';
        $mail = Az::$app->auth->swift;
        $user_id = $this->sessionGet('registerUserEmail');
        $mail->to = $to;
        $mail->subject = 'Verify email code';
        $host = $this->urlGetBase();
        $mail->body = '<html>' .
            ' <body>' .
            '<p><a href="' . $host . "/cpas/user/user-auth/verify/verify-email.aspx?id=$user_id&code=$verify_code" . '">' . 'Пожалуйста нажмите на ссылку чтобы активировать аккаунт' . '</a></p>' .
            ' </body>' .
            '</html>';
        $mail->body_extension = 'text/html';
        $mail->run();
    }

    public function sendEmail($to, $verify_code)
    {
        $user_id = $this->sessionGet('registerUserEmail');
        $host = $this->urlGetBase();
        $body = '<html>' .
            ' <body>' .
            '<p><a href="' . $host . $this->bootEnv('verifyUserUrl') . "?id=$user_id&code=$verify_code" . '">' . Az::l('Пожалуйста нажмите на ссылку чтобы активировать аккаунт') . '</a></p>' .
            ' </body>' .
            '</html>';

        $request = Az::$app->mailer->compose()
            ->setTo($to)
            ->setSubject('Verify email')
            ->setHtmlBody($body)
            ->send();
        //vdd('aa');
        //vdd($request);
        return $request;
    }

    public function sendForgetEmail($email)
    {
        $user = User::find()
            ->where([
                'email' => $email
            ])
            ->one();
        //vdd($user);
        if (!$user)
            return false;

        $user->verify_code = $this->hashGet(random_int(1000000, 9999999));
        $user->save();

        //$user_id = $this->sessionGet('registerUserEmail');
        $host = $this->urlGetBase();
        $body = '<html>' .
            ' <body>' .
            '<p><a href="' . $host . '/core/restoreEmail/restore.aspx' . "?code=$user->verify_code" . '">' . Az::l('Пожалуйста нажмите на ссылку чтобы восстановление пароля') . '</a></p>' .
            ' </body>' .
            '</html>';

        $request = Az::$app->mailer->compose()
            ->setTo($email)
            ->setSubject('Reset email')
            ->setHtmlBody($body)
            ->send();

        return $request;
    }

    public function restorePassword($code, $new_password)
    {
        $user = User::find()
            ->where([
                'verify_code' => $code
            ])
            ->one();

        if (!$user)
            return false;

        $user->verify_code = 0;
        $user->password = $new_password;

        if ($user->save()) {
            $this->setLogin($user->id);
            return true;
        }


    }

    public function setLogin(int $user_id, int $duration = self::duration): ?bool
    {
        $this->sessionDel('login');
        return Az::$app->cores->session->set('login', true, $duration, $user_id);
    }


    public function checkScholar($value)
    {
        return EyufScholar::find()->where(['email' => $value])->one();
    }


    #region Rest

    /**
     *
     * Function  authLogin
     * @param string $method
     * @return  array|bool|mixed|string|\zetsoft\system\actives\ZActiveQuery|null
     * @throws \Exception
     */
    public function authLogin($method = 'POST')
    {
        global $boot;
        switch ($method) {
            case 'POST':
                $login = $this->httpPost('login');
                $password = $this->httpPost('password');
                break;
            case 'GET':
                $login = $this->httpGet('login');
                $password = $this->httpGet('password');
                break;
            default:
                return false;
                break;
        }
        if ($login && $password) {
            $q = User::find()->where(['password' => Az::$app->cores->auth->hashGet($password)]);
            if ($this->isPhone($login)) {
                $q->andWhere(['phone' => $login]);
            }
            if ($this->isEmail($login)) {
                $q->andWhere(['email' => $login]);
            }
            /** @var User $model */
            $model = $q->one();
            if ($model) {
                if ($boot->env('auth_keyExpire')) {
                    $model->auth_key = Az::$app->cores->auth->generate(64, true);
                    $model->save();
                }
                $this->_identity = $model;
                $this->setLogin($model->id);
                return $model->auth_key;
            }
            return false;
        }
        return false;
    }
    #endregion
}

