<?php

/**
 * @author  AzimjonToirov
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\BrandItem;
use zetsoft\models\shop\ShopBanner;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;

//use zetsoft\models\core\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;
use function Dash\count;


class Breadcrumb extends ZFrame
{


    #region init

    public function init()
    {
        parent::init();
    }

    #endregion

    #region Returns Breadcrumb Items

    /**
     *
     * Function  breadcrumbItems
     * @param null $categoryId
     * @return  array
     *
     * @author AzimjonToirov  BreadCrumbs uchun categoriyaga mos breadcrumb item(array) qaytaradi
     */
    public function breadcrumbItems($categoryId = null)
    {
        $menuItem = Az::$app->market->category->getMenuItem($categoryId, false);

        return $this->returnBreadCrumbItems($menuItem);
    }

    /**
     *
     * Function  breadcrumbItems
     * @param null $categoryId
     * @return  array
     *
     * @author AzimjonToirov  BreadCrumbs uchun categoriyaga mos breadcrumb item(array) qaytaradi
     */
    public function getItemByType($id = null, $type = null)
    {

        if (!empty($id) && $type === 'category')
            $menuItem = $this->returnBreadCrumbItems(Az::$app->market->category->getMenuItem($id, false));
        if (!empty($id) && $type === 'brand')
            $menuItem = ShopBrand::findOne($id);
        if (!empty($id) && $type === 'company')
            $menuItem = UserCompany::findOne($id);

        return $menuItem;
    }

    /**
     *
     * Function  recursive
     * @param $menuItem
     * @param array $items
     * @return  array
     *
     * @author AzimjonToirov
     */
    private function recursive($menuItem, $items = [])
    {
        $items[$menuItem->id] = $menuItem->name;
        if (count($menuItem->items) !== 0)
            return $this->recursive(ZArrayHelper::getValue($menuItem->items, 0), $items);
        else
            return $items;
    }

    /**
     *
     * Function  returnBreadCrumbItems
     * @param $menuItem
     * @return  array
     *
     * @author AzimjonToirov
     */
    private function returnBreadCrumbItems($menuItem)
    {
        if (is_array($menuItem))
            $menuItem = ZArrayHelper::getValue($menuItem, 0);

        return $this->recursive($menuItem);
    }

    #endregion

}



