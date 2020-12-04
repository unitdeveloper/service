<?php

/**
 * @author NurbekMakhmudov
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\kernels\ZFrame;


/**
 * Class ReportCourierGood
 * @package zetsoft\service\market
 * @author NurbekMakhmudov
 * Отчет по курьерам
 */
class ReportCourierGood extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-24

    public ?Collection $ware_accepts = null;

    public $chess_id = null;

    public $filter = [];

    /**
     * @param array $filter
     * @throws \Exception
     */
    public function data($filter = [])
    {
        $this->chess_id = $this->paramGet('chess_id');

        $this->filter = $filter;

        if ($this->ware_accepts === null)
            $this->ware_accepts = collect(WareAccept::find()->asArray()->all());

    }

    /**
     * @throws \Exception
     * Testing
     */
    public function test()
    {
        $shop_courier = ShopCourier::find()
            ->asArray()
            ->all();
        $res = $this->getWareAcceptTotal($shop_courier);
        vdd($res);
    }

    public function getWareAcceptTotal(array $shop_couriers)
    {
        $totals = $this->ware_accepts
            ->where('shop_courier_id', $shop_couriers['id'])
            ->pluck('total')
            ->toArray();
        if ($totals === null || empty($totals)) return null;

        $res = null;
        foreach ($totals as $total)
            $res = [
                'value' => $total,
                'valueShow' => $total
            ];

        return $res;

    }


    //end|NurbekMakhmudov|2020-10-24
}




