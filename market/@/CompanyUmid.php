<?php

/**
 * Author:  Daho
 * Date:    14.05.2020
 *
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopBrand;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\user\UserCompany;

use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopOptionBranch;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class CompanyUmid extends ZFrame
{

    #region init
    public $shop_elements;
    public $users;
    public $core_orders;
    public $core_order_items;
    public $user_company_elements;
    public const cart_type = [
        'add' => 'add',
        'set' => 'set',
    ];
    
    public function init()
    {
        $this->core_orders = collect(ShopOrder::find()->all());
        $this->core_order_items = collect(ShopOrderItem::find()->all());
        $this->users = collect(User::find()->all());
        $this->shop_elements = collect(ShopElement::find()->all());
      //*//$this->user_company_elements = collect(ShopCatalog::find()->all());*/
        parent::init();
    }

    public function test(){
        //$this->getCompanyList(19);

        vdd($this->getNewOrderItems(47));
    }
    #endregion

    #region Company
    public function getCompanyList($productId = null, $options = [])
    {
        //vdd($options);
        /*return Az::$app->market->product->product(1);*/
        if ($productId === null) return [];

        $elementId = $this->getElementId($productId, $options);
        //return $elementId;

        //!!!mavjud bo'lmagan class
        /** @var ShopCatalog $company */
       /* $company = ShopCatalog::find()
                ->where([
                    'shop_element_id' => $elementId
                ])
                ->all();*/

        $company = $this->user_company_elements
            ->where('shop_element_id' , $elementId)
            ->all();

        
        return $company;
    }


    public function getUserList($elements)
    {
        $user = [];
        foreach ($elements as $element) {
            /** @var User $comp */
            //$comp = User::findOne($element->user_id);
            $comp = $this->users
            ->where('id', $element->user_id)
            ->first();
            $user[$comp->id] = $comp->name;
        }
        return $user;
    }

    public function getElementId($productId, $options)
    {
        /** @var ShopElement $element */
        /*$elements = CoreElement::find()
            ->where([
                'shop_product_id' => $productId,

                !!!'shop_option_ids' => $options!!!

            ])
            ->all();*/
        $elements = $this->shop_elements
        ->where('shop_product_id', $productId);
        /* return $elements;*/
        foreach ($elements as $element) {
            if ($element->shop_option_ids === $options)
                return $element->id;
        }
    }


    #endregion

    #region price
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
    public function getNewOrderItems($user_id = null)
    {

        /** @var ShopOrder $orders */
       /* $orders = ShopOrder::find()
            ->where([
                'status' => ShopOrder::status['new'],
                'user_id' => $user_id
            ])
            ->all();*/

         $orders = $this->core_orders
         ->where('status', 'new')
         ->where('user_id', $user_id)
         ->all();
       
        $ids = [];
        /** @var ShopOrder $order */
        foreach ($orders as $order)
            foreach ($order->core_order_item_ids as $id)
                $ids[] = $id;

        $orderItem = new ShopOrderItem();
        /*$orderItem->query = ShopOrderItem::find()
                        ->where([
                            'id' => $ids
                        ]);*/

        $orderItem->query = $this->core_order_items
        ->whereIn('id', $ids)
        ->first();

        return $orderItem;
    }

    #endregion

}



