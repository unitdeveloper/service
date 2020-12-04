<?php

/**
 * Author:  Daho
 * Date:    14.05.2020
 *
 */

namespace zetsoft\service\market;


use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\UserCompany;

use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class CompanyStat extends ZFrame
{
    #region Vars
    public const status_types = [
        'callcenter' => 'callcenter',
        'logistics' => 'logistics',
        'client' => 'client',
    ];
    #endregion

    public function init()
    {
        parent::init();
    }

    public function getOrdersCountAndTotalPrice(string $period = 'year', int $company_id = null)
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->user_company_id;
        $q = ShopOrder::find()->where(['user_company_id' => 52]);
        switch ($period):
            case 'today':
                $q = $q->andWhere('created_at > now()::date')->asArray()->all();
                break;
            case 'yesterday':
                $q = $q->andWhere('created_at::date = current_date - 1')->asArray()->all();
                break;
            case 'month':
                $q = $q->andWhere("created_at > now() - interval '1 month'")->asArray()->all();
                break;
            case 'year':
                $q = $q->andWhere("created_at > now() - interval '1 year'")->asArray()->all();
                break;
        endswitch;
        $orders = collect($q);
//        $orders = collect(ShopOrder::find()->where(['user_company_id' => 52])->andWhere("created_at > now() - interval '1 month'")->asArray()->all());

        vdd($orders->count());
    }


    /**
     *
     * Function  allProducts
     * @param null $company_id ShopProduct::measure['pcs']
     * @param string $type self::status_types['']
     * @return  mixed
     * @throws \Exception
     */
    final public function allProducts($company_id = null, string $type = self::status_types['client'])
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->user_company_id;
        $shopCatalogs = collect(ShopCatalog::find()->where(['user_company_id' => $company_id])->asArray()->all());
        $shop_elemet_ids = ZArrayHelper::getColumn($shopCatalogs, 'shop_element_id');

        $shopElements = collect(ShopElement::find()->where(['id' => $shop_elemet_ids])->asArray()->all());
        $shop_product_ids = ZArrayHelper::getColumn($shopElements, 'shop_product_id');
        $shopProducts = collect(ShopProduct::find()->where(['id' => $shop_product_ids])->andWhere(['measure' => $type])->asArray()->all());
        $shop_product_ids = ZArrayHelper::getColumn($shopProducts, 'id');
        $filtered_shopElements = $shopElements->filter(static function ($value, $key) use ($shop_product_ids) {
            if (ZArrayHelper::isIn($value['shop_product_id'], $shop_product_ids))
                return true;
            return false;
        });
        $shop_elemet_ids = ZArrayHelper::getColumn($filtered_shopElements, 'id');
        $filtered_shopCatalogs = $shopCatalogs->filter(static function ($value, $key) use ($shop_elemet_ids) {
            if (ZArrayHelper::isIn($value['shop_element_id'], $shop_elemet_ids))
                return true;
            return false;
        });
        $count = $filtered_shopCatalogs->sum('amount');
        return $count;
    }

    /**
     *
     * Function  orderByStatusAndCompany
     * @param null $status ShopOrder::status_client[]
     * @param null $company_id
     * @return  int
     * @throws \Exception
     */
    public function orderByStatusAndCompany($status = null, $company_id = null, $type = 'client')
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->user_company_id;

        if ($status === null)
            $orders = collect(ShopOrder::find()->where(['user_company_id' => $company_id])->asArray()->all());
        else
            $orders = collect(ShopOrder::find()->where(['user_company_id' => $company_id])->andWhere(['status_client' => $status])->asArray()->all());

        return $orders->count();
    }

    public function orderCountAll($company_id = null)
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->user_company_id;
        $orders = collect(ShopOrder::find()->where(['user_company_id' => $company_id])->asArray()->all());
        return $orders->count();
    }

    public function countByStatusJoin($status = null, $company_id = 2)
    {
        if ($status) {
            $this->watchStart('gg');
            $model = ShopOrderItem::find()->select('shop_order_item.amount')->joinsInner(ShopCatalog::class)->joinsInner(ShopOrder::class)->where('shop_catalog.user_company_id = 2')->andWhere("shop_order.status = $status")->all();
            $query = collect($model);
            $count = $query->sum("amount");
        } else {
            $this->watchStart('gg');
            $model = ShopOrderItem::find()->select('shop_order_item.amount')->joinsInner(ShopCatalog::class)->joinsInner(ShopOrder::class)->where('shop_catalog.user_company_id = 2')->all();
            $query = collect($model);
            $count = $query->sum("amount");
        }
        vd(($this->watchStop('gg'))->getDuration());
        return $count;
    }
    #region Statistics
//    public function ordernumbers($status = null, $company_id = 57)
//    {
//
//        if ($company_id === null)
//            $company_id = $this->userIdentity()->company_id;
//
//
//        $catalogs = $this->catalogs->where(
//            'user_company_id', $company_id
//        );
//
//        $catalog_ids = $catalogs->pluck('id');
//        if ($status == null) {
//            $order_items = $this->order_items->whereIn('shop_catalog_id', $catalog_ids);
//            return $order_items->sum("amount");
//        } else {
//            $query = ShopOrderItem::find()
//                ->select('*')  // make sure same column name not there in both table
//                ->leftJoin('shop_order', 'shop_order.id = core_order_item.shop_order_id ')
//                ->where(['shop_order.status' => $status])
//                ->andWhere(['core_order_item.shop_catalog_id' => $catalog_ids])
//                ->all();
//            $query = collect($query);
//            return $query->sum("amount");
//        }
//    }


    public function myElementCount($company_id = null)
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->company_id;

        //$catalogs = $this->catalogs->where('user_company_id', $company_id);


        //$query = collect($query);

        //return $catalogs->sum('amount');
    }

    public function myOrdersCount($company_id = null, $measure)
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->company_id;

        $catalogs = $this->catalogs->where('user_company_id', $company_id);
        $model = ShopCatalog::findBySql("SELECT cc.shop_element_id, cc.amount FROM shop_catalog cc 
            INNER JOIN shop_element ce ON cc.shop_element_id=ce.id 
            INNER JOIN shop_product cp ON ce.shop_product_id=cp.id 
            WHERE cc.user_company_id = $company_id and cp.measure = '$measure'
            ")->all();

        return $catalogs->sum('amount');
    }

    public function myTodayOrdersCount($company_id = null, $measure)
    {
        if ($company_id === null)
            $company_id = $this->userIdentity()->company_id;
        $company_id = 57;
        $status = ShopOrder::status['delivered'];
        $model = ShopOrderItem::findBySql("SELECT coi.amount, coi.price FROM core_order_item coi 
            INNER JOIN shop_catalog cc ON cc.id=coi.shop_catalog_id
            INNER JOIN shop_order co ON coi.shop_order_id=co.id 
            WHERE co.status = '$status' and cc.user_company_id = $company_id and co.modified_at > now()::date;")->all();
        $model = collect($model);
        return [
            'price' => $model->sum('price'),
            'count' => $model->count()
        ];
    }
    //public function myToday
    #endregion
}



