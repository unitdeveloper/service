<?php

/**
 * @author  Xolmat Ravshanov && NurbekMakhmudov
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\former\reports\ReportsCdkForm;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ReportCdkNurbek extends ZFrame
{

    public ?Collection $shop_orders;
    public ?Collection $shop_shipments;
    public ?Collection $ware_accepts;


    public function init()
    {
        parent::init();
        $this->shop_shipments = collect(ShopShipment::find()->asArray()->all());
        $this->shop_orders = collect(ShopOrder::find()->asArray()->all());
        $this->ware_accepts = collect(WareAccept::find()->asArray()->all());
    }

    #region  fillForm
    public function fillForm($beginDate = null, $endDate = null)
    {
        if ($beginDate === null)
            $beginDate = Az::$app->cores->date->dateTime('-24 hours');

        if ($endDate === null)
            $endDate = Az::$app->cores->date->dateTime();

        $return = [];

        foreach ($this->shop_shipments as $shop_shipment) {

            /*$shop_shipment_id = $this->ware_accepts
                ->where('shop_shipment_id', $shop_shipment['id'])
                ->whereBetween('closed_time', [$beginDate, $endDate])->first();
            if ($shop_shipment_id === null) continue;*/

            $completed = 0;
            $date_transfer = 0;
            $refusal = 0;

            $total = 0;

            $completed_percent = 0;

            $sales_amount = 0;

            $closed_percent = 0;

            /** @var Collection $shop_shipment $form */

            $form = new ReportsCdkForm();

            $shop_orders = $this->shop_orders->where('shop_shipment_id', $shop_shipment['id'])->all();

            $shop_courier = ShopCourier::findOne($shop_shipment['shop_courier_id']);
            if ($shop_courier === null)  continue;


            foreach ($shop_orders as $shop_order) {

                $user_company = UserCompany::findOne($shop_order['source']);
                if ($user_company === null)  continue;

                $user_company_delivery_service = UserCompany::findOne($shop_courier['user_company_id']);

                switch (true) {
                    case $shop_order['status_accept'] === 'part_paid':

                        $shop_order_items = ShopOrderItem::find()->where([

                            'shop_order_id' => $shop_order['id']

                        ])->all();

                        foreach ($shop_order_items as $shop_order_item)
                            $sales_amount += $shop_order_item->price_all_partial;

                        break;

                    case $shop_order['status_accept'] === 'completed':

                        $shop_order_items = ShopOrderItem::find()->where([
                            'shop_order_id' => $shop_order['id']
                        ])->all();

                        foreach ($shop_order_items as $shop_order_item)
                            $sales_amount += $shop_order_item->price_all;

                        break;
                }

                $form->user_company_name = $user_company->name;

                $form->delivery_service = $user_company_delivery_service->name;

                $form->courier_name = $shop_courier->name;

                switch (true) {

                    case $shop_order['status_accept'] === 'completed':
                        $form->completed = ++$completed;
                        break;

                    case $shop_order['status_accept'] === 'delivery_failure':
                        $form->refusal = ++$refusal;
                        break;

                    case $shop_order['status_accept'] === 'delivery_transfer':
                        $form->date_transfer = ++$date_transfer;
                        break;
                }
            }

            $total = $completed + $refusal + $date_transfer;

            $form->total = $total;

            if ($total !== 0) {
                $completed_percent = $completed * 100 / $total;
                $closed_percent = ($date_transfer + $completed) * 100 / $total;
            }

            $form->completed_percent = $completed_percent;

            $form->closed_percent = $closed_percent;

            $form->sales_amount = $sales_amount;

            $return[] = $form;
        }
        return $return;
    }
    #endregion
}



