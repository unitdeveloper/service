<?php


namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\shop\ShopCategory;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\models\ware\WareExitItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;


class ReportXolmat extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $shop_catalog = null;
    public ?Collection $shop_catalog_ware = null;
    public ?Collection $ware_exit_items = null;

    public $filter = [];

    public function data($filter = [])
    {

        $this->filter = $filter;

        if ($this->ware === null)
            $this->ware = collect(Ware::find()->all());

        if ($this->ware_enter === null)
            $this->ware_enter = collect(WareEnter::find()->asArray()->all());

        if ($this->ware_enter_item === null)
            $this->ware_enter_item = collect(WareEnterItem::find()->asArray()->all());

        if ($this->shop_catalog === null)
            $this->shop_catalog = collect(ShopCatalog::find()->asArray()->all());

        if ($this->shop_catalog_ware === null)
            $this->shop_catalog_ware = collect(ShopCatalogWare::find()->asArray()->all());

        if($this->ware_exit_items === null)
            $this->ware_exit_items = collect(WareExitItem::find()->asArray()->all());

    }

    public function getEnterSumTest()
    {
        $boot = new \Boot();

        $boot->start();

        $this->getEnterSumTest1();

        echo $boot->finish();

    }



    #region getEntersum
    public function getEnterSumTest1()
    {

        $ware = ShopCatalogWare::find()->where([
            'id' => 141,
        ])->asArray()->one();

        $r = $this->getEnterSum($ware);

         
    }


    /**
     * @param ShopCatalogWare $shopCatalogWare
     * @return  string|null
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
            return <<<HTML
             <div>$value</div>   
HTML;
        }

        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
HTML;

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
    }

    /**
     *  @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSrReturn
     * @param array $shopCatalogWare
     * @return  string
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
            return <<<HTML
             <div>$value</div>   
HTML;

        }

        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
HTML;
    }
     #endregion
     
     #region getEnterSrExchange
    public function getEnterSrExchangeTest(){

        $ware = ShopCatalogWare::find()->where([
            'id' => 127,
        ])->asArray()->one();

        $r = $this->getEnterSrReturn($ware);

        vdd($r);
    }

    /**
     *  @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSrExchange
     * @param array $shopCatalogWare
     * @return  string
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
            return <<<HTML
             <div>$value</div>   
HTML;

        }

        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
HTML;

    }
     #endregion
     #region getEnterSrCancelTest
    public function getEnterSrCancelTest(){

        $ware = ShopCatalogWare::find()->where([
            'id' => 66,
        ])->asArray()->one();


        $r = $this->getEnterSrReturn($ware);


        vdd($r);

    }


    /**
     *  @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getEnterSrCancel
     * @param array $shopCatalogWare
     * @return  string
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
            return <<<HTML
             <div>$value</div>   
HTML;

        }

        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
HTML;
    }
     #endregion


    #region getExitSum
    public function getExitSumTest(){

        $ware = ShopCatalogWare::find()->where([
            'id' => 56,
        ])->asArray()->one();

        $r = $this-> getExitSum($ware);

        vdd($r);

    }

    /**
     *  @author  Xolmat Ravshanov && Dilshod Olimjonov
     * Function  getExitSum
     * @param array $shopCatalogWare
     * @return  string|null
     */
    public function getExitSum(array $shopCatalogWare)
    {
        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');


        if($shop_catalog_ware_id === null)
            return null;

        $query = $this->ware_exit_items->where('shop_catalog_ware_id', $shop_catalog_ware_id);

  
        // filter
        $time_before = ZArrayHelper::getValue($this->filter, 'amount_before');
        $time_after = ZArrayHelper::getValue($this->filter, 'amount_after');

        if ($time_before !== null && $time_after !== null)
            $query->whereBetween( 'created_at', [$time_before, $time_after]);

        $res_ids = ZArrayHelper::map($query->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value)){
            return <<<HTML
             <div>$value</div>   
HTML;
        }
            
        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareExitItem">$value</a>
HTML;
    }
    #endregion




    #region getExitSum
    public function dailyTest(){

        $ware_accept = \zetsoft\models\ware\WareAccept::find()->asArray()->one();

        vdd($ware_accept);

    }


    public function daily(WareAccept $wareAccept)
    {
        $ware_accept_id = ZArrayHelper::getValue($wareAccept, 'id');


        if ($this->emptyOrNullable($value)){
            return <<<HTML
             <div>$value</div>   
HTML;
        }


        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareExitItem">$value</a>
HTML;
    }
    #endregion



}



