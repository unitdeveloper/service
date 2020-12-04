<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopRejectCause;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareAccept;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;

class ReportNurbek extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_accept = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $shop_catalog_ware = null;
    public ?Collection $shop_reject_cause = null;
    public ?Collection $shop_order = null;
    public ?Collection $shop_order_item = null;
    public ?Collection $shop_shipment = null;
    public ?Collection $shop_courier = null;
    public ?Collection $shop_catalog = null;
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

        if ($this->shop_catalog === null)
            $this->shop_catalog = collect(ShopCatalog::find()->asArray()->all());

        if ($this->shop_reject_cause === null)
            $this->shop_reject_cause = collect(ShopRejectCause::find()->asArray()->all());

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
        $res = $this->getAcceptanceName($shopOrder);
        vd($res);
    }


    #region Причины отказов


    /**
     * @param array $shop_order
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Причины отказов | Приёмка | get WareAccept name
     */
    public function getAcceptanceName(array $shop_order)
    {
        $shop_orders = collect($shop_order);
        foreach ($shop_orders as $shop_order) {
            if ($shop_orders->pluck('shop_shipment_id')->isNotEmpty());
                $shop_shipment_id =  $shop_orders->pluck('shop_shipment_id');

//                vd($shop_shipment_id);
        }

        $ware_accepts = $this->ware_accept->where('shop_shipment_id', $shop_shipment_id);
//        vd($ware_accepts);

        $ware_accept = WareAccept::find()
            ->where([
                'shop_shipment_id' => $shop_shipment_id
            ])->one();

        if ($ware_accept === null) return null;

        return $ware_accept->name;
    }

    /**
     * @param array $ware_accept
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Причины отказов | Причина отказа | get ShopRejectCause name |
     */
    public function getRejectionReason(array $ware_accept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');
        if ($shop_shipment_id === null || $shop_shipment_id === "") return null;

        $shop_orders = $this->shop_order->where('shop_shipment_id', $shop_shipment_id);
        if ($shop_orders === null || $shop_orders->isEmpty()) return null;

        foreach ($shop_orders as $shop_order) {
            $shop_reject_cause_id = ZArrayHelper::getValue($shop_order, 'shop_reject_cause_id');
            $shop_reject_causes = $this->shop_reject_cause->where('id', $shop_reject_cause_id);
            if ($shop_reject_causes === null || $shop_reject_causes->isEmpty()) return null;

            $name = ZArrayHelper::getValue($shop_reject_causes, 'name');

            return $name;
        }
    }

    /**
     * @param array $ware_accept
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Причины отказов | Причина отказа | get ShopRejectCause name | NO used
     */
    public function getRejectionReasonn(array $ware_accept)
    {

        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');

        $shop_orders = $this->shop_order
            ->where('shop_shipment_id', $shop_shipment_id);
        if ($shop_orders === null || $shop_orders->isEmpty()) return null;

        foreach ($shop_orders as $shop_order) {
//            vd($shop_order);
        }

        $shop_reject_cause_id = ZArrayHelper::getValue($shop_orders, 'shop_reject_cause_id');
//        vd($shop_reject_cause_id);

        /*
                $shop_reject_cause_id = ZArrayHelper::getValue($shop_order, 'shop_reject_cause_id');
                if ($shop_reject_cause_id === null || $shop_reject_cause_id === "") return null;

                $shop_reject_causes = $this->shop_reject_cause->where('id', $shop_reject_cause_id);
                if ($shop_reject_causes === null) return null;

                foreach ($shop_reject_causes as $shop_reject_cause)
                    $name = ZArrayHelper::getValue($shop_reject_cause, 'name');
                return $name;
        */
    }


    /**
     * @param array $shop_order
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Причины отказов | Результат доставки | get ShopOrder status_deliver
     */
    public function deliveryResult(array $shop_order)
    {
        $shop_reject_cause_id = ZArrayHelper::getValue($shop_order, 'shop_reject_cause_id');
        if ($shop_reject_cause_id === null || $shop_reject_cause_id === "") return null;

        $shop_reject_causes = $this->shop_reject_cause->where('id', $shop_reject_cause_id);
        if ($shop_reject_causes === null || $shop_reject_causes->isEmpty()) return null;

        foreach ($shop_reject_causes as $shop_reject_cause)
            return ZArrayHelper::getValue($shop_reject_cause, 'name');
    }


    #endregion


    #region Для проверки документа ПЕРЕДАЧА

    /**
     * @param array $shop_order
     * @return |null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Для проверки документа ПЕРЕДАЧА |  Заказы переданные курьеру | get ShopShipment name
     */
    public function getOrdersTransferredToCourier(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $names = $this->shop_shipment
            ->where('id', $shop_shipment_id)
            ->pluck('name');

        foreach ($names as $name)
            return $name;
    }


    public function getReportedShopOrderName(array $shop_order)  /* no used */
    {
        /*$shop_orders = $this->shop_order
            ->where('status_logistics', '=', 'reported')
            ->pluck('id', 'name');

        foreach ($shop_orders as $shop_order) {
            vd($shop_order->name);
        }*/
    }


    #endregion


    #region для проверка документа ПРЕМКА

    /**
     * @param array $shop_order
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | Товарный состав | get WareAccept name  |  Done
     */
    public function getWareAcceptName(array $shop_order)
    {
        $shop_orders = $this->shop_order->where('id', '=', 1955);
//        vd($shop_orders);

        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

//        vd($shop_shipment_id);

        $names = $this->ware_accept
            ->where('shop_shipment_id', $shop_shipment_id)
            ->pluck('name');
        if ($names === null || $names->isEmpty()) return null;

//        vd($names);

        foreach ($names as $name)
            return $name;
    }


    /**
     * @param array $shop_order
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @Todo Для проверки документа ПРЕМКА | Номенклатура |  OK
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

        return implode(", ", $title);
    }


    /**
     * @param array $wareAccept
     * @return array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | get ShopOrder name where shop_order.shop_shipment_id = ware_accept.shop_shipment_id
     */
    public function getShopOrderName(array $wareAccept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($wareAccept, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $shop_order_names = $this->shop_order
            ->where('shop_shipment_id', $shop_shipment_id)
            ->all();
        if ($shop_order_names === null) return null;

        $names = ZArrayHelper::getColumn($shop_order_names, 'name');
        if ($names === null) return null;

        foreach ($names as $name) {
            return $name;
        }
    }


    /**
     * @param array $ware_accept
     * @return mixed
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | Результат доставки | status_logistics from ShopOrder
     */
    public function getStatusLogistics(array $ware_accept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $status_logistics = $this->shop_order
            ->where('shop_shipment_id', $shop_shipment_id)
            ->pluck('status_logistics');

        foreach ($status_logistics as $status_logistic) {
            return (new ShopOrder())->_status_logistics[$status_logistic];
        }
    }


    /**
     * @param array $shop_order
     * @return mixed|null
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

        foreach ($ware_accepts as $ware_accept) {
            return ZArrayHelper::getValue($ware_accept, 'created_at');
        }
    }

    /**
     * @param array $ware_accept
     * @return mixed|null
     * @throws \Exception
     * @todo для проверка документа ПРЕМКА | Номенклатура | get name from ShopOrderItem
     * @author NurbekMakhmudov
     */
    public function getShopOrderItemName(array $ware_accept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');

        $shop_order_ids = $this->shop_order
            ->whereIn('shop_shipment_id', $shop_shipment_id)
            ->pluck('id');

        foreach ($shop_order_ids as $shop_order_id) {
            $names = $this->shop_order_item
                ->where('shop_order_id', $shop_order_id)
                ->pluck('name');

            foreach ($names as $name)
                return $name;
        }
    }


    /**
     * @param array $ware_accept
     * @return mixed
     * @throws \Exception
     * @todo для проверка документа ПРЕМКА | колво | get amount from ShopOrderItem
     * @author NurbekMakhmudov
     */
    public function getShopOrderItemAmount(array $ware_accept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');

        $shop_order_ids = $this->shop_order
            ->whereIn('shop_shipment_id', $shop_shipment_id)
            ->pluck('id');

        foreach ($shop_order_ids as $shop_order_id) {
            $amounts = $this->shop_order_item
                ->where('shop_order_id', $shop_order_id)
                ->pluck('amount');

            foreach ($amounts as $amount)
                return $amount;
        }
    }

    public function getOrderNameSortedByComplectWait(array $shopOrder)   /*no worked*/
    {
        $shop_order_name = ZArrayHelper::getColumn($shopOrder, 'name');
        $status_logistics = ZArrayHelper::getColumn($shopOrder, 'status_logistics');
    }

    #endregion


    #region  СКД Отчет Ежедневный

    /**
     * @param array $shop_courier
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo СКД Отчет Ежедневный  | Проекты шоп | get  Name from UserCompany
     */
    public function getUserCompanyName(array $shop_courier)    /*no worked*/
    {
        $shop_courier_id = ZArrayHelper::getValue($shop_courier, 'id');

        $ware_accepts = $this->ware_accept->where('shop_courier_id', $shop_courier_id);

        foreach ($ware_accepts as $ware_accept) {

            $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');

//            vd($shop_shipment_id);

            $shop_orders = $this->shop_order->where('shop_shipment_id', $shop_shipment_id)->pluck('source');
        }

    }


    /**
     * @param array $shop_shipment
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo СКД Отчет Ежедневный  | get Total from WareAccept
     */
    public function getWareAcceptTotal(array $shop_shipment)
    {
        $ware_accept_id = ZArrayHelper::getValue($shop_shipment, 'ware_accept_id');
        if ($ware_accept_id === null) return null;

        $ware_accept = $this->ware_accept
            ->firstWhere('id', $ware_accept_id);

        $total = ZArrayHelper::getValue($ware_accept, 'total');
        if ($total === null) return null;

        return $total;
    }

    /**
     * @param array $shop_shipment
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo СКД Отчет Ежедневный  | get Completed from WareAccept
     */
    public function getWareAcceptCompleted(array $shop_shipment)
    {
        $ware_accept_id = ZArrayHelper::getValue($shop_shipment, 'ware_accept_id');
        if ($ware_accept_id === null) return null;

        $ware_accept = $this->ware_accept
            ->firstWhere('id', $ware_accept_id);

        $completed = ZArrayHelper::getValue($ware_accept, 'completed');
        if ($completed === null) return null;
        return $completed;
    }

    /**
     * @param array $shop_shipment
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo СКД Отчет Ежедневный  | calculate Percent = Completed * 100 / Total from WareAccept
     */
    public function getWareAcceptPercent(array $shop_shipment)
    {
        $ware_accept_id = ZArrayHelper::getValue($shop_shipment, 'ware_accept_id');
        if ($ware_accept_id === null) return null;

        $ware_accept = $this->ware_accept
            ->firstWhere('id', $ware_accept_id);

        $completed = ZArrayHelper::getValue($ware_accept, 'completed');
        $total = ZArrayHelper::getValue($ware_accept, 'total');
        if ($total === null || $completed === null) return null;

        $return = $completed * 100 / $total;
        /*$return = (int)substr($return, 0, strpos($return, "."));*/
        return (int)$return . ' %';
    }

    #endregion


    #region Отчет по курьерам

    /**
     * @param array $wareAccept
     * @return float|int
     * @throws \Exception
     * @todo  Отчет по курьерам | Процент | completed qiymati total qiymatining necha foizi ekanligini xisoblaydi
     * @author NurbekMakhmudov
     */
    public function getPercent(array $wareAccept)
    {
        $total = ZArrayHelper::getValue($wareAccept, 'total');
        $completed = ZArrayHelper::getValue($wareAccept, 'completed');
        if ($total === null || $completed === null || $total === 0) return null;

        $return = $completed * 100 / $total;

        return (int)$return . ' %';
    }


    /**
     * @param array $wareAccept
     * @return mixed|null
     * @throws \Exception
     * @todo Отчет по курьерам | Перенос | get delivery transfer count  from  shop_order  where status_logistics = delivery_transfer
     * @author NurbekMakhmudov
     */
    public function getTransferCount(array $ware_accept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $transfer_count = $this->shop_order
            ->where('shop_shipment_id', $shop_shipment_id)
            ->whereIn('status_logistics', 'delivery_transfer')
            ->count();
        if ($transfer_count === null) return null;

        return $transfer_count;
    }

    /**
     * @param array $wareAccept
     * @return mixed|null
     * @throws \Exception
     * @todo Отчет по курьерам | Процент переносов | TransferCount qiymati total qiymatining necha foizi ekanligini xisoblaydi
     * @author NurbekMakhmudov
     */
    public function getTransferPercent(array $ware_accept)
    {
        $total = ZArrayHelper::getValue($ware_accept, 'total');
        $date_transfer = ZArrayHelper::getValue($ware_accept, 'date_transfer');
        if ($total === null || $total === 0 || $date_transfer === null) return null;

        $res = ($date_transfer / $total) * 100;
        return number_format($res, 1, '.', '') . ' %';
    }


    /**
     * @param array $ware_accept
     * @return string|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Отчет по курьерам | Процент по закрытым |
     */
    public function getPercentageOnClosed(array $ware_accept)
    {
        $completed = ZArrayHelper::getValue($ware_accept, 'completed');
        $date_transfer = ZArrayHelper::getValue($ware_accept, 'date_transfer');
        $total = ZArrayHelper::getValue($ware_accept, 'total');
        if ($total === null || $total === 0 || $date_transfer === null || $completed === null) return null;

        $res = ($completed + $date_transfer) / $total;
        return number_format($res, 1, '.', '') . ' %';
    }

    public function getPercentageOfTotal(array $ware_accept)
    {
        $all_sales_amount_sum = $this->ware_accept
            ->sum('sales_amount');
        $sales_amount = ZArrayHelper::getValue($ware_accept, 'sales_amount');
        $res = ($sales_amount / $all_sales_amount_sum) * 100;
        return number_format($res, 1, '.', '') . ' %';
        //336
    }


    #endregion


}



