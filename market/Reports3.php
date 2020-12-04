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


class Reports3 extends ZFrame
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
    public $wareSend;
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
        $this->wareSend;
        parent::init();
    }

    #endregion

    #region test

    public function test()
    {

        /* $this->getRejectCausesTest();*/
        vdd($this->getOrderStatusTest());

    }

    #endregion test

    #region daily_report

    public function dailyReport()
    {

    }

    #endregion

    #region SoldProductReport

    public function soldProductsReport()
    {

        $orders = $this->orders;
        $order_items = $this->order_items;
        $catalogs = $this->catalogs;

        $forms = [];

        foreach ($catalogs as $catalog) {

            $form = new ReportsSoldProductsForm();

            $order_item = $order_items->first;

            $order = $orders->where('id', $order_item->shop_order_id)->first;

            $new_order_items = $order_items->where('shop_catalog_id', $catalog->id);

            $new_orders = $orders->whereIn('id', $new_order_items->pluck('shop_order_id'));

            $sold_products = $new_orders->where('status_accept', ShopOrder::status_accept['completed']);

            $rejected_products = $new_orders->where('status_accept', ShopOrder::status_accept['cancel']);

            $returned_products = $new_orders->whereIn('status_logistics', [ShopOrder::status_accept['delivery_failure'], ShopOrder::status_accept['cancel']]);

            $returned_products_price = $new_order_items->whereIn('shop_order_id', $returned_products->pluck('id'))->sum('price');

            $rejected_products_price = $new_order_items->whereIn('shop_order_id', $rejected_products->pluck('id'))->sum('price');

            $price_sold_products = $new_order_items->whereIn('shop_order_id', $sold_products->pluck('id'))->sum('price');

            $outcome = $new_order_items->whereIn('shop_order_id', $rejected_products->pluck('id'))->sum('price');

            if (($new_orders->where('status_accept', ShopOrder::status_accept['exchange'])->count() + $returned_products->count()) > 0)
                $extra_outcome = $rejected_products_price / ($new_orders->where('status_accept', ShopOrder::status_accept['exchange'])->count() + $returned_products->count());
            else
                $extra_outcome = 0;


            $income = $price_sold_products - $new_orders->where('id', $order->id)->sum('delivery_price') - ($rejected_products_price + ($new_orders->where('status_accept', ShopOrder::status_accept['exchange'])->count() + $returned_products_price));

            $form->product_name = $catalog->name;
            $form->products_amount = $new_order_items->count();
            $form->sold_products_amount = $sold_products->count();
            $form->price_sold_products = $price_sold_products;
            $form->percent_sold_products = number_format($this->percentExecute($sold_products->count(), $new_order_items->count()), 2, '.', '');
            $form->delivery = $new_orders->sum('delivery_price');
            $form->rejects_amount = $rejected_products->count();
            $form->percent_rejects = number_format($this->percentExecute($rejected_products->count(), $new_order_items->count()), 2, '.', '');
            $form->outcome = $outcome;
            $form->in_delivery = $new_orders->where('status_client', ShopOrder::status_client['delivering'])->count();
            $form->returned_products_amount = $returned_products->count();
            $form->returned_products_price = $returned_products_price;
            $form->extra_outcome = $extra_outcome;
            $form->income = $income;

            $forms[] = $form;


        }

        return $forms;

    }

    #endregion


    #region PercentExecute

    public function percentExecute($required_number, $total_amount)
    {
        if ($total_amount > 0)
            return $required_number * 100 / $total_amount;

        return 0;
    }

    #endregion

    public function courierReport()
    {

        $orders = $this->orders;
        $order_items = $this->order_items;
        $catalogs = $this->catalogs;
        $shipments = $this->shipments;
        $couriers = $this->couriers;
        $companies = $this->companies;

        $forms = [];


        foreach ($couriers->all() as $courier) {

            $form = new ReportsCourierForm();

            $sold = $shipments->where('shop_courier_id', $courier->id)->whereIn('status', [ShopShipment::status['delivered'], ShopShipment::status['given']])->count();
            $all = $shipments->where('shop_courier_id', $courier->id)->count();
            $transfer = $orders->whereIn('status_accept', ['delivery_transfer'])->count();


            $form->courier = $courier->name;
            $form->all = $all;
            $form->buyback = $sold;
            $form->percent = $this->percentExecute($sold, $all);
            $form->transfer = $transfer;
            $form->percentage_of_transfer = $this->percentExecute($transfer, $all);
            //$form->terminal = ;

            $forms[] = $form;

        }

        return $form;

    }


    #region getRejectCauses

    public function getRejectCausesTest()
    {

        $data = $this->getRejectCauses();
        $this->getRejectCauses();

        vdd($data);
    }

    public function getRejectCauses($company_ids = null)
    {
        if ($company_ids)
            $orders = $this->orders->whereIn('id', $company_ids);
        else
            $orders = $this->orders;

        $shipments = $this->shipments;
        $couriers = $this->couriers;
        $causes = $this->reject_causes;
        $companies = $this->companies;

        $forms = [];

        foreach ($orders as $order) {

            $shipment = $shipments->where('id', $order['shop_shipment_id'])->first();

            if ($shipment !== null)
                $courier = $couriers->where('id', $shipment['shop_courier_id'])->first();


            $reject_cause = $causes->where('id', $order['shop_reject_cause_id'])->first();

            $company = $companies->where('id', $courier['user_company_id'])->first();

            $form = new ReportsRejectCauseForm();

            $form->shipment_info = 'Приемка от курьера "' . $courier['name'] ?? ' ' . '" дата :' . $order['data_return'];
            $form->status_accept = $order['status_accept'];
            $form->reject_cause = $reject_cause !== null ? $reject_cause['name'] : '';
            $form->order_info = 'Заказ клиента "' . $order['contact_name'] . '" дата: ' . $order['created_at'] . ' ,' . $courier['name'] ?? ' ' . ' ,' . $company['name'] ?? " ";

            $forms[] = $form;
        }

        return $forms;
    }


    #endregion getRejectCauses

    ##region getOrderInfoByDeliveryFailureTest
    public function getAcceptanceFromCourierTest()
    {
        $this->getAcceptanceFromCourier(200);
    }
    #endregion


    ##region getOrderInfoByDeliveryFailure
    public function getAcceptanceFromCourier($company_id = null)
    {
        $forms = [];

        $order = $this->orders;
        $catalog = $this->catalogs;
        $orders = $this->order_items;
        if (!empty($this->wareAccept))
            foreach ($this->wareAccept as $item) {
                $shop_order = $order->where('shop_shipment_id', $item['shop_shipment_id'])->where('status_accept', ShopOrder::status_accept['delivery_failure']);
                if ($company_id === null)
                    $shop_order_item = $orders->whereIn('shop_order_id', $shop_order->pluck('id'));
                else
                    $shop_order_item = $orders->whereIn('shop_order_id', $shop_order->pluck('id'))->where('user_company_id', $company_id);

                if (!empty($shop_order_item))
                    foreach ($shop_order_item as $val) {
                        $form = new OrderForm();
                        $form->courier = $item['name'] . $item['created_at'];
                        $form->created_at = $val['created_at'];
                        $form->id = $val['id'];
                        $form->amount = $val['amount'];
                        $catalogName = $catalog->where('id', $val['shop_catalog_id'])->first();
                        $form->product = $catalogName['name'] ?? '';
                        $form->client = $val['name'];
                        $form->status = ShopOrder::status_accept['delivery_failure'];
                        $forms[] = (array)$form;
                    }
            }

        return $forms;
    }
    #endregion


    ##region getFailureReasonsTest
    public function getFailureReasonsTest()
    {
        $this->getFailureReasons();
    }
    #endregion

    ##region getFailureReasons
    public function getFailureReasons()
    {

    }
    #endregion
    ##region getFailureReasonsTest
    public function getOrderStatusTest()
    {
        $this->getOrderStatus();
    }
    #endregion

    ##region getOrderStatus
    public function getOrderStatus()
    {
        $order_items = $this->order_items;
        $orders = $this->orders;
        $forms = [];
        foreach ($order_items as $order_item) {
            $orderStatus = new ReportsOrderStatusForm();
            $order = $orders->where('id', $order_item['shop_order_id'])->first();
            if ($order !== null) {
                $orderStatus->delivery_date = $order['date_deliver'];
                $orderStatus->transfer_date = $order['delayed_deliver_date'];
                $orderStatus->status = $order['status_logistics'];
                $orderStatus->city = $order['place_region_id'];
                $orderStatus->project = $order_item['user_company_id'];
                $orderStatus->products_count = $order_item['amount'];
                $orderStatus->pack_count = $order_item['amount_partial'];
                $orderStatus->phone = $order['contact_phone'];
                $orderStatus->client = $order_item['name'];
                $orderStatus->reference = $order_item['shop_catalog_id'];
                $orderStatus->price = $order_item['price'];
            }
            $forms[] = $orderStatus;
        }

        return $forms;
    }
    #endregion


}



