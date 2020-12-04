<?php

/**
 * Author: Jobir
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;
use zetsoft\former\shop\ShopOperatorForm;

class Operator extends ZFrame
{
    /**
     * @var Collection $core_orders
     */
    private $core_orders;

    /**
     * @var Collection $core_shipments
     */
    private $core_shipments;

    /**
     * @var Collection $users
     */
    private $users;

    public function init()
    {
        parent::init();
        $this->core_orders = collect(ShopOrder::find()->all());
        $this->core_shipments = collect(ShopShipment::find()->all());
        $this->users = collect(User::find()->all());
    }

    public function shipmentData($operator_id){
        if ($operator_id === null) return [];
        /*$orders = ShopOrder::find()
            ->where([
                'operator_id' => $operator_id
            ])
            ->all();*/
        $orders = $this->core_orders->where('operator_id', $operator_id);

        $data = [];

        foreach ($orders as $order){
            $shipment = $this->core_shipments->firstWhere('id', $order->id);
            $user = $this->users->firstWhere('id', $order->user_id);
            if($shipment === null) continue;
            if($user === null) continue;
            $form = new ShopOperatorForm();
            $form->user_id = $user->name;
            //$form->contact_info = $order->contact_info;
            $form->full_name = $order->contact_info['full_name'];
            $form->phone = $order->contact_info['phone'];
            $form->email = $order->contact_info['email'];
            $form->comment = $order->comment;
            $form->core_adress_id = $order->core_adress_id;
            $form->shipment_type = $shipment->shipment_type;
            $form->payment_type = $shipment->payment_type;
            $form->courier_id = $shipment->courier_id;
            $form->price = $order->price;
            $form->total_price = $shipment->price;
            $form->shop_coupon_id = $order->shop_coupon_id;
            $data[] = $form;

        }

        return $data;
    }

}
