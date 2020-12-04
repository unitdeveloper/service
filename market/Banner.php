<?php

/**
 * Author:  AzimjonToirov
 *
 */

namespace zetsoft\service\market;


use Exception;
use zetsoft\dbitem\shop\BrandItem;
use zetsoft\models\shop\ShopBanner;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;

//use zetsoft\models\core\ShopCatalog;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;


class Banner extends ZFrame
{


    #region init

    public function init()
    {

        parent::init();
    }

    #endregion

    /**
     *
     * Function  getBannerPhotos
     * @param null $market_id
     * @return  array
     * @throws Exception
     *
     * @author AzimjonToirov
     *
     * Market Id berilsa usha marketga banner chiqarib beradi swiperga
     */
    public function getBannerPhotos($market_id = null)
    {
        $currentLang = Az::$app->language;

        $imageUrl = '/tempz/eyuf/ShopBanner/image/0/';

        $imagesWithFullUrl = [];

        $images = ShopBanner::find()
            ->where([
                'user_company_id' => $market_id,
                'lang' => $currentLang
            ])
            ->one();

        /** @var ShopBanner $images */
        if ($images) {
            foreach ($images->image as $key => $image) {
                $imagesWithFullUrl['photos'][] = $imageUrl . $image;
            }
            $imagesWithFullUrl['href'] = $images->link;
        }

        return $imagesWithFullUrl;
    }
}



