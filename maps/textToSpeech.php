<?php

/**
 * Author:  Zoirjon Sobirov
 * @license  Zoirjon Sobirov
 * linkedIn: https://www.linkedin.com/in/zoirjon-sobirov/
 * Telegram: https://t.me/zoirjon_sobirov
 * @copyright zhead, zstart, zend
 */

namespace zetsoft\service\maps;


use yii\helpers\ArrayHelper;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\ware\Ware;

use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class textToSpeech  extends ZFrame
{

    #region Test

    public function test()
    {
       return 'hello';
    }
    #endregion

    #region Main
    public  function synTextToSpeech(){
       
    }
    #endregion
}
