<?php

/**
 * Author: Jaxongir
 */

namespace zetsoft\service\market;


use zetsoft\dbitem\shop\AdminItem;
use zetsoft\dbitem\shop\SellerInfoItem;
use zetsoft\former\chart\ChartFormAdmin;
use zetsoft\former\chart\ChartFormSeller;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\system\Az;
use zetsoft\models\user\UserCompany;
use zetsoft\models\core\CoreMenuNew;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\Cupon;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use function Dash\reduce;


class SellerStatistic extends ZFrame
{

    public $user;
    public $product;
    public $order;

    #region init

    public function init()
    {
        parent::init();
        $this->order = ShopOrder::find();

    }

    #endregion
    public function sellerInfoTest()
    {
        $data = $this->sellerInfo(1);
        vd($data);
    }

    public function sellerInfo($market_id)
    {
        $result = new SellerInfoItem();
        $orders = $this->order->where(['in', 'user_company_ids', $market_id])->asArray()->all();
        $count = 0;
        $order_items = ShopOrderItem::find()->select('shop_order_id')->asArray()->all();
        $users = [];
        foreach ($orders as $order) {
            $key = $order['status_client'];
            if (!empty($key)) {
                $users[] = $order['user_id'];
                foreach ($order_items as $order_item)
                    if ($order_item['shop_order_id'] === $order['id']) $count++;
            }
        }

        $result->order = $count;
        $result->user = \Dash\count(array_unique($users));
        $result->product = \Dash\count(Az::$app->market->product->allProducts(null, $market_id, null, null));

        return (array)$result;

    }

    public function sellerInfoChartTest()
    {
        $data = $this->sellerInfoChart(1);
        vd($data);
    }

    public function sellerInfoChart($market_id)
    {

        $result = (object)$this->sellerInfo($market_id);
        $form = new ChartFormSeller();
        $form->name = '';
        $form->user = $result->user;
        $form->product = $result->product;
        $form->order = $result->order;


        return [$form];


    }

    public function getclientsTest()
    {
        $data = $this->getclients(1);
        vd($data);
    }

    public function getclients($market_id)
    {
        $result = [];
        $data = $this->order->select('user_id')->where(['in', 'user_company_ids', $market_id])->asArray()->all();
        foreach ($data as $array)
            $result[] = $array['user_id'];

        return $result;

    }

    public function getStatsUrlsTest()
    {
        $data = $this->getStatsUrls();
        vd($data);
    }

    public function getStatsUrls()
    {
        return [
            'market' => '/shop/admin/user-company/index.aspx',
            'user' => '/seller/main/clients.aspx',
            'seller' => '/shop/admin/user/index.aspx',
            'courier' => '/shop/admin/user/index.aspx',
            'product' => '/seller/shop-product/index.aspx',
            'order' => '/seller/shop-order/index.aspx',
        ];
    }

    /**
     *
     * Function  sellerSumStats
     * @param $company_id
     * @return  array
     * @throws \Exception
     * @author Xolmat Ravshanov
     */
    public function sellerSumStatsByMonth($company_id)
    {
        /**
         * return '{"data":{"datasets":[{"data":[0, 20, 10, 30, 15, 40, 20, 60, 60]}]}}'
         */

        $return = [];
        $sum = 0;
        for ($i = 1; $i <= 12; $i++) {

            $monthStart = Az::$app->cores->date->dateTime_Month_Start("-$i months");
            $monthEnd = Az::$app->cores->date->dateTime_Month_End("-$i months");

            $monthIndex = explode('-', $monthStart);

            $monthStartWith = ZStringHelper::startsWith($monthIndex[1], '0');
            if (!$monthStartWith)
                $monthIndex = $monthIndex[1];
            else
                $monthIndex = substr($monthIndex[1], 1);


            $shop_order_items = ShopOrderItem::find()
                ->where([
                    'user_company_id' => $company_id
                ])->andWhere([
                    'between', 'created_at', $monthStart, $monthEnd
                ])->all();


            $monthName = Az::$app->cores->date->monthName($monthIndex);
            foreach ($shop_order_items as $shop_order_item)
                $sum += $shop_order_item->price_all;


            $return[$monthName] = $sum;
        }

        return $return;
    }

    /**
     *
     * Function  sellerSumStatsByWeek
     * @param $company_id
     * @return  array
     * @throws \Exception
     * @author Xolmat Ravshanov
     */

    public function sellerSumStatsByWeek($company_id)
    {
        /**
         * return '{"data":{"datasets":[{"data":[0, 20, 10, 30, 15, 40, 20, 60, 60]}]}}'
         */


        $return = [];
        $sum = 0;
        for ($i = 1; $i <= 12; $i++) {

            $monthStart = Az::$app->cores->date->dateTime_Month_Start("-$i months");
            $monthEnd = Az::$app->cores->date->dateTime_Month_End("-$i months");

            $monthIndex = explode('-', $monthStart);

            $monthStartWith = ZStringHelper::startsWith($monthIndex[1], '0');
            if (!$monthStartWith)
                $monthIndex = $monthIndex[1];
            else
                $monthIndex = substr($monthIndex[1], 1);


            $shop_order_items = ShopOrderItem::find()
                ->where([
                    'user_company_id' => $company_id
                ])->andWhere([
                    'between', 'created_at', $monthStart, $monthEnd
                ])->all();


            $monthName = Az::$app->cores->date->monthName($monthIndex);
            foreach ($shop_order_items as $shop_order_item)
                $sum += $shop_order_item->price_all;


            $return[$monthName] = $sum;
        }

        return $return;
    }


    /**
     *
     * Function  sellerOrderStats
     * @param $company_id
     * @author Xolmat Ravshanov
     */
    public function sellerOrderStats($company_id)
    {

        $return = [];

        for ($i = 1; $i <= 12; $i++){

            $monthStart = Az::$app->cores->date->dateTime_Month_Start("-$i months");

            $monthEnd = Az::$app->cores->date->dateTime_Month_End("-$i months");

            $monthIndex = explode('-', $monthStart);
            
            $monthStartWith = ZStringHelper::startsWith($monthIndex[1], '0');
            
            if (!$monthStartWith)
                $monthIndex = $monthIndex[1];
            else
                $monthIndex = substr($monthIndex[1], 1);

                                                  
            $shop_order_items_count = ShopOrderItem::find()
                ->where([
                    'user_company_id' => $company_id
                ])->andWhere([
                    'between', 'created_at', $monthStart, $monthEnd
                ])->count();
                
            $monthName = Az::$app->cores->date->monthName($monthIndex);

            $sum = $shop_order_items_count;

            $return[$monthName] = $sum;
        }

        return $return;
    }


        
}
