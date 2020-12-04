<?php

/**
 * Author: Sardor
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\former\reports\ReportsCourierForm;
use zetsoft\former\reports\ReportsDailyFormSkd;
use zetsoft\former\reports\ReportsSoldProductsForm;
use zetsoft\former\shop\DailyReportForm;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopRejectCause;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\service\cores\Date;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class Reports2 extends ZFrame
{
    /** @var Collection $orders */
    public ?Collection $orders = null;
    public ?Collection $product = null;
    public ?Collection $order_items = null;
    public ?Collection $catalogs = null;
    public ?Collection $catalogWares = null;
    public ?Collection $shipments = null;
    public ?Collection $couriers = null;
    public ?Collection $companies = null;
    public ?Collection $wareAccept = null;
    public ?Collection $reject_causes = null;
    public ?Collection $place_regions = null;


    #region init

    /**
     * @var mixed
     */

    public function init()
    {
        $this->reject_causes = collect(ShopRejectCause::find()->asArray()->all());
        $this->orders = collect(ShopOrder::find()->asArray()->all());
        $this->product = collect(ShopProduct::find()->asArray()->all());
        $this->catalogWares = collect(ShopCatalogWare::find()->asArray()->all());
        $this->order_items = collect(ShopOrderItem::find()->asArray()->all());
        $this->catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shipments = collect(ShopShipment::find()->asArray()->all());
        $this->couriers = collect(ShopCourier::find()->asArray()->all());
        $this->companies = collect(UserCompany::find()->asArray()->all());
        $this->wareAccept = collect(WareAccept::find()->asArray()->all());
        $this->place_regions = collect(PlaceRegion::find()->asArray()->all());

        parent::init();
    }

    #endregion
    #region daily_report

    public function dailyReport()
    {
        $orders = $this->orders;
        $catalogWares = $this->catalogWares;
        $products = $this->product;

        $forms = [];

        $date = Az::$app->cores->date->date();

        /** @var DailyReportForm $form */
        $form = new DailyReportForm();

        $form->name = $products['name'] ?? '---';

        $form->date = $date;
        $form->coming = 'AA';

        /*

        foreach ($orders->all() as $order) {

            $shopOrders = $orders->where('status_logistics', [
                ShopOrder::status_logistics['completed'],
                ShopOrder::status_logistics['shipment_ready'],
            ]);


            $form->place_region_name = $place_region['name'] ?? "---";

            $forms[] = $form;

        }

        */

        $forms[] = $form;
        $forms[] = $form;
        $forms[] = $form;

        return $forms;
    }

    #endregion

    #region SoldProductReport

    public function soldProductsReport()
    {

        $orders = $this->orders;
        $order_items = $this->order_items;
        $catalogs = $this->catalogs;

        $forms = [];
        if ($catalogs !== null) {
            foreach ($catalogs as $catalog) {

                $form = new ReportsSoldProductsForm();

                $order_item = $order_items->where('shop_catalog_id', $catalog['id'])->first;

                $order = $orders->where('id', $order_item->shop_order_id)->first;

                $new_order_items = $order_items->where('shop_catalog_id', $catalog['id']);

                $new_orders = $orders->whereIn('id', $new_order_items->pluck('shop_order_id'));

                $sold_products = $new_orders->whereIn('status_accept', ShopOrder::status_accept['completed']);

                $rejected_products = $new_orders->whereIn('status_accept', ShopOrder::status_accept['cancel']);

                $returned_products = $new_orders->whereIn('status_accept', ShopOrder::status_accept['delivery_failure']);

                $returned_products_price = $new_order_items->whereIn('shop_order_id', $returned_products->pluck('id'));

                $rejected_products_price = $new_order_items->whereIn('shop_order_id', $rejected_products->pluck('id'));

                $price_sold_products = $new_order_items->whereIn('shop_order_id', $sold_products->pluck('id'));

                $price_sold_products = !empty($price_sold_products) ? $price_sold_products->sum('price') : 0;

                $outcome = $new_order_items->whereIn('shop_order_id', $rejected_products->pluck('id'));

                $outcome = !empty($outcome) ? $outcome->sum('price') : 0;
                $returned_products_price = !empty($returned_products_price) ? $returned_products_price->sum('price') : 0;
                $rejected_products_price = !empty($rejected_products_price) ? $rejected_products_price->sum('price') : 0;

                if (($new_orders->where('status_accept', ShopOrder::status_accept['exchange'])->count() + $returned_products->count()) > 0)
                    $extra_outcome = $rejected_products_price / ($new_orders->where('status_accept',
                                ShopOrder::status_accept['exchange'])->count() + $returned_products->count());
                else
                    $extra_outcome = 0;

                $income = $price_sold_products - $new_orders->where('id', $order->id)->sum('delivery_price') - ($rejected_products_price + ($new_orders->where('status_accept', ShopOrder::status_accept['exchange'])->count() + $returned_products_price));

                $form->product_name = $catalog['name'];
                $form->products_amount = $new_order_items->count();
                $form->sold_products_amount = $sold_products->count();
                $form->price_sold_products = $price_sold_products;
                $form->percent_sold_products = number_format($this->percentExecute($sold_products->count(), $new_order_items->count()), 2, '.', '');
                $form->delivery = $new_orders->sum('delivery_price');

                $form->rejects_amount = $rejected_products->count();
                $form->percent_rejects = number_format($this->percentExecute($rejected_products->count(), $new_order_items->count()), 2, '.', '');
                $form->outcome = $outcome;
                $form->in_delivery = $new_orders->where('status_accept', ShopOrder::status_accept['delivery_transfer'])->count();
                $form->returned_products_amount = $returned_products->count();
                $form->returned_products_price = $returned_products_price;
                $form->extra_outcome = $extra_outcome;
                $form->income = $income;

                /*$form->product_name = '';
                $form->products_amount = 0;
                $form->sold_products_amount = 0;
                $form->price_sold_products = 0;
                $form->percent_sold_products = 0;
                $form->delivery = 0;
                $form->rejects_amount = 0;
                $form->percent_rejects = 0;
                $form->outcome = 0;
                $form->in_delivery = 0;
                $form->returned_products_amount = 0;
                $form->returned_products_price = 0;
                $form->extra_outcome = 0;
                $form->income = 0;*/

                $forms[] = $form;
            }
        }

        return $forms;

    }

    #endregion

    #region Daily_report

    public function dailyReportSKd()
    {
        $orders = $this->orders;
        $couriers = $this->couriers;
        $shipments = $this->shipments;
        $companies = $this->companies;
        $place_regions = $this->place_regions;

        $forms = [];

        $current_date = date('Y-m-d');

        foreach ($couriers->all() as $courier) {

            $new_shipments = $shipments->where('shop_courier_id', $courier['id']);

            $company = $companies->where('id', $courier['user_company_id'])->first();
            $courier_regions = json_decode($courier['place_region_ids']);

            if ( $courier['place_region_ids'] )
                $place_region = $place_regions->where('id', $courier_regions[0])->first();
            $form = new ReportsDailyFormSkd();
//            if ( $courier['place_region_ids'] )
//                $place_region = $place_regions->where('id', $courier['place_region_ids'])->first();
//            $form = new ReportsDailyFormSkd();

            $form->courier_name = $courier['name'] ?? '---';

            $form->user_company_name = $company['name'] ?? '---';

            $form->place_region_name = $place_region['name'] ?? '---';

            foreach ($new_shipments as $ship) {

                $new_orders = $orders->where('shop_shipment_id', $ship['id']);
//                $new_orders = $orders->where('shop_shipment_id', $ship['id'])->where('created_at', $current_date);

                $sold_products = $new_orders->whereIn('status_logistics', ShopOrder::status_logistics['completed']);

                $rejected_products = $new_orders->whereIn('status_logistics', ShopOrder::status_accept['cancel']);

                $transferred_products = $new_orders->whereIn('status_logistics', ShopOrder::status_accept['delivery_transfer']);

                $form->total_products += $new_orders->count() ?? 0;

                $form->sold_products += $sold_products->count() ?? 0;

                $form->rejected_products += $rejected_products->count() ?? 0;

                $form->transferred_products += $transferred_products->count() ?? 0;

                $form->total_sold_products += $sold_products->sum('price') ?? 0;

            }

            $form->sold_products_percent = $this->percentExecute($form->sold_products, $form->total_products);

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



    //#check
    public function courierReport()
    {

        $orders = collect($this->orders);
        $order_items = collect($this->order_items);
        $catalogs = collect($this->catalogs);
        $shipments = collect($this->shipments);
        $couriers = collect($this->couriers);
        $companies = collect($this->companies);

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
}



