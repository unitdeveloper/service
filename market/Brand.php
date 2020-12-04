<?php

/**
 * Author:  Daho
 * Date:    14.05.2020
 *
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\BrandItem;
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


class Brand extends ZFrame
{


    #region init

    public function init()
    {

        parent::init();
    }

    public function test()
    {
        //$this->companyListTest();
        //  $this->GetUserListTest(); Error:Element
        // $this->GetElementIdTest();
        $this->GetPriceStringTest();
        //$this->GetNewOrderItemsTest();
        //$this->GetBrandListTest();
    }

    #endregion

    #region Company
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows company list by $productId , $options
    public function companyListTest()
    {
        $productId = 653;
        $options = ["1", "2", "5", "8", "204", "206", "207"];
        $val = $this->companyList($productId, $options);
        vd($val);
//         $val = []
        //   ZTest::assertEquals([],$val);
    }

    public function companyList($productId = null, $options = [])
    {
        //vdd($options);
        /*return Az::$app->market->product->product(1);*/
        Az::start(__FUNCTION__);
        if ($productId === null) return [];

        $elementId = $this->getElementId($productId, $options);
        //return $elementId;
        /** @var ShopCatalog $company */
        $company = ShopCatalog::find()
            ->where([
                'shop_element_id' => $elementId
            ])
            ->all();
        return $company;
    }
    #endregion
    #region UserList

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows
    public function GetUserListTest()
    {
        $elements = 55;
        $data = $this->getUserList($elements);
        vd($data);
    }

    public function getUserList($elements)
    {
        Az::start(__FUNCTION__);
        $user = [];
        foreach ($elements as $element) {
            /** @var User $comp */
            $comp = User::findOne($element->user_id);
            $user[$comp->id] = $comp->name;
        }
        return $user;
    }


    #region GetElementId

    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows elements by   $productId ,$options
    public function GetElementIdTest()
    {
        $productId = 653;
        $options = ["1", "2", "5", "8", "204", "206", "207"];
        $data = $this->getElementId($productId, $options);
        vd($data);
    }

    public function getElementId($productId, $options)
    {
        /** @var ShopElement $element */
        Az::start(__FUNCTION__);
        $elements = ShopElement::find()
            ->where([
                'shop_product_id' => $productId,
                /*'shop_option_ids' => $options*/
            ])
            ->all();
        /* return $elements;*/
        foreach ($elements as $element) {
            if ($element->shop_option_ids === $options)
                return $element->id;
        }
    }


    #endregion

    #region price
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows
    public function GetPriceStringTest()
    {
        $price = [];
        $currensy = '$';
        $data = $this->getPriceString($price, $currensy);
        vd($data);
    }

    public function getPriceString($price = [], $currensy = '$')
    {
        Az::start(__FUNCTION__);
        if ($this->isCLI()) {
            $product = Az::$app->market->product->product(11);
            $price = $product->price_old;
            $currensy = $product->currency;
        }

        if (empty($price)) return '';


        $min = \Dash\Curry\min($price);
        $max = \Dash\Curry\max($price);
        if (!$min || !$max) return "";

        if ($min === $max) return $max . $currensy;

        return $min . '-' . $max . $currensy;
    }
    #endregion

    #region market


    #endregion

    #region History

    /**
     *
     * Function  getActiveOrderItems
     * Statusi new bo'lgan orderItemlarni qaytaradi
     * @param $user_id
     * Orderlari qaytariladigan user id si
     * @author Daho
     *
     */
    #region getNewOrderItems
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows
    public function GetNewOrderItemsTest()
    {
        $user_id = null;
        $data = $this->getNewOrderItems($user_id);
        vd($data);
    }

    public function getNewOrderItems($user_id = null)
    {
        /** @var ShopOrder $orders */
        Az::start(__FUNCTION__);
        $orders = ShopOrder::find()
            ->where([
                'status' => ShopOrder::status['new'],
                'user_id' => $user_id
            ])
            ->all();

        $ids = [];
        /** @var ShopOrder $order */
        foreach ($orders as $order)
            foreach ($order->core_order_item_ids as $id)
                $ids[] = $id;

        $orderItem = new ShopOrderItem();
        $orderItem->query = ShopOrderItem::find()
            ->where([
                'id' => $ids
            ]);

        return $orderItem;
    }

    #endregion

    #region Statistics

    #endregion
    #region  BrandItem
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function shows
    public function GetBrandListTest()
    {
        $data = $this->getBrandList();
        vd($data);
    }

    public function brandList()
    {
        Az::start(__FUNCTION__);
        $brands = ShopBrand::find()->all();

        $brandsList = [];

        //vdd($brands);
        foreach ($brands as $brand) {

            $brandItem = new BrandItem();
            $brandItem->id = $brand->id;
            $brandItem->name = $brand->name ?? '';

            $brandItem->image = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$brand->id/" . ZArrayHelper::getValue($brand->image, 0) ?? '';

            $brandItem->url = '/shop/user/filter-common/main.aspx?brand_id=' . $brand->id;

            $brandsList[] = $brandItem;
        }

        return $brandsList;

    }


    public function getBrand($id)
    {
        $brandItem = new BrandItem();
        if (!empty($id)){
            $brand = ShopBrand::find()
                ->where([
                    'id' => $id
                ])->one();
            $brandItem->id = $brand->id;
            $brandItem->name = $brand->name ?? '';
            $brandItem->image = '/uploaz/' . $this->bootEnv('appTitle') . "/ShopBrand/image/$brand->id/" . ZArrayHelper::getValue($brand->image, 0) ?? '';
            return $brandItem;
        }
        else{
            return false;
        }

    }
#endregion
}



