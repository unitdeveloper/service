<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\dbitem\shop\OrderElementItem;
use zetsoft\dbitem\shop\OrderItem;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Order2 extends ZFrame
{
    /**
     * @var Collection $core_orders
     */
    private $core_orders;

    /**
     * @var Collection $core_order_items
     */
    private $core_order_items;


    #region init

    public function init()
    {
        parent::init();
        $this->core_orders = collect(ShopOrder::find()->asArray()->all());
        $this->core_order_items = collect(ShopOrderItem::find()->asArray()->all());
       
    }

    public function test()
    {
        $this->getOrderList(1, 'new');
        //vdd($this->getUserOrderList());
        $this->getUserOrderList('new');
        $this->getProductBelongsToOrder(15);
        $this->getOrderAddress();
    }

    #endregion

    #region getOrderList
    /**
     * If has not user_id and type return  all order list
     * Otherwise returns existing type and user orders
     * Function  getOrderList
     * @param null $user_id
     * @param null $type
     * @return  mixed
     */
    public function getOrderList($user_id = null, $status = null)
    {

        $type = strtolower($status);
        if ($user_id === null && $status === null) {
            return  ShopOrder::find()->all();
        }
        if ($status === null && $user_id !== null) {
            $user_order_s  = ShopOrder::find()->
            where([
                'user_id' => $user_id
            ])->all();
            
            return $$user_order_s ;
        }
        if ($status !== null && $user_id === null) {
            $user_order_s = ShopOrder::find()->where([
                'status' => $status
            ])->all();
            return $user_order_s;
        }
        $user_order_s = ShopOrder::find()->where([
            'user_id' => $user_id
        ])->andWhere([
            'status' => $status
        ])->all();
        return $user_order_s;
    }







    #endregion
    #region UserOrderList
        public function getUserOrderList($status = null){
            if($status === null)
                return null;

            $user_order_list = [];
            $user_id = $this->userIdentity()->id;
            $orders = $this->getOrderList( 76, $status);
            foreach ($orders as $order){
                $order_item = new OrderItem();
                $order_item->sum = $order->total_price;
                $order_item->created_at = $order->created_at;
                $id = (int)$order->id;
                $order_item->current_order_element_items[] = $this->getProductBelongsToOrder($id);
                $user_order_list[] = $order_item;
            }
            return $user_order_list;
        }
    #endregion


    #region getOrderAddres


        /**
         * Return order address
         * Give order object
         * Function  getOrderAddress
         * @param null $order obj
         * @return  array
         */
        public function getOrderAddress($order = null)
        {
            if ($order === null) {
                return [];
            }
            $current_order_address = Az::$app->market->address->getAddress($order[0]->id, 'order');
            return $current_order_address;
        }

    #endregion


    public function getProductBelongsToOrder($order_id = null)
    {
        if ($order_id === null)
            return null;

        $orderItems = [];
        
        $current_order = $this->core_orders->firstWhere('id', $order_id);
        $order_items = $this->core_order_items->whereIn('id', $current_order->core_order_item_ids);
//        vdd($order_items);
        foreach ($order_items as $order_item){
            $c_order = new OrderElementItem();
            $core_catalog  = $order_item->getCoreCatalog() ?? null;
            $c_order->amount = $order_item->amount;
            $c_order->sum = ($order_item->amount) * ($order_item->price);
            $user_company = $core_catalog->getUserCompany() ?? null;
            $c_order->catalog_name = $user_company->name;
            $shop_element = $core_catalog->getCoreElement() ?? null;
            $c_order->created_at = $order_item->created_at;
            $c_order->element_price = $core_catalog->price;
            $c_order->element_name = $shop_element->name;
            $c_order->status = $current_order->status;
            $c_order->delivery_type = $current_order->packaging_type;
            $shop_product = $shop_element->getCoreProduct() ?? null;
            $c_order->image = $shop_product->image;
            $orderItems[] = $c_order;
            
        }


        return $orderItems;
    }
}
