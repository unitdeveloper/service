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
use zetsoft\models\core\CoreTransact;
use zetsoft\models\user\User;
use zetsoft\system\actives\ZModel;
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
class Tranz extends ZFrame
{

    public function save($modelName, $session)
    {
        $models = explode('|', $modelName);
        foreach ($models as $model) {
            $modelName = $this->bootFull($model);
            /** @var CoreTransact $trans */
            $trans = CoreTransact::find()->where([
                'models' => $modelName,
                'session' => $session
            ])->one();
            if ($trans) {
                $trans = CoreTransact::find()->where([
                    'models' => $modelName,
                    'session' => $session
                ])->one();
                if ($trans) {
                    /** @var ZModel $model */
                    $attr = $trans->value;
                    unset($attr['id']);
                    if ($trans->is_new) {
                        $model = new $trans->models();
                        $model->setAttributes($attr);
                    } else {
                        $model = $trans->models::findOne($trans->modelId);
                        $model->setAttributes($attr);
                    }
                    $model->save();
                    $trans->delete();
                }
            }
        }
    }

    public function flush($modelName, $session)
    {
        $models = explode('|', $modelName);
        foreach ($models as $model) {
            $modelClass = $this->bootFull($model);
            /** @var CoreTransact $trans */
            $trans = CoreTransact::find()->where([
                'models' => $modelClass,
                'session' => $session
            ])->one();
            if ($trans)
                $trans->delete();
        }
        return true;
    }

}

