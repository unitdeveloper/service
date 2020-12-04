<?php

/**
 * Author: Sardor
 */

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
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;


class ReportOld1 extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_enter = null;
    public ?Collection $shop_catalog = null;

    public $filter = [];

    public function data($filter = [])
    {

        $this->filter = $filter;

        if ($this->ware === null)
            $this->ware = collect(Ware::find()->all());

        if ($this->ware_enter === null)
            $this->ware_enter = collect(WareEnter::find()->asArray()->all());

        if ($this->shop_catalog === null)
            $this->shop_catalog = collect(ShopCatalog::find()->asArray()->all());


    }

    public function getEnterSumTest()
    {


        $boot = new \Boot();

        $boot->start();

        $Q = ShopCatalogWare::find()
            ->where([
                'id' => 72
            ]);

        $ware = $Q->one();


        $sum = $this->getEnterSum($ware, []);

        echo $boot->finish();

        // vdd($sum);
    }


    public function getEnterSum(ShopCatalogWare $shopCatalogWare)
    {

        //array $shopCatalogWare
        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');

        if ($shop_catalog_ware_id === null)
            return null;
            
        $ware = $this->ware->where('id', $shop_catalog_ware_id);


        $Q = $shopCatalogWare->getWareOne();

        /* $Q = Ware::findOne($ware->ware_id);
         vdd($Q->id);
        */

        //$ware = Ware::findOne($shop_catalog_ware_id);


        if ($ware->isEmpty())
            return null;


        if ($Q === null)
            return null;


        $Q = $Q->getWareEntersWithWareId()
            ->andWhere([
                'source' => [
                    'accept',
                    'trans',
                    'enter'
                ]
            ])
            ->asArray()
            ->all();

        $ids = ZArrayHelper::map($Q, 'id', 'id');

        $query = $shopCatalogWare->getWareEnterItemsWithShopCatalogWareId()
            ->andWhere([
                'ware_enter_id' => $ids
            ]);

        /*$query = WareEnterItem::find()
         ->where([
             'ware_enter_id' => $ids
         ]);*/

        $time_before = ZArrayHelper::getValue($t, 'ZDynamicModel[amount_before]');
        $time_after = ZArrayHelper::getValue($filter, 'ZDynamicModel[amount_after]');

        if ($time_before !== null && $time_after !== null)
            $query->andWhere(['between', 'amount', $time_before, $time_after]);


        $res_ids = ZArrayHelper::map($query->asArray()->all(), 'id', 'id');
        $res_ids = implode('|', $res_ids);
        $value = (string)$query->sum('amount');

        if ($this->emptyOrNullable($value))
            return $value;


        return <<<HTML
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
HTML;

    }

    public function getEnterSrReturnTest()
    {
        $ware = ShopCatalogWare::findOne(72);

        $sum = $this->getEnterSrReturn($ware, []);
        vdd($sum);
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
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
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
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
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
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareEnterItem">$value</a>
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
         <a href="/shop/user/report/enter-sum.aspx?ids={$res_ids}&modelClass=WareExitItem">$value</a>
HTML;
    }
}



