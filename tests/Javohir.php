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
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopReview;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;


class Javohir extends ZFrame
{


#region init

    public function init()
    {
        parent::init();
    }

#endregion


#region getByScroll
    public function getByScroll($page_num, $range)
    {

        if (isset($page_num)) {
            $page_num = $page_num;
        } else {
            $page_num = 1;
        }

        $pageStart = ($page_num - 1) * $range;

        $data = User::find()->limit($pageStart, $range)->all();

        return json_encode($data);

    }

    public function currencyAbbr()
    {
        $core_catalog = new ShopCatalog();
        $currencies = $core_catalog->_currency;
        $currencyLowercase = [];
        foreach ($currencies as $abbr => $currency) {
            $currencyLowercase [] = strtolower($abbr);
        }

        return $currencyLowercase;
    }


}
