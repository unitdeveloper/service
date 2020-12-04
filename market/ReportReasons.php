<?php

/**
 * @author NurbekMakhmudov
 * $todo For Universal Report | Причины отказов
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopRejectCause;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

/**
 * Class ReportReasons
 * Причины отказов
 * @package zetsoft\service\market
 * @author NurbekMakhmudov
 */
class ReportReasons extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-15
    
    public ?Collection $shop_reject_cause = null;
    public ?Collection $ware_accept = null;
    public ?Collection $shop_order = null;
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

        if ($this->shop_reject_cause === null)
            $this->shop_reject_cause = collect(ShopRejectCause::find()->asArray()->all());

        if ($this->ware_accept === null)
            $this->ware_accept = collect(WareAccept::find()->asArray()->all());

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());


        if ($this->user_company === null)
            $this->user_company = collect(UserCompany::find()->asArray()->all());
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

    //start|NurbekMakhmudov|2020-10-25

    /**
     * @param array $shop_order
     * @return null[]|null
     * @throws \Exception
     *  @author NurbekMakhmudov
     *  Причины отказов | Проект
     */
    public function getUserCompanyName(array $shop_order){

        $source = ZArrayHelper::getValue($shop_order, 'source');
        if ($source === null || $source === "") return null;

        $user_companys = $this->user_company
            ->where('id', $source);
        if ($user_companys === null || $user_companys->isEmpty()) return null;

        $name = null;
        foreach ($user_companys as $user_company)
            $name = ZArrayHelper::getValue($user_company, 'name');

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }


    //end|NurbekMakhmudov|2020-10-25


    /**
     * @param array $shop_order
     * @return array
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Причины отказов | Приёмка | get WareAccept name |
     */
    public function getAcceptanceName(array $shop_order)
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
     * @return mixed|null
     * @throws \Exception
     * @author NurbekMakhmudov
     * @todo Причины отказов | Причина отказа | get ShopRejectCause name |
     */
    public function getRejectionReason(array $shop_order)
    {
        $shop_reject_cause_id = ZArrayHelper::getValue($shop_order, 'shop_reject_cause_id');
        if ($shop_reject_cause_id === null || $shop_reject_cause_id === "") return null;

        $shop_reject_causes = $this->shop_reject_cause
            ->where('id', $shop_reject_cause_id);
        if ($shop_reject_causes === null || $shop_reject_causes->isEmpty()) return null;


        $name = null;
        foreach ($shop_reject_causes as $shop_reject_cause)
            $name = ZArrayHelper::getValue($shop_reject_cause, 'name');

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }


    /**
     * @param array $shop_order
     * @return null[]|null
     * @throws \Exception
     * @author NurbekMakhmudov
     */
    public function getWareAcceptTime(array $shop_order)
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

        $name = null;
        foreach ($shop_reject_causes as $shop_reject_cause)
            $name = ZArrayHelper::getValue($shop_reject_cause, 'name');

        $res = [
            'value' => $name,
            'valueShow' => $name
        ];
        return $res;
    }


    #endregion

    //end|NurbekMakhmudov|2020-10-15

    // in Progress channel { 77 lines = 154 000 UZS }

}



