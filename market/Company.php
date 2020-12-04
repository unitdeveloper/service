<?php

/**
 * Author:  Daho
 * Date:    14.05.2020
 *
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\CompanyCardItem;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopPolicy;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\UserCompany;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Company extends ZFrame
{

    public function test()
    {
        //$this->getCompanyListTest();
        //$this->getUserListTest();
        //$this->getCompanyByIdTest();
        //$this->getElementIdTest();
        //$this->getPriceStringTest(); there is a mistake in getPriceString() function
        //$this->getNewOrderItemsTests(); there is a mistake in getNewOrderItems() function
    }

    #region init

    public function init()
    {

        parent::init();
    }

    #endregion

    #region for Company
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows list of companies
    public function getCompanyListTest()
    {
        $productId = 348;
        $options = [9, 5, 15];
        $result = Az::$app->market->company->getCompanyList($productId, $options);
        vd($result);
    }

    public function getCompanyList($productId, $options)
    {
        //vdd($options);
        /*return Az::$app->market->product->product(1);*/
        if ($productId === null) return [];

        $elementId = $this->getElementId($productId, $options);
        //return $elementId;
        /** @var ShopCatalog $catalog */
        $catalogs = ShopCatalog::find()
            ->where([
                'shop_element_id' => $elementId
            ])
            ->all();

        $markets = [];
        /** @var ShopCatalog $catalog */
        /*foreach ($catalogs as $catalog){
             $market = new CompanyItem();
             $company = UserCompany::findOne($catalog->user_company_id);
             $market->amount = $catalog->amount;
             $market->cart_amount
        }*/

        return $catalogs;
    }

    #endregion

    #region for getProductByCompanyId
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows products by its company id
    public function getProductByCompanyIdTest()
    {
        $company = null;
        $status = null;
        $result = Az::$app->market->company->getProductByCompanyId($company, $status);
        vd($result);
    }
  // @Dishkan2000 : Kim yozgan pastaki function ni? $company, $status bula ishlatilmayabtiku

    public function getProductByCompanyId($company, $status)
    {
        $products = ShopProduct::find()->where(
            ['']
        )->all();

    }

    #endregion

    #region for getUserList
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows lists of users by its elements
    public function getUserListTest()
    {
        $elements = [];
        $result = Az::$app->market->company->getUserList($elements);
        vd($result);
    }

    public function getUserList($elements)
    {
        $user = [];
        foreach ($elements as $element) {
            /** @var User $comp */
            $comp = User::findOne($element->user_id);
            $user[$comp->id] = $comp->name;
        }
        return $user;
    }

    #endregion

    #region getCompanyById
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows company by its id
    public function getCompanyByIdTest()
    {
        $id = 1;
        $result = Az::$app->market->company->getCompanyById($id);
        vd($result);
    }

    public function getCompanyById($id)
    {
        return UserCompany::findOne($id);
    }

    #endregion

    #region for getElementId
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows element by its id
    public function getElementIdTest()
    {
        $productId = 28;
        $options = ["1", "2", "5", "8", "204", "206", "207"];
        $result = Az::$app->market->company->getElementId($productId, $options);
        vd($result);
    }

    public function getElementId($productId, $options)
    {
        /** @var ShopElement $element */
        $elements = ShopElement::find()
            ->where([
                'shop_product_id' => $productId,
                /* 'shop_option_ids' => $options*/
            ])
            ->all();
        /* return $elements;*/
        foreach ($elements as $element) {
            if ($element->shop_option_ids === $options)
                return $element->id;
        }
    }

    #endregion

    #region for getPriceString
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows element by its id
    public function getPriceStringTest()
    {
        $price = [];
        $currensy = '$';
        $result = Az::$app->market->company->getPriceString($price, $currensy);
        vd($result);
    }

    public function getPriceString($price = [], $currensy = '$')
    {

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

    #region for getNewOrderItems

    /**
     *
     * Function  getActiveOrderItems
     * Statusi new bo'lgan orderItemlarni qaytaradi
     * @param $user_id
     * Orderlari qaytariladigan user id si
     * @author Daho
     *
     */
    // Dilshod Khudoyarov
    // telegram = @Dishkan2000
    // this function shows new order items
    public function getNewOrderItemsTests()
    {
        $user_id = 110;
        $result = Az::$app->market->company->getNewOrderItems($user_id);
        vd($result);
    }

    public function getNewOrderItems($user_id = null)
    {
        /** @var ShopOrder $orders */
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

}



