<?php

/**
 * Author: Xolmat
 * Date:    07.06.2020
 */

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


class demoAmmo extends ZFrame
{
    ##Vars


    ###region TestCase
    public function test(){
        return 'salom';
    }
    ###endregion TestCase
    ###region SortCase
    public function sort(){
        return 'hello world';

    }
    ###endregion SortCase

}
