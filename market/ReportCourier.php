<?php

/**
 *
 * @author   Ravshanov Xolmat
 *
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareExitItem;
use zetsoft\models\ware\WareReturn;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class ReportCourier extends ZFrame
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

    public $filterOne = null;

    public $filterTwo = null;

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
            $this->ware_enter_item = collect(WareAccept::find()->asArray()->all());


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


        $this->filterOne = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');

        $this->filterTwo = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

    }



    /**
     * @param ShopCatalogWare $shopCatalogWare
     * @return  string|null
     * @throws \Exception
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSum
     */


    #endregion  getAmountSum
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     *  Отчет по курьерам | Всего
     */
    public function getAmountSum(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        if ($this->ware_accept->isEmpty())
            return [];

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('total');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;

    }
    #endregion




    #region  getCompletedOrderSum
    /**
     * @param array $shopCourier
     * @return string[]
     * @throws \Exception
     * Отчет по курьерам |  Выкуп
     */
    public function getCompletedOrderSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);


        $value = (string)$ware_accepts->sum('completed');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion


    #region  getPercentageCompletedSum
    /**
     * @param array $shopCourier
     * @return string[]|null
     * @throws \Exception
     *  Отчет по курьерам | Процент
     */
    public function getPercentageCompletedSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);


        $completed = $ware_accepts->sum('completed');
        $all = $ware_accepts->sum('total');

        if ((int)$completed === 0)
            return null;
        $percentage = (int)$completed * 100 / (int)$all;

        $percentage = number_format((float)$percentage, 2, '.', '') . " %";


        $res = [
            'value' => $percentage,
            'valueShow' => $percentage
        ];

        return $res;

    }
    #endregion





    #region
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Перенос
     */
    public function getDeliveryTransferOrderSum(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);


        $value = $ware_accepts->sum('date_transfer');


        $res = [
            'value' => $value,
            'valueShow' => $value
        ];
        return $res;
    }
    #endregion


    #region getPercentageDeliveryTransfer
    /**
     * @param array $shopCourier
     * @return string[]|null
     * @throws \Exception
     * Отчет по курьерам | Процент переносов
     */
    public function getPercentageDeliveryTransfer(array $shopCourier)
    {

        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $completed = $ware_accepts->sum('date_transfer');

        $all = $ware_accepts->sum('total');

        if ((int)$completed === 0)
            return null;

        $percentage = (int)$completed * 100 / (int)$all;

        $percentage = number_format((float)$percentage, 2, '.', '') . " %";

        $res = [
            'value' => $percentage,
            'valueShow' => $percentage
        ];

        return $res;
    }
    #endregion


    /*
     * Second table
     */

    #region  getSalesAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам |  Сумма
     */
    public function getSalesAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_at', [$this->filterOne, $this->filterTwo]);


        $value = $ware_accepts->sum('sales_amount');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion


    #region  getPercentageFromAll
    /**
     * @param array $shopCourier
     * @return string[]|null
     * @throws \Exception
     * Отчет по курьерам | 	Процент от общего
     */
    public function getPercentageFromAll(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $one = $ware_accepts->sum('sales_amount');

        $all = $this->ware_accept->sum('sales_amount');

        if ($one === 0)
            return null;

        $percentage = $one * 100 / $all;

        $percentage = number_format((float)$percentage, 2, '.', '') . " %";

        $res = [
            'value' => $percentage,
            'valueShow' => $percentage,
        ];

        return $res;
        //line||11
    }
    #endregion


    #region getTerminalAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Терминал
     *
     */
    public function getTerminalAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('terminal');


        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;

        //line|11
    }
    #endregion


    #region getCashelessAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Безналичные
     */
    public function getCashlessAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('cashless');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;

        //line|9
    }
    #endregion


    #region getAddDeliveryAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Доп доставки
     */
    public function getAddDeliveryAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('add_delivery');

        $res = [
            'value' => $value,
            'valueShow' => $value];

        return $res;

    }
    #endregion


    #region  getConvertedAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | \\\\$
     */
    public function getConvertedAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('converted');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion


    #region getBonusAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Бонус
     */
    public function getBonusAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('bonus');


        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion


    #region getAddDeliveryAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Сумма ВДС
     */
    public function getDcReturnAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        if ($ware_accepts->isEmpty())
            return [];

        $ware_accepts = $ware_accepts->first();

        $ware_return = $this->ware_return->where('id', $ware_accepts['dc_returns_group']);

        $value = $ware_return->sum('total_price');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion


    #region getAddDeliveryAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Оплата ВДС
     */
    public function getRefundRewardAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('refund_reward');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion


    #region getAddDeliveryAmount
    /**
     * @param array $shopCourier
     * @return array
     * @throws \Exception
     * Отчет по курьерам | Остаток
     */
    public function getRemainAmount(array $shopCourier)
    {
        $shop_courier_id = ZArrayHelper::getValue($shopCourier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        if ($this->filterOne !== null && $this->filterTwo !== null)
            $ware_accepts->whereBetween('closed_time', [$this->filterOne, $this->filterTwo]);

        $value = $ware_accepts->sum('remain');

        $res = [
            'value' => $value,
            'valueShow' => $value
        ];

        return $res;
    }
    #endregion
}




