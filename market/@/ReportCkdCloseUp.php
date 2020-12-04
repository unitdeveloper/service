<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\former\reports\ReportsCdkForm;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExitItem;
use zetsoft\models\ware\WareReturn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

class ReportCkdCloseUp extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $ware_accept = null;
    public ?Collection $ware_return = null;
    public ?Collection $shop_shipment = null;
    public ?Collection $shop_order_item = null;
    public ?Collection $shop_order = null;
    public ?Collection $shop_catalog = null;
    public ?Collection $shop_catalog_ware = null;
    public ?Collection $ware_exit_items = null;

    public $chess_id = null;

    public $filter = [];

    public function data($filter = [])
    {
        $this->chess_id = $this->paramGet('chess_id');

        $this->filter = $filter;

        if ($this->ware === null)
            $this->ware = collect(Ware::find()->all());

        if ($this->ware_enter === null)
            $this->ware_enter = collect(WareEnter::find()->asArray()->all());

        if ($this->ware_enter_item === null)
            $this->ware_enter_item = collect(WareEnterItem::find()->asArray()->all());


        if ($this->shop_catalog_ware === null)
            $this->shop_catalog_ware = collect(ShopCatalogWare::find()->asArray()->all());

        if ($this->ware_exit_items === null)
            $this->ware_exit_items = collect(WareExitItem::find()->asArray()->all());

        if ($this->shop_order_item === null)
            $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());

        if ($this->shop_shipment === null)
            $this->shop_shipment = collect(ShopShipment::find()->asArray()->all());

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());

        if ($this->ware_accept === null)
            $this->ware_accept = collect(\zetsoft\models\ware\WareAccept::find()->asArray()->all());

        if ($this->ware_return === null)
            $this->ware_return = collect(WareReturn::find()->asArray()->all());
    }


    public function fillForm()
    {
        $return = [];

        $shop_orders = collect(ShopOrder::find()->asArray()->all());

        foreach ($shop_orders as $shop_order) {

            $form = new ReportsCdkForm();
            $form->user_company_name = $shop_order['total'];
            $form->courier_name = $shop_order['total'];
            $form->total = $this->getCourierName($shop_order['id']);
            $form->completed = $this->getCourierName($shop_order['id']);
            $form->completed_percent = $this->getCourierName($shop_order['id']);
            $form->refusal = $this->getCourierName($shop_order['id']);
            $form->date_transfer = $this->getCourierName($shop_order['id']);
            $form->sales_amount = $this->getCourierName($shop_order['id']);
            /// Перенос + Выкуп / Всего
            $form->closed_percent = $this->getCourierName($shop_order['id']);

            $return[] = $form;
        }

        return $return;

    }


    public function getTotal($shop_order_id)
    {
        if ($this->getShopShipment($shop_order_id) === null) return null;
        $ware_accept = WareAccept::findOne($this->getShopShipment($shop_order_id)->ware_accept_id);
        if ($ware_accept === null) return null;

        return $ware_accept->total;
    }


    public function getShopShipment($shop_order_id)
    {
        $shop_order = ShopOrder::findOne($shop_order_id);
        if ($shop_order === null) return null;

        $shop_shipment = ShopShipment::find()->where([
            'id' => $shop_order->shop_shipment_id,
        ]);
        if ($shop_shipment === null) return null;

        return $shop_shipment;
    }


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

            if ($courier['place_region_ids'])
                $place_region = $place_regions->where('id', $courier_regions[0])->first();
            $form = new ReportCkdCloseUp();
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

    #endregion  getAmountSum


    /**
     * @param ShopCatalogWare $shopCatalogWare
     * @return  string|null
     * @throws \Exception
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSum
     */
    public function getAmountSum()
    {

        $return = [];

        $shop_orders = collect(ShopOrder::find()->asArray()->all());

        foreach ($shop_orders as $shop_order) {

            $form = new ReportsCdkForm();
            $form->total = $shop_order['total'];
            $form->sales_amount = $shop_order['total'];
            $form->courier_name = $this->getCourierName($shop_order['id']);

            $return[] = $form;

        }


        return $return;

    }
    #endregion

    #endregion  getCompletedOrderSum
    public function getCompletedOrderSum(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $value = (string)$ware_accepts->sum('completed');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region  getPercentageCompletedSum
    public function getPercentageCompletedSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $completed = $ware_accepts->sum('completed');
        $all = $ware_accepts->sum('total');

        if ((int)$completed === 0)
            return null;
        $percentage = (int)$completed * 100 / (int)$all;
        $percentage = $percentage . " %";
        if ($this->emptyOrNullable($percentage)) {
            return <<<HTML
             <div>$percentage</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$percentage</a>
HTML;

    }

    #endregion


    public function getDeliveryTransferOrderSum(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $value = $ware_accepts->sum('date_transfer');


        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getPercentageDeliveryTransfer
    public function getPercentageDeliveryTransfer(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $completed = $ware_accepts->sum('date_transfer');
        $all = $ware_accepts->sum('total');

        if ((int)$completed === 0)
            return null;

        $percentage = (int)$completed * 100 / (int)$all;

        if ($this->emptyOrNullable($percentage)) {
            return <<<HTML
             <div>$percentage</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$percentage</a>
HTML;

    }
    #endregion


    /*
     * Second table;
     */

    #region  getPercentageCompletedSum
    public function getSalesAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_at', [$time_before, $time_after]);


        $value = $ware_accepts->sum('sales_amount');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region  getPercentageCompletedSum
    public function getPercentageFromAll(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');


        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $one = $ware_accepts->sum('sales_amount');

        $all = $this->ware_accept->sum('sales_amount');

        if ($one === 0)
            return null;

        $percentage = $one * 100 / $all;

        $percentage = (double)$percentage . " %";

        if ($this->emptyOrNullable($percentage)) {
            return <<<HTML
             <div>$percentage</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$percentage</a>
HTML;

    }
    #endregion


    #region getTerminalAmount
    public function getTerminalAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('terminal');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getCashelessAmount
    public function getCashlessAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('cashless');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getAddDeliveryAmount
    public function getAddDeliveryAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('add_delivery');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region  getConvertedAmount
    public function getConvertedAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('converted');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getBonusAmount
    public function getBonusAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('bonus');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getAddDeliveryAmount
    public function getDcReturnAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        if ($ware_accepts->isEmpty())
            return [];

        $ware_accepts = $ware_accepts->first();

        $ware_return = $this->ware_return->where('id', $ware_accepts['dc_returns_group']);

        $value = $ware_return->sum('total_price');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getAddDeliveryAmount
    public function getRefundRewardAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('refund_reward');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region getAddDeliveryAmount
    public function getRemainAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);

        $value = $ware_accepts->sum('remain');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion

}



