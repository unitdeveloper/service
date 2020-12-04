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
use zetsoft\models\shop\ShopCourier;
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


class Courier extends ZFrame
{


    public function placeAdressTest()
    {

        $shop_courier = ShopCourier::findOne(23);
        $a = Az::$app->market->courier->placeAdress($shop_courier);

        vdd($a);

    }

    /**
     *
     * Function  placeAdress
     * @param ShopCourier $shop_courier
     */
    public function placeAdress($shop_courier)
    {
        $placeRegions = $shop_courier->place_region_ids;
        return ZArrayHelper::getValue($placeRegions, 0);

        if (\Dash\count($placeRegions) > 1)
            return $placeRegions;
        else
            return ZArrayHelper::getValue($placeRegions, 0);

    }


}
