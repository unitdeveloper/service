<?php

/**
 * @author   Ravshanov Xolmat
 */

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


class ReportCatalog extends ZFrame
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

        $this->filterOne = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $this->filterTwo =   ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

    }

    /**
     *
     * Function  getTotalSum
     * @param array $shopCatalog
     * @return  array|string
     * @throws \Exception
     */

    #endregion  getTotalSum
    /**
     *
     * Function  getTotalSum
     * @param array $shopCatalog
     * @return  array
     * @throws \Exception
     * Выкупленные заказы | Количество
     */
    public function getTotalSum(array $shopCatalog)
    {
        $shop_catalog_id = ZArrayHelper::getValue($shopCatalog, 'id');

        if ($this->shop_order_item->isEmpty())
            return [];

        $shop_order_item = $this->shop_order_item->where('shop_catalog_id', $shop_catalog_id);


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $shop_order_item->whereBetween('created_at', [$this->filterOne, $this->filterTwo]);

        $total = 0;

        $shop_order_items = $shop_order_item->all();

        foreach ($shop_order_items as $shop_order_item) {
            $shop_order = $this->shop_order->where('id', $shop_order_item['shop_order_id'])->first();

            if (!empty($shop_order)) {

                if ($shop_order['status_logistics'] === 'completed' || $shop_order['status_logistics'] === 'part_paid') {
                    $total += $shop_order_item['amount'];

                }
            }
        }

        $total = (int)$total;

        //start|NurbekMakhmudov|2020-10-27
        if ($total === 0) return null;
        //end|NurbekMakhmudov|2020-10-27

        $res = [
            'value' => $total,
            'valueShow' => $total
        ];
        return $res;
        //line|18
    }
    #endregion


    /**
     *
     * Function  getTotalSum
     * @param array $shopCatalog
     * @return  array|string
     * @throws \Exception
     *
     * Выкупленные заказы |  Сумма
     */
    #endregion  getAmountSum
    public function getAmountSum(array $shopCatalog)
    {
        $shop_catalog_id = ZArrayHelper::getValue($shopCatalog, 'id');

        if ($this->shop_order_item->isEmpty())
            return [];

        $shop_order_item = $this->shop_order_item->where('shop_catalog_id', $shop_catalog_id);

        // filter uchun kerak
        $this->filterOne = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $this->filterTwo = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');


        if ($this->filterOne !== null && $this->filterTwo !== null)
            $shop_order_item->whereBetween('created_at', [$this->filterOne, $this->filterTwo]);


        $total = 0;

        $shop_order_items = $shop_order_item->all();

        foreach ($shop_order_items as $shop_order_item) {
            $shop_order = $this->shop_order->where('id', $shop_order_item['shop_order_id'])->first();

            if (!empty($shop_order))
                if ($shop_order['status_logistics'] === 'completed' || $shop_order['status_logistics'] === 'part_paid') {
                    $total += $shop_order_item['price_all'];
                }
        }

        $total = (int)$total;

        $res = [
            'value' => $total,
            'valueShow' => $total
        ];

        return $res;

    }
    #endregion
}



