<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

class ReportBroadcast extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-21

    //Для проверки документа ПЕРЕДАЧА
    public ?Collection $shop_shipment = null;
    public ?Collection $shop_order_item = null;
    public ?Collection $shop_catalogs = null;

    public $chess_id = null;

    public $filter = [];

    /**
     * @param array $filter
     * @throws \Exception
     * @license NurbekMakhmudov
     * @todo if Collection null, add new Collection
     */
    public function data($filter = [])
    {
        $this->chess_id = $this->paramGet('chess_id');
        $this->filter = $filter;

        if ($this->shop_shipment === null)
            $this->shop_shipment = collect(ShopShipment::find()->asArray()->all());

        if ($this->shop_order_item === null)
            $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());

        if ($this->shop_catalogs === null)
            $this->shop_catalogs = collect(ShopCatalog::find()->asArray()->all());

    }

    /**
     * @author NurbekMakhmudov
     */
    public function test()
    {
        $shopOrder = ShopOrder::find()
            ->asArray()
            ->all();

        $res = $this->getShopOrderItemName($shopOrder);
        vd($res);
    }


    #region Для проверки документа ПЕРЕДАЧА

    /**
     * @param array $shop_order
     * @return |null
     * @throws \Exception
     * @author NurbekMakhmudov
     * Для проверки документа ПЕРЕДАЧА |  Заказы переданные курьеру | get ShopShipment name
     */
    public function getOrdersTransferredToCourier(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $names = $this->shop_shipment
            ->where('id', $shop_shipment_id)
            ->pluck('name');

        $name = null;
        foreach ($names as $name)
            $name = ZArrayHelper::getValue($name, 'name');

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }


    /**
     * @param array $shop_order
     * @return |null
     * @throws \Exception
     * @author NurbekMakhmudov
     * Для проверки документа ПЕРЕДАЧА |  Товары | get ShopCatalog title
     */
    public function getShopCatalogTile(array $shop_orders)
    {
        $shop_order_id = ZArrayHelper::getValue($shop_orders, 'id');
        if ($shop_order_id === null) return null;

        $shop_order_item = $this->shop_order_item
            ->whereIn('shop_order_id', $shop_order_id);
        if ($shop_order_item === null || $shop_order_item->isEmpty()) return null;

        $shop_catalog_ids = $shop_order_item->pluck('shop_catalog_id')->toArray();

        $shop_catalogs = null;
        foreach ($shop_catalog_ids as $shop_catalog_id) {
            $shop_catalogs = $this->shop_catalogs
                ->whereIn('id', $shop_catalog_id);
            if ($shop_catalogs === null || $shop_catalogs->isEmpty()) return null;
        }

        $titles = $shop_catalogs->pluck('title')->toArray();

        foreach ($titles as $title) {
            $res = [
                'value' => $title,
                'valueShow' => $title
            ];
            return $res;
        }

    }

    // in Progress channel { 44 lines = 88 000 UZS }

//end|NurbekMakhmudov|2020-10-21


#endregion


}



