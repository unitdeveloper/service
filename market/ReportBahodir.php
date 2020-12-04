<?php

/**
 * @author Bahodir
 * $todo For Universal EyufReport
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;
use function DusanKasan\Knapsack\only;
use function DusanKasan\Knapsack\sum;


class ReportBahodir extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_accept = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $shop_catalog_ware = null;
    public ?Collection $shop_order = null;
    public ?Collection $shop_order_item = null;
    public ?Collection $shop_shipment = null;
    public ?Collection $shop_courier = null;
    public ?Collection $user_company = null;
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

        if ($this->ware_enter === null)
            $this->ware_enter = collect(WareEnter::find()->asArray()->all());

        if ($this->user_company === null)
            $this->user_company = collect(UserCompany::find()->asArray()->all());

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());

        if ($this->shop_order_item === null)
            $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());

        if ($this->shop_shipment === null)
            $this->shop_shipment = collect(ShopShipment::find()->asArray()->all());

        if ($this->ware_accept === null)
            $this->ware_accept = collect(WareAccept::find()->asArray()->all());

        if ($this->ware === null)
            $this->ware = collect(Ware::find()->all());

        if ($this->shop_catalog_ware === null)
            $this->shop_catalog_ware = collect(ShopCatalogWare::find()->asArray()->all());

        if ($this->ware_enter_item === null)
            $this->ware_enter_item = collect(WareEnterItem::find()->asArray()->all());

        if ($this->shop_courier === null)
            $this->shop_courier = collect(ShopCourier::find()->asArray()->all());
    }



    #region Для проверки документа передача
    
    public function getDeliveryCount(array $shopOrder)
    {
        $shop_order_id = ZArrayHelper::getValue($shopOrder, 'id');
        if ( !$shop_order_id ) return null;

        $shop_order_items_count = $this->shop_order_item
            ->where('shop_order_id', $shop_order_id)
            ->count();
        return $shop_order_items_count;
    }


    public function getDeliveryCountGroup(array $shopOrder)
    {
//        $shop_order_id = ZArrayHelper::getValue($shopOrder, 'id');
//        if ( !$shop_order_id ) return null;
//
//        $shop_shipment = $this->shop_order
//            ->firstWhere('shop_shipment_id', $shop_order_id);
//        vd($shop_shipment);
//        $shop_shipment_name = ZArrayHelper::getValue($shop_shipment, 'name');

//        if ( !$shop_shipment_name ) return null;

//        return $shop_shipment_name;
    }

    #endregion



}



