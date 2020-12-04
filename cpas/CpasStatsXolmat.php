<?


/**
 *
 *
 * Author:  Xolmat Ravshanov
 * Date:    9/20/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cpas;


use DateTime;
use yii\rbac\DbManager;
use zetsoft\former\cpas\CpasStatsForm;
use zetsoft\former\cpas\CpasTrackForm;
use zetsoft\former\stat\StatHistoryForm;
use zetsoft\models\cpas\CpasLand;
use zetsoft\models\cpas\CpasSource;
use zetsoft\models\cpas\CpasTracker;
use zetsoft\models\cpas\CpasTeaser;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Safe\ftp_alloc;
use function zetsoft\apisys\edit\returnn;

class CpasStatsXolmat extends ZFrame
{

   #region Vars


   #endregion

   #region Cores

   #endregion


   public function timeStats($minDate, $maxDate, $status){


   }

}

