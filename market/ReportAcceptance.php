<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report | Acceptance  | для проверка документа ПРЕМКА
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


/**
 * Class ReportAcceptance
 * для проверка документа ПРЕМКА
 * @package zetsoft\service\market
 * @author NurbekMakhmudov
 */
class ReportAcceptance extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-15

    public ?Collection $ware_accept = null;
    public ?Collection $shop_order = null;
    public ?Collection $shop_order_item = null;
    public ?Collection $shop_catalog = null;
    public ?Collection $shop_shipment = null;

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

        if ($this->ware_accept === null)
            $this->ware_accept = collect(WareAccept::find()->asArray()->all());

        if ($this->shop_catalog === null)
            $this->shop_catalog = collect(ShopCatalog::find()->asArray()->all());

        if ($this->shop_order_item === null)
            $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());

        if ($this->shop_shipment === null)
            $this->shop_shipment = collect(ShopShipment::find()->asArray()->all());

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());
    }


    /**
     * @author NurbekMakhmudov
     */
    public function test()
    {
        $shopOrder = ShopOrder::find()
            ->asArray()
            ->all();
        $res = $this->getWareAcceptName($shopOrder);
        vd($res);
    }


    #region для проверка документа ПРЕМКА

    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | Создан в | get created_at from WareAccept
     */
    public function getCreatedAt(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $ware_accepts = $this->ware_accept
            ->where('shop_shipment_id', $shop_shipment_id);
        if ($ware_accepts === null || $ware_accepts->isEmpty()) return null;

        $created_at = null;
        foreach ($ware_accepts as $ware_accept)
            $created_at = ZArrayHelper::getValue($ware_accept, 'created_at');

        $res = [
            'value' => $created_at,
            'valueShow' => $created_at
        ];
        return $res;
    }


    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | Товарный состав | get WareAccept name
     */
    public function getWareAcceptName(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null || $shop_shipment_id === "") return null;

        $ware_accepts = $this->ware_accept
            ->where('shop_shipment_id', $shop_shipment_id);
        if ($ware_accepts === null || $ware_accepts->isEmpty()) return null;

        $name = null;
        foreach ($ware_accepts as $ware_accept)
            $name = ZArrayHelper::getValue($ware_accept, 'name');

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }


    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @Todo Для проверки документа ПРЕМКА | Номенклатура |
     */
    public function getShopCatalogName(array $shop_order)
    {
        $shop_order_id = ZArrayHelper::getValue($shop_order, 'id');
        if ($shop_order_id === null) return null;

        $shop_catalog_ids = $this->shop_order_item
            ->whereIn('shop_order_id', $shop_order_id)
            ->pluck('shop_catalog_id');
        if ($shop_catalog_ids === null || $shop_catalog_ids->isEmpty()) return null;

        $shop_catalog = $this->shop_catalog
            ->whereIn('id', $shop_catalog_ids);
        if ($shop_catalog === null || $shop_catalog->isEmpty()) return null;

        $name = ZArrayHelper::getColumn($shop_catalog, 'name');
        if ($name === null || empty($name)) return null;

        $res = [
            'value' => implode(", ", $name),
            'valueShow' => implode(", ", $name)
        ];
        return $res;
    }



    #endregion


    //end|NurbekMakhmudov|2020-10-15

    // in Progress channel { 64 lines = 128 000 UZS }



}



