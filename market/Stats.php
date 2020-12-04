<?php

/**
 * Author:  Xolmat Ravshanov
 * Date:    10.11.2020
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\models\ware\Ware;
use zetsoft\system\kernels\ZFrame;

class Stats extends ZFrame
{
    public ?Collection $shop_order;
    public ?Collection $shop_order_item;
    public ?Collection $user;
    public ?Collection $ware;


    public function init()
    {
        parent::init();
        $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());
        $this->user = collect(User::find()->asArray()->all());
        $this->ware = collect(Ware::find()->asArray()->all());
        $this->shop_order = collect(ShopOrder::find()->asArray()->all());
              //line|20
    }

    /**
     *
     * Function  getShopOrderItemSum
     * @return  mixed
     */
    public function getShopOrderItemSum()
    {
        $return = $this->shop_order_item->sum('amount');
        return $return;
    }

    /**
     *
     * Function  getClientOrderSum
     * @return  int
     */
    public function getClientOrderSum()
    {
        $return = $this->user->where('role', 'seller')
            ->count();
        return $return;
    }

    /**
     *
     * Function  getWareAmount
     * @return  int
     */
    public function getWareAmount()
    {
        $return = $this->ware->count();
        return $return;
    }

    /**
     *
     * Function  getSoldOrderSum
     * @return  int|mixed
     */
    public function getSoldOrderSum()
    {

        $return = 0;

        $shop_orders = $this->shop_order->where('status_logistics', 'completed')->all();

        foreach ($shop_orders as $shop_order) {
            $shop_order_item = $this->shop_order_item->where('shop_order_id', $shop_order['id'])->sum('amount');
            $return += $shop_order_item;
        }
        return $return;
    }


}




