<?php


namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExitItem;
use zetsoft\models\ware\WareReturn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;



class ReportCourierold1 extends ZFrame
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


    /**
     * @param ShopCatalogWare $shopCatalogWare
     * @return  string|null
     * @throws \Exception
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSum
     */
    #endregion  getAmountSum
    public function getAmountSum(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        if($this->ware_accept->isEmpty())
            return [];
            
        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $ware_accept_ids = ZArrayHelper::map($ware_accepts->all(), 'id', 'id');

        $shop_shipments = $this->shop_shipment->where('id', $ware_accept_ids);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_order = $this->shop_order->where('shop_shipment_ids', $shop_shipment_ids);

        $shop_order_ids = ZArrayHelper::map($shop_order->all(), 'id', 'id');

        $shop_order_items = $this->shop_order_item->where('id', $shop_order_ids);


        $res_ids = ZArrayHelper::map($shop_order_items->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$shop_order_items->sum('amount');

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


        $ware_accept_ids = ZArrayHelper::map($ware_accepts->all(), 'id', 'id');

        $shop_shipments = $this->shop_shipment->where('id', $ware_accept_ids);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_order = $this->shop_order->where('shop_shipment_ids', $shop_shipment_ids)->where('status_accept', 'completed');;

        $shop_order_ids = ZArrayHelper::map($shop_order->all(), 'id', 'id');

        $shop_order_items = $this->shop_order_item->where('id', $shop_order_ids);


        $res_ids = ZArrayHelper::map($shop_order_items->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$shop_order_items->sum('amount');

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

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $ware_accept_ids = ZArrayHelper::map($ware_accepts->all(), 'id', 'id');

        $shop_shipments = $this->shop_shipment->where('id', $ware_accept_ids);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_order = $this->shop_order->where('shop_shipment_ids', $shop_shipment_ids)->where('status_accept', 'completed');;

        $shop_order_ids = ZArrayHelper::map($shop_order->all(), 'id', 'id');

        $completed = $this->shop_order_item->where('id', $shop_order_ids)
            ->where('status_accept', 'completed')->sum('amount');
        $all = $this->shop_order_item->where('id', $shop_order_ids)->sum('amount');

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


    public function getDeliveryTransferOrderSum(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $ware_accepts->whereBetween('closed_time', [$time_before, $time_after]);


        $ware_accept_ids = ZArrayHelper::map($ware_accepts->all(), 'id', 'id');

        $shop_shipments = $this->shop_shipment->where('id', $ware_accept_ids);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_order = $this->shop_order->where('shop_shipment_ids', $shop_shipment_ids)->where('status_accept', 'delivery_transfer');;

        $shop_order_ids = ZArrayHelper::map($shop_order->all(), 'id', 'id');

        $shop_order_items = $this->shop_order_item->where('id', $shop_order_ids);


        $res_ids = ZArrayHelper::map($shop_order_items->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$shop_order_items->sum('amount');

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


        $ware_accept_ids = ZArrayHelper::map($ware_accepts->all(), 'id', 'id');

        $shop_shipments = $this->shop_shipment->where('id', $ware_accept_ids);

        $shop_shipment_ids = ZArrayHelper::map($shop_shipments->all(), 'id', 'id');

        $shop_order_transfer = $this->shop_order->where('shop_shipment_ids', $shop_shipment_ids)->where('status_accept', 'delivery_transfer');

        $shop_order = $this->shop_order->where('shop_shipment_ids', $shop_shipment_ids);

        $shop_order_transfer_ids = ZArrayHelper::map($shop_order_transfer->all(), 'id', 'id');

        $shop_order_ids = ZArrayHelper::map($shop_order->all(), 'id', 'id');


        $completed = $this->shop_order_item->where('id', $shop_order_transfer_ids)->sum('amount');
        $all = $this->shop_order_item->where('id', $shop_order_ids)->sum('amount');

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

        $percentage = (double)$percentage. " %";
        
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



