<?php

/**
 * @author  Xolmat Ravshanov
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\former\reports\ReportsCompletedForm;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ReportSource extends ZFrame
{

    public ?Collection $shop_orders;
    public ?Collection $shop_shipments;
    public ?Collection $ware_accepts;
    public ?Collection $shop_catalogs;
    public ?Collection $shop_order_item;


    public function init()
    {
        parent::init();
        $this->shop_shipments = collect(ShopShipment::find()->asArray()->all());
        $this->shop_orders = collect(ShopOrder::find()->asArray()->all());
        $this->ware_accepts = collect(WareAccept::find()->asArray()->all());
        $this->shop_catalogs = collect(ShopCatalog::find()->asArray()->all());
        $this->shop_order_item = collect(ShopOrderItem::find()->asArray()->all());
    }

    #region  fillForm
    public function fillForm($beginDate = null, $endDate = null)
    {
        if ($beginDate === null)
            $beginDate = Az::$app->cores->date->dateTime('-24 hours');

        if ($endDate === null)
            $endDate = Az::$app->cores->date->dateTime();

        $return = [];

        foreach ($this->shop_catalogs as $shop_catalog) {

            $catalogTotal = 0;
            $catalogAmount = 0;


            $ware_total = 0;

            //ware_accept
            $completed = new ReportsCompletedForm();

            $completed->shop_order = $shop_catalog['name'];
            $completed->user_company = $shop_catalog['user_company_id'];


            $shop_order_items = $this->shop_order_item->where('shop_catalog_id', $shop_catalog['id'])->all();
            if ($shop_order_items === null)
                return null;


            foreach ($shop_order_items as $shop_order_item) {

                $total = 0;

                $amount = 0;

                $ware_amount = 0;

                $shop_order = ShopOrder::findOne($shop_order_item['shop_order_id']);

                if ($shop_order === null)
                    continue;


                if ($shop_order->status_accept !== ShopOrder::status_accept['completed'])
                    continue;

                $shop_shipment = ShopShipment::findOne($shop_order->shop_shipment_id);

                if ($shop_shipment === null)
                    continue;

                $ware_accept = WareAccept::findOne($shop_shipment['id']);


                if ($ware_accept === null)
                    break;


                $total += $shop_order_item->price_all;

                $amount += $shop_order_item->amount;

                $ware_amount += $ware_accept->sales_amount;

                $ware_total += $total;

                $completed->shop_order = $shop_order->name;

                $completed->total = $total;

                $completed->amount = $amount;


            }

            $completed->ware_total = $ware_total;

            $completed->ware_amount = $ware_amount;

            $return[] = $completed;

        }

        return $return;

    }

    #endregion

}



