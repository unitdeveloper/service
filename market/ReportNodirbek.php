<?php

/**
 * @author NurbekMakhmudov
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
use zetsoft\service\ALL\Select;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;
use function Dash\Curry\pluck;
use function DusanKasan\Knapsack\only;
use function DusanKasan\Knapsack\sum;


class ReportNodirbek extends ZFrame
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

    /**
     * @author NurbekMakhmudov
     * @todo get method running time
     */
    public function runningTime()
    {
        $boot = new \Boot();
        $boot->start();
        $this->getPercentTest();
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
        $res = $this->getReportedShopOrderName($shopOrder);
        vd($res);
    }

    #region Для проверки документа ПЕРЕДАЧА

    //Заказы переданные курьеру  = ShopShipment name
//    public function getOrdersTransferredToCourier(array $shop_order)
//    {
//        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
//        if ($shop_shipment_id === null) return null;
//
//        $this->shop_shipment
//            ->where('')
//
//    }

    /**
     * @param array $shop_order
     * @return |null
     * @throws \Exception
     * author NodirbekOmonov
     */

    //Заказ клиента   ShopOrder name where status_logistics = reported   Передан в подотчёт
    //Ser. #
    //Дата доставки  = ShopShipment date_deliver

    public function getReportedShopOrderName(array $shop_order)
    {
        vd($shop_order);

        $status_logistics = ZArrayHelper::getValue($shop_order, 'status_logistics');
        if ($status_logistics === null) return null;

        vd($status_logistics);

        $names = $this->shop_order
            ->where( 'status_logistics', '=', 'reported')
            ->pluck('name')
            ->all();
//            ->where('status_logistics', '=', 'reported')
//            ->pluck('name')
//            ->all();
////        foreach ($names as $name)
//            return;
    }

    #endregion


    #region для проверка документа ПРЕМКА

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

        foreach ($names as $name)
            return $name;
    }


    /**
     * @param array $wareAccept
     * @return mixed
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | Результат доставки | status_logistics from ShopOrder
     */
    public function getStatusLogistics(array $shop_order)
    {
        $status_logistics = ZArrayHelper::getValue($shop_order, 'status_logistics');
        if ($status_logistics === null) return null;
        $status = (new ShopOrder())->_status_logistics[$status_logistics];
        if ($status === null) return null;
        return $status;
    }

    /**
     * @param array $shop_order
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo для проверка документа ПРЕМКА | Товарный состав | get WareAccept name
     */
    public function getWareAcceptName(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null) return null;

        $ware_accept_id = $this->shop_shipment
            ->where('id', $shop_shipment_id)
            ->pluck('ware_accept_id');
        if ($ware_accept_id === null) return null;

        foreach ($ware_accept_id as $id) {

            $names = $this->ware_accept
                ->where('id', $id)
                ->pluck('name');
            if ($names === null) return null;

            foreach ($names as $name) {
                if ($name === null) return null;
                return $name;
            }
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
     * @param array $shop_order
     * @return mixed|null
     * @throws \Exception
     * @todo для проверка документа ПРЕМКА | Номенклатура | get name from ShopOrderItem
     * @author NurbekMakhmudov
     */
    public function getShopOrderItemName(array $shop_order)
    {
        $id = ZArrayHelper::getValue($shop_order, 'id');

        $shop_order_item = $this->shop_order_item
            ->where('shop_order_id', $id);

        $names = ZArrayHelper::getColumn($shop_order_item, 'name');
        foreach ($names as $name)
            return $name;
    }


    /**
     * @param array $shop_order
     * @return mixed
     * @throws \Exception
     * @todo для проверка документа ПРЕМКА | колво | get amount from ShopOrderItem
     * @author NurbekMakhmudov
     */
    public function getShopOrderItemAmount(array $shop_order)
    {
        $id = ZArrayHelper::getValue($shop_order, 'id');

        $shop_order_item = $this->shop_order_item
            ->where('shop_order_id', $id);

        $amounts = ZArrayHelper::getColumn($shop_order_item, 'amount');
        foreach ($amounts as $amount)
            return $amount;
    }

    public function getOrderNameSortedByComplectWait(array $shopOrder)
    {
        $shop_order_name = ZArrayHelper::getColumn($shopOrder, 'name');
        $status_logistics = ZArrayHelper::getColumn($shopOrder, 'status_logistics');

    }

    #endregion

    #region  СКД Отчет Ежедневный

    /**
     * @author NurbekMakhmudov
     */
    public function getUserCompanyNameTest()
    {
        $shopShipment = ShopShipment::find()
            ->asArray()
            ->all();
        $res = $this->getUserCompanyName($shopShipment);
        vd($res);
    }


    /**
     * @param array $shop_shipment
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo СКД Отчет Ежедневный  | get  Name from UserCompany
     */
    public function getUserCompanyName(array $wareAccept)
    {
        $shop_courier_id = ZArrayHelper::getValue($wareAccept, 'shop_courier_id');
        if ($shop_courier_id === null) return null;

        $shop_courier = $this->shop_courier
            ->firstWhere('id', $shop_courier_id);

        $user_company_id = ZArrayHelper::getValue($shop_courier, 'user_company_id');
        if ($user_company_id === null) return null;

        $user_company = $this->user_company
            ->firstWhere('id', $user_company_id);

        $name = ZArrayHelper::getValue($user_company, 'name');
        if ($user_company_id === null) return null;

        return $name;
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
     * @author NurbekMakhmudov
     */
    public function getPercentTest()
    {
        $ware_accept = WareAccept::find()
            ->asArray()
            ->one();
        $res = $this->getPercent($ware_accept);
        vd($res);
    }

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
     * @author NurbekMakhmudov
     */
    public function getTransferCountTest()
    {
        $ware_accept = WareAccept::find()
            ->asArray()
            ->all();
        $res = $this->getTransferCount($ware_accept);
        vd($res);
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

    public function getTransferPercentTest()
    {
        $ware_accept = WareAccept::find()
            ->asArray()
            ->one();
        $res = $this->getPercent($ware_accept);
        vd($res);
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


    public function getPercentageOnClosedTest()
    {
        $ware_accept = WareAccept::find()
            ->asArray()
            ->all();
        $res = $this->getPercentageOnClosed($ware_accept);
        vd($res);
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


    #region EnterSum

    public function getEnterSumTest()
    {
        $shopCatalogWare = ShopCatalogWare::find()->where([
            'id' => 141,
        ])->asArray()->one();
        $this->getEnterSum($shopCatalogWare);
    }

    public function getEnterSum(array $shopCatalogWare)
    {
        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');
        $ware_id = ZArrayHelper::getValue($shopCatalogWare, 'ware_id');

        $ware_enters = $this->ware_enter->where('ware_id', $ware_id)
            ->whereIn('source', [
                'accept',
                'trans',
                'enter'
            ])->all();

        $ids = ZArrayHelper::map($ware_enters, 'id', 'id');

        $query = $this->ware_enter_item
            ->where('shop_catalog_ware_id', $shop_catalog_ware_id)
            ->whereIn('ware_enter_id', $ids);

        // filter uchun kerak
        $time_before = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($this->filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->whereBetween('amount', [$time_before, $time_after]);

        $res_ids = ZArrayHelper::map($query->all(), 'id', 'id');

        $res_ids = implode('|', $res_ids);

        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value)) {
            return <<<HTML
             <div>$value</div>   
HTML;
        }


        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;
    }

    public function getEnterSrReturn(ShopCatalogWare $ware, array $filter = [])
    {

        $Q = $ware->getWareOne();

        /* $Q = Ware::findOne($ware->ware_id);
         vdd($Q->id);*/


        if ($Q === null)
            return null;


        $Q = $Q->getWareEntersWithWareId()
            ->andWhere([
                'source' => 'return'
            ])->asArray()
            ->all();

        $ids = ZArrayHelper::map($Q, 'id', 'id');
        $query = $ware->getWareEnterItemsWithShopCatalogWareId()
            ->andWhere([
                'ware_enter_id' => $ids
            ]);

        $time_before = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->andWhere(['between', 'amount', $time_before, $time_after]);

        $res_ids = ZArrayHelper::map($query->asArray()->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value))
            return $value;

        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;
    }

    public function getEnterSrExchange(ShopCatalogWare $ware, array $filter = [])
    {
        $Q = $ware->getWareOne();
        /* $Q = Ware::findOne($ware->ware_id);
         vdd($Q->id);*/
        if ($Q === null)
            return null;

        $Q = $Q->getWareEntersWithWareId()
            ->andWhere([
                'source' => 'exchange'
            ])->asArray()
            ->all();

        $ids = ZArrayHelper::map($Q, 'id', 'id');
        $query = $ware->getWareEnterItemsWithShopCatalogWareId()
            ->andWhere([
                'ware_enter_id' => $ids
            ]);
        /* $query = WareEnterItem::find()
             ->where([
                 'ware_enter_id' => $ids
             ]);*/
        $time_before = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->andWhere(['between', 'amount', $time_before, $time_after]);
        $res_ids = ZArrayHelper::map($query->asArray()->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$query->sum('amount');
        if ($this->emptyOrNullable($value))
            return $value;
        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;
    }

    public function getEnterSrCancel(ShopCatalogWare $ware, array $filter = [])
    {
        $Q = $ware->getWareOne();
        /* $Q = Ware::findOne($ware->ware_id);
         vdd($Q->id);*/
        if ($Q === null)
            return null;

        $Q = $Q->getWareEntersWithWareId()
            ->andWhere([
                'source' => 'cancel'
            ])->asArray()
            ->all();

        $ids = ZArrayHelper::map($Q, 'id', 'id');
        $query = $ware->getWareEnterItemsWithShopCatalogWareId()
            ->andWhere([
                'ware_enter_id' => $ids
            ]);
        /* $query = WareEnterItem::find()
             ->where([
                 'ware_enter_id' => $ids
             ]);*/
        $time_before = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->andWhere(['between', 'amount', $time_before, $time_after]);
        $res_ids = ZArrayHelper::map($query->asArray()->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$query->sum('amount');
        if ($this->emptyOrNullable($value))
            return $value;
        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;
    }

    public function getExitSum(ShopCatalogWare $ware, array $filter = [])
    {
        $Q = $ware->getWareExitItemsWithShopCatalogWareId();
        $time_before = ZArrayHelper::getValue($filter, 'amount_before');
        $time_after = ZArrayHelper::getValue($filter, 'amount_after');

        if ($time_before !== null && $time_after !== null)
            $Q->andWhere(['between', 'created_at', $time_before, $time_after]);
        $res_ids = ZArrayHelper::map($Q->asArray()->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$Q->sum('amount');
        if ($this->emptyOrNullable($value))
            return $value;
        return <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareExitItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;
    }

    #endregion

}



