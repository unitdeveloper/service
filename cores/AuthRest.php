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


use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rbac\DbManager;
use yii\web\UnauthorizedHttpException;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\kernels\ZFrame;


/**
 * Class ZAuth
 * @package zetsoft\service
 *
 * @property DbManager $auth
 * @property User $identity
 */
class AuthRest extends ZFrame
{
    public function basic()
    {
        [$username, $password] = Az::$app->getRequest()->getAuthCredentials();

        if ($username !== null && $password !== null) {
            if ($this->userIdentity()->id === 0) {
                $form = new AuthLoginForm();
                $form->login = $username;
                $form->password = $password;

                $identity = Az::$app->cores->auth->login($form);
            } else {
                $identity = true;
            }
        } else {
            $identity = null;
        }


        if ($identity === null) {
            $this->handle403();
        }

        if ($identity) {
            return true;
        }

        $this->handle401();

        return false;
    }

    public function param()
    {
        $tokenParam = 'access-token';
        $accessToken = Az::$app->getRequest()->get($tokenParam);

        if ($accessToken === null) {
            $this->handle401();
        }
        if (is_string($accessToken)) {
            $identity = $this->checkUserByAuthKey($accessToken);
        }
        if (!$identity) {
            $this->handle401();
        }

        return $identity;
    }

    public function basicParam()
    {
        $email = $this->httpGet('email');
        $password = $this->httpGet('password');
        if ($this->emptyVar($email) || $this->emptyVar($password))
            $this->handle403();
        $identity = $this->getUserByParam();

        if ($identity === null) {
            $this->handle401();
        }
        return true;
    }

    public function bearer()
    {
        $header = 'Authorization';
        $pattern = '/^Bearer\s+(.*?)$/';

        $authHeader = Az::$app->getRequest()->getHeaders()->get($header);

        if ($authHeader !== null) {
            if ($pattern !== null) {
                if (preg_match($pattern, $authHeader, $matches)) {
                    $authHeader = $matches[1];
                } else {
                    $this->handle401();
                }
            }

            $identity = $this->checkUserByAuthKey($authHeader);
        } else {
            $identity = null;
        }

        if ($identity === null) {
            throw new ZException(Az::l('Credentials not given'));
        }

        if ($identity) {

            return true;
        }

        $this->handle401();

        return false;
    }

    private function handle401()
    {
        throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
    }

    private function handle403()
    {
        throw new ZException(Az::l('Credentials not given'));
    }

    #endregion

    #region Auth_Key

    public function getUserByParam()
    {
        $email = $this->httpGet('email');
        $password = $this->httpGet('password');
        return User::find()->where(['email' => $email, 'password' => Az::$app->cores->auth->hashGet($password)])->one();
    }

    /**
     *
     * Function  loginByAccessToken
     * @param $token
     * @return  boolean
     */
    public function checkUserByAuthKey($token): bool
    {
        $token = ZHTML::encode($token);
        if (User::find()->where(['auth_key' => $token])->exists()) {

            $this->sessionSet('auth_key', $token);

            return true;
        }
        return false;
    }

    #endregion

}

