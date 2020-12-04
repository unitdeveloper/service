<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report  | выгрузка данных приемок  &&  Заказы операторов
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

/**
 * Class ReportAcceptanceData
 *  выгрузка данных приемок  &&  Заказы операторов
 * @package zetsoft\service\market
 * @author NurbekMakhmudov
 */
class ReportAcceptanceData extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-15

    public ?Collection $ware_accept = null;
    public ?Collection $shop_order = null;
    public ?Collection $shop_order_item = null;
    public ?Collection $user = null;
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

        if ($this->user_company === null)
            $this->user_company = collect(UserCompany::find()->asArray()->all());

        if ($this->user === null)
            $this->user = collect(User::find()->asArray()->all());

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());

        if ($this->ware_accept === null)
            $this->ware_accept = collect(WareAccept::find()->asArray()->all());

        if ($this->shop_courier === null)
            $this->shop_courier = collect(ShopCourier::find()->asArray()->all());
    }


    /**
     * @author NurbekMakhmudov
     */
    public function test()
    {
        $ware_accept = WareAccept::find()
            ->where([
                'id' => 350
            ])
            ->asArray()
            ->one();

        $res = $this->getVDC($ware_accept);
        vd($res);
    }

    #region выгрузка данных приемок


    /**
     * @param array $ware_accept
     * @return array
     * @author NurbekMakhmudov
     * @todo выгрузка данных приемок | ВДС | get  dc_returns_group count
     */
    public function getVDC(array $ware_accept)
    {
        $count = \Dash\count($ware_accept['dc_returns_group']);
        $res = [
            'value' => $count,
            'valueShow' => $count
        ];
        return $res;
    }

    /**
     * @param array $ware_accept
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo выгрузка данных приемок | статус приемки |  get shop_order status_accept  |
     */
    public function getStatusAcceptance(array $ware_accept)
    {
        $shop_shipment_id = ZArrayHelper::getValue($ware_accept, 'shop_shipment_id');
        if ($shop_shipment_id === null || $shop_shipment_id === "") return null;

        $shop_orders = $this->shop_order->where('shop_shipment_id', $shop_shipment_id);
        if ($shop_orders === null || $shop_orders->isEmpty()) return null;

        $status = null;
        foreach ($shop_orders as $shop_order) {
            $status_accept = $shop_order['status_accept'];
            $status = (new ShopOrder())->_status_accept[$status_accept];
        }

        $res = [
            'value' => $status,
            'valueShow' => $status
        ];
        return $res;
    }

    /**
     * @param array $ware_accept
     * @return array|null
     * @throws \Exception
     * @author NurbekMakhmudov
     *  выгрузка данных приемок | Проект | get usr_company name
     */
    public function getUserCompanyName(array $ware_accept)
    {
        $shop_courier_id = ZArrayHelper::getValue($ware_accept, 'shop_courier_id');
        if ($shop_courier_id === null || $shop_courier_id === "") return null;

        $user_company_ids = $this->shop_courier
            ->where('id', $shop_courier_id)
            ->pluck('user_company_id');
        if ($user_company_ids === null || $user_company_ids->isEmpty()) return null;

        foreach ($user_company_ids as $user_company_id) {

            $names = $this->user_company
                ->where('id', $user_company_id)
                ->pluck('name');

            foreach ($names as $name) {
                $res = [
                    'value' => $name,
                    'valueShow' => $name
                ];
                return $res;
            }
        }
    }


    /**
     * @param array $ware_accept
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo выгрузка данных приемок | Курьер | get shop_courier name
     */
    public function getShopCourierName(array $ware_accept)
    {
        $shop_courier_id = ZArrayHelper::getValue($ware_accept, 'shop_courier_id');
        if ($shop_courier_id === null || $shop_courier_id === "") return null;

        $shop_couriers = $this->shop_courier->where('id', $shop_courier_id);
        if ($shop_couriers === null) return null;

        $name = null;
        foreach ($shop_couriers as $shop_courier)
            $name = ZArrayHelper::getValue($shop_courier, 'name');

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }


    #endregion


    #region выгрузка данных приемок Заказы операторов

    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo выгрузка данных приемок Заказы операторов | Приемка | get WareAccept name
     */
    public function getAcceptanceName(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null || $shop_shipment_id === "") return null;

        $ware_accepts = $this->ware_accept
            ->where('shop_shipment_id', $shop_shipment_id);
        if ($ware_accepts === null || $ware_accepts->isEmpty()) return null;

        $name = null;
        $id = null;
        foreach ($ware_accepts as $ware_accept) {
            $name = ZArrayHelper::getValue($ware_accept, 'name');
            $id = ZArrayHelper::getValue($ware_accept, 'id');
        }

        if (!$this->emptyOrNullable($name)) {
            $returnName = <<<HTML
         <a target="_blank" href="/core/dynagrid/processWareAccept.aspx?ware_accept_id={$id}" >$name</a>
HTML;
        }

        $res = [
            'value' => $name,
            'valueShow' => $returnName
        ];
        return $res;
    }


    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo выгрузка данных приемок Заказы операторов | Дата | get WareAccept created_at
     */
    public function getCreateAt(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null || $shop_shipment_id === "") return null;

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
     * @todo выгрузка данных приемок Заказы операторов | Номер | get WareAccept id
     */
    public function getWareAcceptId(array $shop_order)
    {
        $shop_shipment_id = ZArrayHelper::getValue($shop_order, 'shop_shipment_id');
        if ($shop_shipment_id === null || $shop_shipment_id === "") return null;

        $ware_accepts = $this->ware_accept
            ->where('shop_shipment_id', $shop_shipment_id);
        if ($ware_accepts === null || $ware_accepts->isEmpty()) return null;

        $id = null;
        foreach ($ware_accepts as $ware_accept)
            $id = ZArrayHelper::getValue($ware_accept, 'id');

        $res = [
            'value' => $id,
            'valueShow' => $id
        ];

        return $res;
    }

    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo выгрузка данных приемок Заказы операторов | Оператор KC | get User name
     */
    public function getOperator(array $shop_order)
    {
        $operator = ZArrayHelper::getValue($shop_order, 'operator');
        if ($operator === null || $operator === "") return null;

        $users = $this->user->where('id', $operator)->toArray();
        if ($users === null || $users === "") return null;

        $name = null;
        foreach ($users as $user => $key) {
            $name = ZArrayHelper::getValue($key, 'name');
        }

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }
    #endregion

    //end|NurbekMakhmudov|2020-10-15

    // in Progress channel { 108 lines = 216 000 UZS }
}

