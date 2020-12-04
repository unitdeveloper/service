<?php

/**
 * Author: Jaxongir
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\AdminItem;
use zetsoft\former\chart\ChartFormAdmin;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\system\kernels\ZFrame;


class AdminStatistic extends ZFrame
{
    public $market;
    public $user;
    public $product;
    public $order;
    public $order_item;
    public $agent;
    public $logistics;
    public $catalogs;
    public $element;

    #region init

    public function init()
    {
        parent::init();
        $this->user = User::find()->select('role')->asArray()->all();
        $this->market = UserCompany::find()->asArray()->all();
        $this->product = ShopProduct::find()->asArray()->all();
        $this->catalogs = ShopCatalog::find()->asArray()->all();
        $this->element = ShopElement::find()->asArray()->all();
        $this->order = ShopOrder::find()->asArray()->all();
        $this->order_item = ShopOrderItem::find()->asArray()->all();

    }

    #endregion

    public function adminInfo()
    {
        $result = new AdminItem();
        $result->user = 0;
        $result->order = collect($this->order)->count();
        $result->product = collect($this->product)->count();
        $result->seller = collect($this->user)->where('role', 'seller')->count();
        $result->user = collect($this->user)->where('role', 'client')->count();
        $result->agent = collect($this->user)->where('role', 'agent')->count();
        $result->logistics = collect($this->user)->where('role', 'logistics')->count();
        
        return (array)$result;
    }

    public function adminInfoChart()
    {

        $result = new AdminItem();
        $result->user = 0;
       // $result->market = \Dash\count($this->market);
        $result->order = \Dash\count($this->order);
        $result->product = \Dash\count($this->product);

        foreach ($this->user as $user) {
            switch ($user['role']) {
                case 'seller':
                    $result->seller += 1;
                    break;
                case 'client':
                    $result->user += 1;
                    break;
                /*case 'deliver':
                    $result->courier += 1;
                    break;*/

            }


        }


        $form = new ChartFormAdmin();
        $form->name = '';
        $form->seller = $result->seller;
        //$form->courier = $result->courier;
        $form->user = $result->user;
        //$form->market = $result->market;
        //$form->product = $result->product;
        $form->order = $result->order;


        return [$form];


    }


    public function AdminExpence($begin = null, $end = null)
    {
        $result = [];
        $list = $this->order;
        $list_item = $this->order_item;
        //$list = ShopOrder::find()->all();
        //$list_item = ShopOrderItem::find()->all();
        $begin = date_create($begin);
        $end = date_create($end);
        foreach ($list as $item) {
            $itemdate = date_create($item['created_at']);
            $total = 0;
            $total_price = 0;
            $data = [];
            // $itemdate = date_create($item->created_at);
            if (($itemdate >= $begin) && ($itemdate <= $end)) {
                foreach ($list_item as $order) {
                    if ($order['shop_order_id'] === $item['id']) {
                        //if ($order->shop_order_id===$item->id){

                        $sum = (int)$order['price'] * (int)$order['amount'];
                        $data[] = [$order['name'], $order['amount'], $order['price'], $sum
                        ];
                        $total += (int)$order['amount'];
                        $total_price += $sum;
                    }

                }
                $result[] = [$item['name'], $total, $total_price, $data];

            }

        }

        return $result;
    }

    public function CourierList()
    {
        $couriers = ShopCourier::find()->all();
        $result = [];
        foreach ($couriers as $courier) {
            $user = User::findOne($courier->user_id);
            $region = PlaceRegion::findOne($courier->place_region_id);
            $result[] = [$courier, $user, $region];
        }

        return $result;

    }


}
