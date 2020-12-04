<?php

/**
 * @author  Nurbek Makhmudov && Ravshanov Xolmat
 */

namespace zetsoft\service\market;

use zetsoft\former\reports\ReportsCdkForm;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\kernels\ZFrame;


class ReportCdkOLdv1 extends ZFrame
{
    #region  fillForm
    public function fillForm()
    {
        $return = [];

        $shop_orders = ShopOrder::find()->asArray()->all();

        foreach ($shop_orders as $shop_order) {

            $form = new ReportsCdkForm();
            $form->user_company_name = $this->getUserCompanyName($shop_order['id']);

            $form->courier_name = $this->getCourierName($shop_order['id']);
            $form->total = $this->getTotal($shop_order['id']);
            $form->completed = $this->getCompleted($shop_order['id']);
            $form->completed_percent = $this->getCompletedPercent($shop_order['id']);
            $form->refusal = $this->getRefusal($shop_order['id']);
            $form->date_transfer = $this->getDateTransfer($shop_order['id']);
            $form->sales_amount = $this->getSalesAmount($shop_order['id']);

            /// Перенос + Выкуп / Всего
            /// (date_transfer + completed) * 100 / total
            $form->closed_percent = $this->getClosedTransfer($shop_order['id']);
//            $form->closed_time = $this->getClosedTime($shop_order['id']);

            $return[] = $form;
        }

        return $return;
    }

    #endregion

    public function getUserCompanyName($shop_order_id)
    {

        $shop_order = ShopOrder::findOne($shop_order_id);
        if ($shop_order === null)
            return null;

        $shop_shipment = ShopShipment::findOne($shop_order_id);
        if ($shop_shipment === null)
            return null;

        $user_courier = ShopCourier::findOne($shop_shipment->shop_courier_id);

        if ($user_courier === null) return null;

        $user_company = UserCompany::findOne($user_courier->user_company_id);
        if ($user_company === null) return null;

        return $user_company->name;
    }

    public function getCourierName($shop_order_id)
    {

        $shop_order = ShopOrder::findOne($shop_order_id);
        if ($shop_order === null)
            return null;

        $shop_shipment = ShopShipment::findOne($shop_order->shop_shipment_id);

        if ($shop_shipment === null)
            return null;

        $user_courier = ShopCourier::findOne($shop_shipment->shop_courier_id);

        if ($user_courier === null) return null;

        return $user_courier->name;


    }

    public function getTotal($shop_order_id)
    {

        if ($this->getShopShipment($shop_order_id) === null) return null;

        $shop_shipments = $this->getShopShipment($shop_order_id);
        $total = 0;
        foreach ($shop_shipments as $shop_shipment) {
            $ware_accept = WareAccept::findOne($shop_shipment->ware_accept_id);
            if ($ware_accept !== null)
                $total += $ware_accept->total;
        }
        return $total;
    }

    public function getCompleted($shop_order_id)
    {
        if ($this->getShopShipment($shop_order_id) === null) return null;

        $shop_shipments = $this->getShopShipment($shop_order_id);
        $completed = 0;
        foreach ($shop_shipments as $shop_shipment) {
            $ware_accept = WareAccept::findOne($shop_shipment->ware_accept_id);
            if ($ware_accept !== null)
                $completed += $ware_accept->completed;
        }
        return $completed;
    }

    public function getCompletedPercent($shop_order_id)
    {
        $total =$this->getTotal($shop_order_id);
        if ($total === 0 || $total === null) return null;
        return $this->getCompleted($shop_order_id) * 100 / $total;
    }

    public function getRefusal($shop_order_id)
    {
        if ($this->getShopShipment($shop_order_id) === null) return null;

        $shop_shipments = $this->getShopShipment($shop_order_id);
        $refusal = 0;
        foreach ($shop_shipments as $shop_shipment) {
            $ware_accept = WareAccept::findOne($shop_shipment->ware_accept_id);
            if ($ware_accept !== null)
                $refusal += $ware_accept->refusal;
        }
        return $refusal;
    }

    public function getDateTransfer($shop_order_id)
    {
        if ($this->getShopShipment($shop_order_id) === null) return null;

        $shop_shipments = $this->getShopShipment($shop_order_id);
        $date_transfer = 0;
        foreach ($shop_shipments as $shop_shipment) {
            $ware_accept = WareAccept::findOne($shop_shipment->ware_accept_id);
            if ($ware_accept !== null)
                $date_transfer += $ware_accept->date_transfer;
        }
        return $date_transfer;
    }

    public function getSalesAmount($shop_order_id)
    {
        if ($this->getShopShipment($shop_order_id) === null) return null;

        $shop_shipments = $this->getShopShipment($shop_order_id);
        $sales_amount = 0;
        foreach ($shop_shipments as $shop_shipment) {
            $ware_accept = WareAccept::findOne($shop_shipment->ware_accept_id);
            if ($ware_accept !== null)
                $sales_amount += $ware_accept->sales_amount;

        }
        return $sales_amount;
    }

    public function getClosedTransfer($shop_order_id)
    {
        /// (date_transfer + completed) * 100 / total
        $total =$this->getTotal($shop_order_id);
        if ($total === 0 || $total === null) return null;
        return ($this->getDateTransfer($shop_order_id) + $this->getCompleted($shop_order_id)) * 100 / $total;
    }

    public function getClosedTime($shop_order_id){

        if ($this->getShopShipment($shop_order_id) === null) return null;

        $shop_shipments = $this->getShopShipment($shop_order_id);

        foreach ($shop_shipments as $shop_shipment) {

            $ware_accept = WareAccept::findOne($shop_shipment->ware_accept_id);

            if ($ware_accept !== null)
                $closed_time =  $ware_accept->closed_time;

        }

        return $closed_time;

    }


    public function getShopShipment($shop_order_id)
    {
        $shop_order = ShopOrder::findOne($shop_order_id);

        if ($shop_order === null) return null;

        $shop_shipment = ShopShipment::find()->where([
            'id' => $shop_order->shop_shipment_id,
        ])->all();

        if (empty($shop_shipment)) return null;

        return $shop_shipment;
    }

}



