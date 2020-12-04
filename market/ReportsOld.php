<?php

/**
 * Class Reports
 * @package zetsoft\service\market
 * @author OtabekNosirov
 * @author JaloliddinovSalohiddin
 * @author AkromovAzizjon
 *
 * @license OtabekNosirov
 * @license JaloliddinovSalohiddin
 * @license AkromovAzizjon
 *
 */

namespace zetsoft\service\market;

use FontLib\Table\Type\name;
use Illuminate\Support\Collection;
use zetsoft\former\order\OrderForm;
use zetsoft\former\reports\CourierReportForm;
use zetsoft\former\reports\DailyReportFormSkd;
use zetsoft\former\reports\OrderStatusForm;
use zetsoft\former\reports\RejectCauseForm;
use zetsoft\former\reports\SoldProductsForm;
use zetsoft\former\shop\ShopDailyReportForm;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopRejectCause;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;

use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExitItem;
use zetsoft\models\ware\WareTrans;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

class Reports extends ZFrame
{


    public $orders;

    public $order_items;

    public ?Collection $catalogs = null;
    public ?Collection $catalogsWare = null;
    public ?Collection $shipments = null;
    public ?Collection $couriers = null;
    public ?Collection $companies = null;
    public ?Collection $wareAccept = null;
    public ?Collection $wareEnterItem = null;
    public ?Collection $wareExitItem = null;
    public ?Collection $reject_causes = null;
    public ?Collection $place_regions = null;
    /** @var Collection $products */
    public $products;
    public $shopElement;


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
        $this->products = collect(ShopProduct::find()->asArray()->all());
        $this->catalogsWare = collect(ShopCatalogWare::find()->asArray()->all());
        $this->shipments = collect(ShopShipment::find()->asArray()->all());
        $this->shopElement = collect(ShopElement::find()->asArray()->all());
        $this->couriers = collect(ShopCourier::find()->asArray()->all());
        $this->companies = collect(UserCompany::find()->asArray()->all());
        $this->wareAccept = collect(WareAccept::find()->asArray()->all());
        $this->wareEnterItem = collect(WareEnterItem::find()->asArray()->all());
        $this->wareExitItem = collect(WareExitItem::find()->asArray()->all());
        $this->place_regions = collect(PlaceRegion::find()->asArray()->all());

