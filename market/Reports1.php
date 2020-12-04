<?php

/**
 * Author: Sardor
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\former\order\OrderForm;
use zetsoft\former\reports\ReportsCourierForm;
use zetsoft\former\reports\ReportsOrderStatusForm;
use zetsoft\former\reports\ReportsRejectCauseForm;
use zetsoft\former\reports\ReportsSoldProductsForm;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopRejectCause;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\pluck;


class Reports1 extends ZFrame
{
    /** @var Collection $orders */
    public $orders;
    public $order_items;
    public $catalogs;
    public $shipments;
    public $couriers;
    public $companies;
    public $wareAccept;
    public $reject_causes;

    #region init

    /**
     * @var mixed
     */

    public function init()
    {
        $this->reject_causes = collect(ShopRejectCause::find()->asArray()->all());
        $this->orders = collect(ShopOrder::find()->asArray()->all());
        $this->order_items = collect(ShopOrderItem::find()->asArray()->all());
        $this->catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shipments = collect(ShopShipment::find()->asArray()->all());
        $this->couriers = collect(ShopCourier::find()->asArray()->all());
        $this->companies = collect(UserCompany::find()->asArray()->all());
        $this->wareAccept = collect(WareAccept::find()->asArray()->all());
        parent::init();
    }

    #endregion

    #region daily_report

    public function dailyReport()
    {

    }

    #endregion

    #region SoldProductReport

    public function soldProductsReport()
    {

        $orders = collect($this->orders);
        $order_items = collect($this->order_items);
        $catalogs = collect($this->catalogs);

        $forms = [];

        foreach ($catalogs as $catalog) {

            $form = new ReportsSoldProductsForm();

            $order_item = $order_items->first;

            $order = $orders->where('id', $order_item->shop_order_id)->first;

            $new_order_items = $order_items->where('shop_catalog_id', $catalog->id);

            $new_orders = $orders->whereIn('id', $new_order_items->pluck('shop_order_id'));

            $sold_products = $new_orders->whereIn('status_client', [ShopOrder::status_client['accepted'], ShopOrder::status_client['delivered']]);

            $rejected_products = $new_orders->whereIn('status_client', [ShopOrder::status_client['not_delivered'], ShopOrder::status_client['not_received']]);

            $returned_products = $new_orders->whereIn('status_logiistics', [ShopOrder::status_accept['delivery_failure'], ShopOrder::status_accept['cancel']]);

            if (($new_orders->where('status_logiistics', ShopOrder::status_accept['exchange'])->count() + $returned_products->count()) > 0)
                $extra_outcome = $rejected_products->sum('price') / ($new_orders->where('status_logiistics', ShopOrder::status_accept['exchange'])->count() + $returned_products->count());
            else
                $extra_outcome = 0;


            $income = $new_order_items->whereIn('shop_order_id', $sold_products->pluck('id'))->sum('price') - $new_orders->where('id', $order->id)->sum('delivery_price') - ($rejected_products->sum('price') + ($new_orders->where('status_logiistics', ShopOrder::status_accept['exchange'])->count() + $returned_products->count()));

            $form->product_name = $catalog->name;
            $form->products_amount = $new_order_items->count();
            $form->sold_products_amount = $sold_products->count();
            $form->price_sold_products = $new_order_items->whereIn('shop_order_id', $sold_products->pluck('id'))->sum('price');
            $form->percent_sold_products = number_format($this->percentExecute($sold_products->count(), $new_order_items->count()), 2, '.', '');
            $form->delivery = $new_orders->sum('delivery_price');
            $form->rejects_amount = $rejected_products->count();
            $form->percent_rejects = number_format($this->percentExecute($rejected_products->count(), $new_order_items->count()), 2, '.', '');
            $form->in_delivery = $new_orders->where('status_client', ShopOrder::status_client['delivering'])->count();
            $form->returned_products_amount = $returned_products->count();
            $form->returned_products_price = $returned_products->sum('price');
            $form->extra_outcome = $extra_outcome;
            $form->income = $income;

            $forms[] = $form;


        }

        return $forms;

    }

    #endregion

   /* public function getRejectCauses()
    {
        $orders = $this->orders;
        $shipments = $this->shipments;
        $couriers = $this->couriers;

        $forms = [];

        foreach ($orders as $order) {

            $shipment = $shipments->where('id',$order->shop_shipment_id)->first;

            $courier = $couriers->where('id',$shipment->shop_courier_id)->first;

            $form = new ReportsRejectCauseForm();

            $form->shipment_info = 'asdas';

        }


    }*/

    #region PercentExecute

    public function percentExecute($required_number, $total_amount)
    {
        if ($total_amount > 0)
            return $required_number * 100 / $total_amount;

        return 0;
    }

    #endregion

    #region CourierReport

    public function courierReport()
    {

        $orders = $this->orders;
        $order_items = $this->order_items;
        $catalogs = $this->catalogs;
        $shipments = $this->shipments;
        $couriers = $this->couriers;
        $companies = $this->companies;
        $ware_accepts = $this->wareAccept;

        $forms = [];

        $user_couriers = [];
        foreach ($companies as $company) {

            $user_couriers = $couriers->where('user_company_id', $company['id']);

            $total_shipment = $shipments->whereIn('shop_courier_id', $user_couriers->pluck('id'));

            $total_order = $orders->whereIn('shop_shipment_id', $total_shipment->pluck('id'))->sum('total_price');

            foreach ($user_couriers as $user_courier) {

                $form = new ReportsCourierForm();

                $courier_salary = $user_courier['award_order'] + $user_courier['delivery_price'];

                $new_shipments = $shipments->where('shop_courier_id', $user_courier['id']);

                $new_orders = $orders->whereIn('shop_shipment_id', $new_shipments->pluck('id'));

                $sold_orders = $new_orders->whereIn('status_accept', ShopOrder::status_accept['completed'])->count();

                $all = $new_orders->count();

                $transfer = $new_orders->whereIn('status_accept', ['delivery_transfer'])->count();

                $shipment_price = $new_orders->where('shop_order_id', $user_courier['id'])->sum('total_price');

                $additional_delivery_price = $new_shipments->where('shop_order_id', $user_courier['id'])->sum('additional_received_money');

                $interminal = $new_orders->whereIn('payment_type', [ShopOrder::payment_type['humo'], ShopOrder::payment_type['uzcard']]);

                $in_cashless = $new_orders->where('payment_type', ShopOrder::payment_type['transfer']);

                $terminal_sum = $new_orders->whereIn('id', $interminal->pluck('id'))->sum('total_price');

                $cash_sum = $new_orders->whereIn('id', $in_cashless->pluck('id'))->sum('total_price');

                $cancelled_orders = $new_orders->whereIn('status_accept', ShopOrder::status_accept['cancel']);

                $ware = $ware_accepts->where('shop_courier_id', $user_courier['id']);

                $usd = $ware->sum('in_dollar');

                $return_award = $user_courier['award_return'];


                $form->courier = $user_courier['name'];
                $form->courier_company = $company['name'];
                $form->all = $all;
                $form->buyback = $sold_orders;
                $form->percent = $this->percentExecute($sold_orders, $all);
                $form->transfer = $transfer;
                $form->percentage_of_transfer = $this->percentExecute($transfer, $all);
                $form->amount = $shipment_price;
                $form->percentage_of_total = $this->percentExecute($form->amount, $total_order);
                $form->terminal = $terminal_sum;
                $form->cashless = $cash_sum;
                $form->additional_delivery = $additional_delivery_price;
                $form->bonus = $user_courier['award_order'];
                $form->usd = $usd;
                $form->returned_products_amount = $cancelled_orders->whereIn('id', $cancelled_orders->pluck('id'))->sum('price');
                $form->returned_products_payment = $return_award;
                $form->balance = ($form->amount - $form->returned_products_amount - ($form->all ? $form->returned_products_payment : 0) - $all * $courier_salary);


                $forms[] = $form;

            }

        }

        return $forms;

    }

    #endregion

    #region OrderSrtatus

    public function orderStatus(){


        $order_items = $this->order_items;
        $orders = $this->orders;
        $catalogs = $this->catalogs;
        $shipments = $this->shipments;
        $couriers = $this->couriers;
        $companies = $this->companies;
       // $ware_accepts = collect($this->ware_accepts);

        $forms = [];

        foreach ($orders as $order) {

            $form = new ReportsOrderStatusForm();

            $form->client = 

            $forms[] = $form;
        }




        

    }

    #endregion

}



