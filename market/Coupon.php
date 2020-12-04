<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Google\ApiCore\OperationResponse;
use yii\caching\TagDependency;
use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\shop\ProductItem;
use zetsoft\dbitem\shop\PropertyItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\models\page\PageAction;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\user\UserCompany;

use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopCoupon;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\menu\Menu;
use zetsoft\models\menu\MenuImage;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\Terrabayt;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZDynaWidget;
use zetsoft\widgets\incores\ZMCheckboxGroupWidget;
use zetsoft\widgets\inputes\ZHCheckboxButtonGroupWidget;
use zetsoft\widgets\navigat\ZGAccordionWidget;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\returnArgument;
use function Spatie\array_keys_exist;


class Coupon extends ZFrame
{
    public function test()
    {
         //$this->getCouponByStatusTest(); there is a mistake in getCouponByStatus() function related to database
        //$this->getCouponListTest(); there is a mistake in getCouponList() function related to database
        $this->getCouponByCodeTest();
    }

#region init

    public function init()
    {

        parent::init();
    }



#endregion

#region getCouponByStatus
    /**
     * Accept coupon status
     * Return coupon list
     * Function  getCouponList
     * @param null $status
     */
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows coupons by its status
    public function getCouponByStatusTest()
    {
        $status = null;
        $result = Az::$app->market->company->getCouponBySatus($status);
        vd($result);
    }

    public function getCouponBySatus($status = null)
    {
        if ($status === null) {
            return [];
        }
        $coupon = ShopCoupon::find()->where([
            'status' => $status
        ])->all();
        return $coupon;
    }
#endregion




    /**
     *
     * Function  getCuponList
     * @param null $cupon_id
     * @param null $type
     */

     #region GetCouponList
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows list of coupons
    public function getCouponListTest() {
        $coupon_id = null;
        $type = null;
        $result = Az::$app->market->coupon->getCouponList($coupon_id, $type);
        vd($result);
    }

    public function getCouponList($coupon_id = null, $type = null)
    {
        if ($type !== null) $type = strtolower($type);
        if ($coupon_id === null) {
            $coupon = ShopCoupon::find()->all();
        }


    }
#endregion
#region GetCouponByCode
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows coupons of its codes
    public function getCouponByCodeTest() {
        $coupon_code = null;
        $result = Az::$app->market->coupon->getCouponByCode($coupon_code);
        vd($result);
    }
    public function getCouponByCode($coupon_code = null)
    {
        $coupon = ShopCoupon::find()->where(
            [
                'code' => $coupon_code
            ]
        )->one();
        return $coupon;
    }

#endregion


}
