<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\webs;


use yii\base\Exception;
use yii\base\UserException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;

class Verify extends ZFrame
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


        /** @var ZView $this */

        $user_id = $this->httpGet('user_id');
        $code = $this->httpGet('code');

        $user = User::findOne([
            'id' => $user_id,
            'verify_code' => $code
        ]);

        if ($user === null)
            return null;

        $user->configs->rules = [
            [validatorSafe]
        ];
        if ($user !== null) {
            $user->verified_email = true;
            if ($user->save()) {
                Az::$app->cores->auth->login($user);
                $url = ZUrl::to($boot->env('redirectAfterVerify'));
                return $this->urlRedirect($url);
            }
        }
    }
}
