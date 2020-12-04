<?php

/**
 * @author NurbekMakhmudov
 */

namespace zetsoft\service\market;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopCatalogWare;
use zetsoft\models\ware\Ware;
use zetsoft\models\ware\WareEnter;
use zetsoft\models\ware\WareEnterItem;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\implode;


class WareAccept extends ZFrame
{
    public ?Collection $ware = null;
    public ?Collection $ware_enter = null;
    public ?Collection $ware_enter_item = null;
    public ?Collection $shop_catalog_ware = null;

    public $filter = [];

    /**
     * todo Collection null bo'lsa modeldan malumotlarni arrayga olib, collection ga joylaydi
     */
    public function data($filter = [])
    {
        $this->filter = $filter;

        if ($this->ware_enter === null)
            $this->ware_enter = collect(WareEnter::find()->asArray()->all());

        if ($this->ware === null)
            $this->ware = collect(Ware::find()->all());

        if ($this->shop_catalog_ware === null)
            $this->shop_catalog_ware = collect(ShopCatalogWare::find()->asArray()->all());

        if ($this->ware_enter_item === null)
            $this->ware_enter_item = collect(WareEnterItem::find()->asArray()->all());


    }

    /**
     * @author NurbekMakhmudov
     * @todo methodni qancha vaqtda ish bajaryotganini chiqaradi
     */
    public function runningTime()
    {
        $boot = new \Boot();
        $boot->start();
        $this->getEnterSumTest();
        echo $boot->finish();
    }


    public function getEnterSumTest()
    {
        /**
         * @todo shop_catalog_ware_id in array
         * SELECT * FROM shop_catalog_ware WHERE "id" = 61
         */
        $shopCatalogWare = ShopCatalogWare::find()->where([
            'id' => 141,
        ])->asArray()->one();
        $this->getEnterSum($shopCatalogWare);
    }

    public function getEnterSum(array $shopCatalogWare)
    {
        $shop_catalog_ware_id = ZArrayHelper::getValue($shopCatalogWare, 'id');
        $ware_id = ZArrayHelper::getValue($shopCatalogWare, 'ware_id');

        /**
         * @todo WhereIn usuli to'plamni berilgan qator ichida joylashgan kalit / qiymat bo'yicha filtrlaydi
         * @todo sql1 va sql2 va sql3 natijalarini bitta arrayga chiqarib beradi
         * @sql1 SELECT * FROM ware_enter WHERE "ware_id" = 61 AND "source" = 'enter';
         * @sql2 SELECT * FROM ware_enter WHERE "ware_id" = 61 AND "source" = 'trans';
         * @sql3 SELECT * FROM ware_enter WHERE "ware_id" = 61 AND "source" = 'accept';
         */
        $ware_enters = $this->ware_enter->where('ware_id', $ware_id)
            ->whereIn('source', [
                'accept',
                'trans',
                'enter'
            ])->all();

        /**
         * @todo https://yiiframework.com.ua/ru/doc/guide/2/helper-array/
         */
        $ids = ZArrayHelper::map($ware_enters, 'id', 'id');

//        vd($shop_catalog_ware_id);
//        vd($ids);

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



