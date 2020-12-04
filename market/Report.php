<?php

/**
 * @author  Daho || Xolmat Ravshanov
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExitItem;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;


class Report extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $shop_catalog = null;
    public ?Collection $shop_catalog_ware = null;
    public ?Collection $ware_exit_items = null;
    public $chess_id = null;

    public $filter = [];
    public $start_time = null;
    public $end_time = null;

    public function data($filter = [])
    {
        $this->chess_id = $this->paramGet('chess_id');


        $this->filter = $filter;

        $this->start_time = ZArrayHelper::getValue($filter, 'amount_before');
        $this->end_time = ZArrayHelper::getValue($filter, 'amount_after') ;

        if ($this->start_time === null)
            $this->start_time = '01.01.0001';
            
        if ($this->end_time === null)
            $this->end_time = '31.12.9999';

        $this->start_time .=  ' 00:00:00';
        $this->end_time .=  ' 23:59:59';
        
        if ($this->ware === null)
            $this->ware = collect(Ware::find()->all());

        if ($this->ware_enter === null)
            $this->ware_enter = collect(
                WareEnter::find()
                    ->whereBetween('created_at', $this->start_time, $this->end_time)
                    ->asArray()
                    ->all());

        if ($this->ware_enter_item === null)
            $this->ware_enter_item = collect(
                WareEnterItem::find()
                    ->whereBetween('created_at', $this->start_time, $this->end_time)
                    ->asArray()
                    ->all());

        /*if ($this->shop_catalog === null)
            $this->shop_catalog = collect(ShopCatalog::find()->asArray()->all());*/

        if ($this->shop_catalog_ware === null)
            $this->shop_catalog_ware = collect(ShopCatalogWare::find()->asArray()->all());

        if ($this->ware_exit_items === null)
            $this->ware_exit_items = collect(WareExitItem::find()
                ->whereBetween('created_at', $this->start_time, $this->end_time)
                ->asArray()
                ->all());

                

        //line|18
        
    }

    public function getEnterSumTest()
    {
        $boot = new \Boot();

        $boot->start();

        $this->getEnterSumTest1();

        echo $boot->finish();

        //line|4

    }

    //5 
     //  ware_accecpt
     //  ShopOrder status_logistics
     //  completed part_paid
     
    #region getEntersum
    public function getEnterSumTest1()
    {

        $ware = ShopCatalogWare::find()->where([
            'id' => 141,
        ])->asArray()->one();

        $r = $this->getEnterSum($ware);

        //line|3
    }


    /**
     * @param ShopCatalogWare $shopCatalogWare
     * @return  array
     * @throws \Exception
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSum
     */
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
            $return = <<<HTML
             <div>$value</div>   
HTML;
        } else

            $return = <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;
        $res =  [
            'value' => (int)$value,
            'valueShow' => $return
        ];

        return $res;
                //line||22
    }
    #endregion
    #region getEnterSrReturn
    public function getEnterSrReturnTest()
    {
        $ware = ShopCatalogWare::find()->where([
            'id' => 141,
        ])->asArray()->one();

        $r = $this->getEnterSrReturn($ware);

        vdd($r);

        //line|3
    }

    /**
     * @param array $shopCatalogWare
     * @return  array
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSrReturn
     */
    public function getEnterSrReturn(array $shopCatalogWare)
    {
        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');

        $ware_id = ZArrayHelper::getValue($shopCatalogWare, 'ware_id');

        $ware_enters = $this->ware_enter->where('ware_id', $ware_id)
            ->where('source', 'return')->all();

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

            $return = <<<HTML

             <div>$value</div>   
HTML;

        }
         else
        $return = <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;

        $res =  [
            'value' => (int)$value,
            'valueShow' => $return
        ];

        return $res;

        //line|17
    }
    #endregion

    #region getEnterSrExchange
    public function getEnterSrExchangeTest()
    {

        $ware = ShopCatalogWare::find()->where([
            'id' => 127,
        ])->asArray()->one();

        $r = $this->getEnterSrReturn($ware);

        vdd($r);

        //line|3

    }

    /**
     * @param array $shopCatalogWare
     * @return  array
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSrExchange
     */
    public function getEnterSrExchange(array $shopCatalogWare)
    {

        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');

        $ware_id = ZArrayHelper::getValue($shopCatalogWare, 'ware_id');

        $ware_enters = $this->ware_enter->where('ware_id', $ware_id)
            ->where('source', 'exchange')->all();

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
            $return = <<<HTML
             <div>$value</div>   
HTML;

        }
        else
        $return = <<<HTML
<a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;
        $res =  [
            'value' => (int)$value,
            'valueShow' => $return
        ];

        return $res;
        //line|19
    }
    #endregion
    #region getEnterSrCancelTest
    public function getEnterSrCancelTest()
    {

        $ware = ShopCatalogWare::find()->where([
            'id' => 66,
        ])->asArray()->one();


        $r = $this->getEnterSrReturn($ware);

               //line|3
        vdd($r);

    }


    /**
     * @param array $shopCatalogWare
     * @return  array
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSrCancel
     */
    public function getEnterSrCancel(array $shopCatalogWare)
    {

        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');

        $ware_id = ZArrayHelper::getValue($shopCatalogWare, 'ware_id');

        $ware_enters = $this->ware_enter->where('ware_id', $ware_id)
            ->where('source', 'cancel')->all();

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
            $return = <<<HTML
             <div>$value</div>   
HTML;

        }
        else
        $return = <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareEnterItem&id={$this->chess_id}" target="_blank">$value</a>
HTML;

        $res =  [
            'value' => (int)$value,
            'valueShow' => $return
        ];

        return $res;

        //line|18
    }
    #endregion


    #region getExitSum
    public function getExitSumTest()
    {
        $ware = ShopCatalogWare::find()->where([
            'id' => 56,
        ])->asArray()->one();

        $r = $this->getExitSum($ware);

        //line|3
        vdd($r);
    }


    /**
     * @param array $shopCatalogWare
     * @return  array
     * @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getExitSum
     */

    public function getExitSum(array $shopCatalogWare)
    {

        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');

        if ($shop_catalog_ware_id === null)
            return null;

        $query = $this->ware_exit_items->where('shop_catalog_ware_id', $shop_catalog_ware_id);


        // filter
        $time_before = ZArrayHelper::getValue($this->filter, 'amount_before');
        $time_after = ZArrayHelper::getValue($this->filter, 'amount_after');

        if ($time_before !== null && $time_after !== null)
            $query->whereBetween('created_at', [$time_before, $time_after]);
        $res_ids = ZArrayHelper::map($query->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$query->sum('amount');
        if ($this->emptyOrNullable($value)) {
            $return = <<<HTML
             <div>$value</div>   
HTML;
        }
         else
        $return = <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$res_ids}&modelClass=WareExitItem&id={$this->chess_id}" target="_blank" >$value</a>
HTML;

        $res =  [
            'value' => (int)$value,
            'valueShow' => $return
        ];
                //line||18
        return $res;
    }
    #endregion


    #region getExitSum
    public function dailyTest()
    {

        $ware_accept = \zetsoft\models\ware\WareAccept::find()->asArray()->one();

        vdd($ware_accept);

    }
    #endregion


}



