<?php


namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExitItem;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;


class ReportCourierOld extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $ware_accept = null;
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
    }

    public function getEnterSumTest()
    {
        $boot = new \Boot();

        $boot->start();

        $this->getEnterSumTest1();

        echo $boot->finish();

    }


    #region getEntersum
    public function getEnterSumTest1()
    {

        $ware = ShopCatalogWare::find()->where([
            'id' => 141,
        ])->asArray()->one();

        $r = $this->getEnterSum($ware);


    }


    /**
     * @param ShopCatalogWare $shopCatalogWare
     * @return  string|null
     * @throws \Exception
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSum
     */

    public function getAmountSum(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $shop_shipments = $this->shop_shipment->where('shop_courier_id', $shop_courier_id);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_orders = $this->shop_order->where('id', $shop_shipment_ids);

        $shop_order_ids = ZArrayHelper::map($shop_orders->all(), 'id', 'id');

        $query = $this->shop_order_item->where('id', $shop_order_ids);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->whereBetween('created_at', [$time_before, $time_after]);

        $res_ids = ZArrayHelper::map($query->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
#endregion

    #region getCompletedOrderSum
    public function getCompletedOrderSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $shop_shipments = $this->shop_shipment->where('shop_courier_id', $shop_courier_id);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_orders_completed = $this->shop_order->where('id', $shop_shipment_ids)
            ->where('status_accept', 'completed');

        $shop_orders_completed_ids = ZArrayHelper::map($shop_orders_completed->all(), 'id', 'id');

        $query = $this->shop_order_item->where('id', $shop_orders_completed_ids);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->whereBetween('created_at', [$time_before, $time_after]);

        $res_ids = ZArrayHelper::map($query->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }

    #endregion


    #region  getPercentageCompletedSum
    public function getPercentageCompletedSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $shop_shipments = $this->shop_shipment->where('shop_courier_id', $shop_courier_id);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_orders = $this->shop_order->where('id', $shop_shipment_ids);

        $shop_orders_ids = ZArrayHelper::map($shop_orders->all(), 'id', 'id');

        $completed = $this->shop_order_item->where('id', $shop_orders_ids)
            ->where('status_accept', 'completed')->sum('amount');
        $all = $this->shop_order_item->where('id', $shop_orders_ids)->sum('amount');

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        $percentage = (int)$completed * 100 / (int)$all;

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


    #region   getDeliveryTransferOrderSum
    public function getDeliveryTransferOrderSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $shop_shipments = $this->shop_shipment->where('shop_courier_id', $shop_courier_id);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_orders_delivery_transfer = $this->shop_order->where('id', $shop_shipment_ids)
            ->where('status_accept', 'delivery_transfer');

        $shop_orders_delivery_transfer_ids = ZArrayHelper::map($shop_orders_delivery_transfer->all(), 'id', 'id');

        $query = $this->shop_order_item->where('id', $shop_orders_delivery_transfer_ids);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->whereBetween('created_at', [$time_before, $time_after]);

        $res_ids = ZArrayHelper::map($query->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

    }
    #endregion


    #region  getPercentageCompletedSum
    public function getPercentageDeliveryTransfer(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $shop_shipments = $this->shop_shipment->where('shop_courier_id', $shop_courier_id);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_orders = $this->shop_order->where('id', $shop_shipment_ids);

        $shop_orders_ids = ZArrayHelper::map($shop_orders->all(), 'id', 'id');

        $delivery_transfer = $this->shop_order_item->where('id', $shop_orders_ids)
            ->where('status_accept', 'delivery_transfer')->sum('amount');
        $all = $this->shop_order_item->where('id', $shop_orders_ids)->sum('amount');

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        $percentage = (int)$delivery_transfer * 100 / (int)$all;

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


    #region  getPercentageCompletedSum
    public function getSalesAmount(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('created_at', [$time_before, $time_after]);

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
            $ware_accepts->whereBetween('created_at', [$time_before, $time_after]);

        $one = $ware_accepts->sum('sales_amount');
        $all = $this->ware_accept->all();

        $percentage = (double)$one * 100 / (double)$all;

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


    #region  getPercentageCompletedSum
    public function getTerminalAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('created_at', [$time_before, $time_after]);

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
}