        parent::init();
    }

    #endregion

    #region test

    public function test()
    {
        vd($this->dailyReportSKdTest());
        vd($this->soldProductsReportTest());
        vd($this->percentExecuteTest());
        vd($this->courierReportTest());
        vd($this->getRejectCausesTest());
        vd($this->getAcceptanceFromCourierTest());
        vd($this->getOrderStatusTest());
    }

    #endregion test

    #region Daily_reportTest

    public function dailyReportSKdTest()
    {
        $this->dailyReportSKd();

    }

    #endregion

    //start: MurodovMirbosit dailyReport
    public function dailyReport()
    {
    
        $shop_catalogs = $this->catalogs;
        $ware_enter_items = $this->wareEnterItem;
        $ware_exit_items = $this->wareExitItem;

        $forms = [];

        foreach ($shop_catalogs->all() as $catalog) {

            $new_catalogs = $ware_enter_items->where('shop_catalog_id', $catalog['id']);

            $shop_ware_exit_items = $ware_exit_items->where('shop_catalog_id', $catalog['id']);

            foreach ($shop_ware_exit_items as $shop_ware_exit_item) {

                foreach ($new_catalogs as $new_catalog) {

                    $form = new ShopDailyReportForm();

                    $form->shop_catalog_id = $catalog['name'] ?? '---';

                    $form->best_before = $new_catalog['best_before'] ?? '---';

                    $history = json_decode($new_catalog['amount_history'], true, 512, JSON_THROW_ON_ERROR);

                    $history_name = ZArrayHelper::getValue($history[0], 'name');

                    $form->before_amount = $history_name ?? '---';

                    $form->enter_amount = $new_catalog['amount'];

                    $form->exit_amount = $shop_ware_exit_item['amount'];

                    $form->last_amount = $form->before_amount + $form->enter_amount - $form->exit_amount;

                    $forms[] = $form;
                }
            }
        }
        return $forms;
    }
    //todo::end


    #region Daily_reportSkd


    /**
     *
     * Function  dailyReportSKd
     *
     * Kunlik otchetlar
     */
    public function dailyReportSKd()
    {
        $orders = $this->orders;
        $couriers = $this->couriers;
        $shipments = $this->shipments;
        $companies = $this->companies;
        $place_regions = $this->place_regions;

        $forms = [];

        foreach ($couriers->all() as $courier) {

            $new_shipments = $shipments->where('shop_courier_id', $courier['id']);

            $company = $companies->where('id', $courier['user_company_id'])->first();

            $place_region = $place_regions->where('id', $courier['place_region_id'])->first();

            $form = new DailyReportFormSkd();

            $form->courier_name = $courier['name'] ?? '---';

            $form->user_company_name = $company['name'] ?? '---';

            $form->place_region_name = $place_region['name'] ?? '---';

            foreach ($new_shipments as $ship) {

                $new_orders = $orders->where('shop_shipment_id', $ship['id']);

                $sold_products = $new_orders->whereIn('status_logisitics', ShopOrder::status_logistics['completed']);

                $rejected_products = $new_orders->whereIn('status_logistics', ShopOrder::status_accept['cancel']);

                $transferred_products = $new_orders->whereIn('status_logistics', ShopOrder::status_accept['delivery_transfer']);

                $form->total_products += $new_orders->count();

                $form->sold_products += $sold_products->count();

                $form->rejected_products += $rejected_products->count();

                $form->transferred_products += $transferred_products->count();

                $form->total_sold_products += $sold_products->sum('price');

            }

            $form->sold_products_percent = $this->percentExecute($form->sold_products, $form->total_products);

            $forms[] = $form;

        }

        return $forms;

    }

    #endregion

    #region SoldProductReportTest

    public function soldProductsReportTest()
    {
        $this->soldProductsReport();

    }

    #endregion

    #region SoldProductReport
    /**
     *
     * Function  soldProductsReport
     * @return  array
     *
     * Sotilgan productlar torisidagi otchetlar
     */
    public function soldProductsReport()
    {

        $orders = $this->orders;
        $order_items = $this->order_items;
        $catalogs = $this->catalogs;

        $forms = [];

        foreach ($catalogs as $catalog) {

            $form = new SoldProductsForm();

            $order_item = $order_items->where('shop_catalog_id', $catalog['id'])->first;

            $order = $orders->where('id', $order_item->shop_order_id)->first;

            $new_order_items = $order_items->where('shop_catalog_id', $catalog['id']);

            $new_orders = $orders->whereIn('id', $new_order_items->pluck('shop_order_id'));

            $sold_products = $new_orders->whereIn('status_client', [ShopOrder::status_client['accepted'], ShopOrder::status_client['delivered']]);

            $rejected_products = $new_orders->whereIn('status_logistics', [ShopOrder::status_logistics['delivery_failure'], ShopOrder::status_logistics['cancelled']]);


            $returned_products = $new_orders->whereIn('status_logistics', [ShopOrder::status_accept['delivery_failure'], ShopOrder::status_accept['cancel']]);

            $returned_products_price = $new_order_items->whereIn('shop_order_id', $returned_products->pluck('id'))->sum('price');

            $rejected_products_price = $new_order_items->whereIn('shop_order_id', $rejected_products->pluck('id'))->sum('price');

            $price_sold_products = $new_order_items->whereIn('shop_order_id', $sold_products->pluck('id'))->sum('price');

            $outcome = $new_order_items->whereIn('shop_order_id', $rejected_products->pluck('id'))->sum('price');

            if (($new_orders->where('status_logiistics', ShopOrder::status_accept['exchange'])->count() + $returned_products->count()) > 0)
                $extra_outcome = $rejected_products_price / ($new_orders->where('status_logistics', ShopOrder::status_accept['exchange'])->count() + $returned_products->count());
            else
                $extra_outcome = 0;


            $income = $price_sold_products - $new_orders->where('id', $order->id)->sum('delivery_price') - ($rejected_products_price + ($new_orders->where('status_logistics', ShopOrder::status_accept['exchange'])->count() + $returned_products_price));

            $form->product_name = $catalog['name'];
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

    #region PercentExecuteTest

    public function percentExecuteTest()
    {
        $this->percentExecute();
    }

    #endregion

    #region PercentExecute

    /**
     *
     * Function  percentExecute
     *
     * Prosent qisoblab beradigan funksiya
     *
     * @param $required_number
     * @param $total_amount
     * @return  float|int
     *
     */
    public function percentExecute($required_number, $total_amount)
    {
        if ($total_amount > 0)
            return $required_number * 100 / $total_amount;

        return 0;
    }

    #endregion

    #region courierReportTest

    public function courierReportTest()
    {

        $this->courierReport();

    }
    #endregion

    #region courierReport
    /**
     * Function  courierReport
     * Kurierlar torisida otchetlar
     * @return  array
     */
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

                $courier_salary = 0;

                $form = new CourierReportForm();

                $courier_salary = (double)$user_courier['award_order'] + (double)$user_courier['delivery_price'];

                $new_shipments = $shipments->where('shop_courier_id', $user_courier['id']);

                $new_orders = $orders->whereIn('shop_shipment_id', $new_shipments->pluck('id'));

                $sold_orders = $new_orders->whereIn('status_accept', [ShopOrder::status_client['delivered'], ShopOrder::status_client['received']])->count();

                $all = $new_orders->count();

                $transfer = $new_orders->whereIn('status_accept', ['delivery_transfer'])->count();

                $shipment_price = $new_orders->where('shop_order_id', $user_courier['id'])->sum('total_price');

                $additional_delivery_price = $new_shipments->where('shop_order_id', $user_courier['id'])->sum('additional_received_money');

                $interminal = $new_orders->whereIn('payment_type', [ShopOrder::payment_type['humo'], ShopOrder::payment_type['uzcard']]);

                $in_cashless = $new_orders->where('payment_type', ShopOrder::payment_type['transfer']);

                $terminal_sum = $new_orders->whereIn('id', $interminal->pluck('id'))->sum('total_price');

                $cash_sum = $new_orders->whereIn('id', $in_cashless->pluck('id'))->sum('total_price');

                $cancelled_orders = $new_orders->whereIn('status_logistics', [ShopOrder::status_logistics['delivery_failure'], ShopOrder::status_logistics['annulled']]);

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
                $form->returned_products_amount = $cancelled_orders
                    ->whereIn('id', $cancelled_orders->pluck('id'))->sum('price');
                $form->returned_products_payment = $return_award;
                $form->balance = ($form->amount - $form->returned_products_amount
                    - ($form->all ? $form->returned_products_payment : 0) - $all * $courier_salary);


                $forms[] = $form;
            }
        }
        return $forms;
    }
    #endregion

    #region getRejectCausesTest
    public function getRejectCausesTest()
    {

        $this->getRejectCauses();


    }

    #endregion getRejectCauses

    #region getRejectCauses

    /**
     *
     * Function  getRejectCauses
     *
     * Otkaz qilingan tovarlani otcheti
     * @param null $company_ids
     * @return  array
     */
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

            if ($order !== null) {
                //  vdd($order['shop_shipment_id']);
                $shipment = $shipments->where('id', $order['shop_shipment_id'])->first();

                /*vdd($shipment['shop_courier_id']);*/
                if ($shipment !== null) {

                    $courier = $couriers->where('id', $shipment['shop_courier_id'])->first();

                    $reject_cause = $causes->where('id', $order['shop_reject_cause_id'])->first();
                    //vdd($shipment['shop_courier_id']);

                    $company = $companies->where('id', $courier['user_company_id'])->first();

                    $form = new RejectCauseForm();

                    $form->shipment_info = 'Приемка от курьера "' . $courier['name'] ?? ' ' . '" дата :' . $order['data_return'];
                    $form->status_accept = $order['status_accept'];
                    $form->reject_cause = $reject_cause !== null ? $reject_cause['name'] : '';
                    $form->order_info = 'Заказ клиента "' . $order['contact_name'] . '" дата: ' . $order['created_at'] . ' ,' . $courier['name'] ?? ' ' . ' ,' . $company['name'] ?? " ";

                    $forms[] = $form;

                }
            }

        }

        return $forms;
    }


    #endregion getRejectCauses

    ##region getAcceptanceFromCourierTest
    public function getAcceptanceFromCourierTest()
    {
        $this->getAcceptanceFromCourier();
    }
    #endregion

    ##region getAcceptanceFromCourier

    /**
     *
     * Function  getAcceptanceFromCourier
     *
     * otchet priemka kurielar
     * @param null $company_id
     * @return  array
     */
     
    public function getAcceptanceFromCourier($company_id = null)
    {
        $forms = [];

        $order = $this->orders;
        $catalog = $this->catalogs;
        $orders = $this->order_items;
        if ($this->wareAccept->isEmpty())
            foreach ($this->wareAccept as $item) {

                $shop_order = $order
                    ->where('shop_shipment_id', $item['shop_shipment_id'])
                    ->where('status_accept', ShopOrder::status_accept['delivery_failure']);


                if ($company_id === null)
                    $shop_order_item = $orders->whereIn('shop_order_id', $shop_order->pluck('id'));
                else
                    $shop_order_item = $orders->whereIn('shop_order_id', $shop_order->pluck('id'))->where('user_company_id', $company_id);
                if ($shop_order_item->isEmpty())
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
                        $forms[] = $form;
                    }
            }

        return $forms;
    }
    #endregion

    ##region getOrderStatusTest
    public function getOrderStatusTest()
    {
        $this->getOrderStatus();
    }
    #endregion

    ##region getOrderStatus

    /**
     *
     * Function  getOrderStatus
     *
     * orderlani statusi torisidagi otchetlar
     * @return  array
     */
    public function getOrderStatus()
    {
        $order_items = $this->order_items;
        $orders = $this->orders;
        $forms = [];
        foreach ($order_items as $order_item) {
            $orderStatus = new OrderStatusForm();
            $orderStatus->id = $order_item['id'];
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



