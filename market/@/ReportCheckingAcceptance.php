<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report  | Для проверки документа ПРИЕМКА
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;

//Для проверки документа ПРИЕМКА
class ReportCheckingAcceptance extends ZFrame
{
    public ?Collection $ware_accept = null;
    public ?Collection $shop_order = null;
    public ?Collection $shop_catalog = null;

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

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());

        if ($this->shop_catalog === null)
            $this->shop_catalog = collect(ShopCatalog::find()->asArray()->all());

        if ($this->ware_accept === null)
            $this->ware_accept = collect(WareAccept::find()->asArray()->all());
    }

    /**
     * @author NurbekMakhmudov
     * @todo get method running time
     */
    public function runningTime()
    {
        $boot = new \Boot();
        $boot->start();
        $this->test();
        echo $boot->finish();
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
     * @todo для проверка документа ПРЕМКА | Приемка  | get WareAccept name
     */
    public function getWareAcceptName(array $shop_order)
    {
        if ($shop_order['shop_shipment_id'] === null || $shop_order['shop_shipment_id'] === "") return null;

        $ware_accepts = $this->ware_accept->where('shop_shipment_id', $shop_order['shop_shipment_id'])->toArray();
        if ($ware_accepts === null) return null;

        $names = ZArrayHelper::map($ware_accepts,  'name', 'name');

        $name = null;
        foreach ($names as $item){
             $name = $item;
        }

        $res =  [
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
     * @Todo Для проверки документа ПРЕМКА | Номенклатура | get ShopCatalog title
     */
    public function getShopCatalogName(array $shop_order)
    {
        $shop_order_id = ZArrayHelper::getValue($shop_order, 'id');
        if ($shop_order_id === null) return null;

        $shop_catalog_id = $this->shop_order_item
            ->whereIn('shop_order_id', $shop_order_id)
            ->pluck('shop_catalog_id');
        if ($shop_catalog_id === null || $shop_catalog_id->isEmpty()) return null;

        $shop_catalog = $this->shop_catalog
            ->whereIn('id', $shop_catalog_id);
        if ($shop_catalog === null || $shop_catalog->isEmpty()) return null;

        $title = ZArrayHelper::getColumn($shop_catalog, 'title');
        if ($title === null || empty($title)) return null;

        $res =  [
            'value' => implode(", ", $title),
            'valueShow' => implode(", ", $title)
        ];
        return $res;
    }

    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * * @todo для проверка документа ПРЕМКА | Создан в | get created_at from WareAccept
     */
    public function getCreatedAt(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $ware_accepts = $this->ware_accept
            ->where('shop_shipment_id', $shop_shipment_id);

        $created_at = null;
        foreach ($ware_accepts as $ware_accept) {
            $created_at =  ZArrayHelper::getValue($ware_accept, 'created_at');
        }

        $res =  [
            'value' => $created_at,
            'valueShow' => $created_at
        ];
        return $res;
    }

    #endregion


}



