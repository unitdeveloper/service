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


class Ioc extends ZFrame
{


}

