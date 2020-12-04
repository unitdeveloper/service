<?php


namespace zetsoft\service\tests;

use yii\base\ErrorException;
use yii\data\Pagination;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\Az;
use zetsoft\dbitem\chat\ReviewItem;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;


class randomTest extends ZFrame
{
    ##region Test
    public function test(){
    return 'salom';
    }

    ##endregion Test

    ##region SortObj
    public function sortObj(){
        return 'sort';
    }
    ##endregion SortObj

}
