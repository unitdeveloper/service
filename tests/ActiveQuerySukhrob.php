<?php

/**
 * @author  SukhrobNuraliev
 *
 */

namespace zetsoft\service\tests;


use yii\data\ActiveDataProvider;
use yii\db\Query;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use function Dash\Curry\all;

class ActiveQuerySukhrob extends ZFrame
{

    public function test()
    {
        $this->indexBy();

//                $this->offset();
//                $this->with();
//                $this->union();
//        $this->scalar();
//        $this->params();
//        $this->prepare();


//                $this->on();
//                $this->orFilterHaving();
//                $this->orFilterWhere();
//                $this->orHaving();
//
//                $this->orOnCondition();
//                $this->populate();
//                $this->rightJoin();
//                $this->withQuery();


        // NO  (in the model)
//        $this->onCondition();
//        $this->via();
//        $this->viaTable();
//        $this->inverseOf();
    }

//            https://www.yiiframework.com/doc/api/2.0/yii-db-activerelationtrait#via()-detail
//            class Order extends ActiveRecord
//            {
//                public function getOrderItems() {
//                    return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
//                }
//
//                public function getItems() {
//                    return $this->hasMany(Item::className(), ['id' => 'item_id'])
//                        ->via('orderItems');
//                }
//            }


    #region Successful functions


    public function indexBy()
    {
        $query = PlaceCountry::find()
            ->indexBy('id')
            ->all();

        vdd($query);
    }

    /*
     * Sets the OFFSET part of the query.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-querytrait#offset()-detail
     */

    public function offset()
    {
        $query = (new Query())
            ->select('name')
            ->from('place_country')
            ->limit(3)
            ->offset(20)
            ->all();

        vdd($query);
    }

    public function with()
    {
        $query = PlaceAdress::find()->with('place_country')->all();

        vdd($query);
    }

    /*
     * Attaches an event handler to an event.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-base-component#on()-detail
     */
    public function on()
    {

    }

    /*
   * Sets the ON condition for a relational query.
   * @author SukhrobNuraliev
   * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#onCondition()-detail
   */

    public function onCondition()
    {

    }

    /*
     * Sets the parameters to be bound to the query.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#params()-detail
     */

    public function params()
    {
        $query = (new Query())
            ->params([':name' => 'Гонконг', ':alpha2' => 'HK']);

        vdd($query);
    }

    /*
     * Appends a SQL statement using UNION operator.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#union()-detail
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     */

    public function union()
    {
        $query1 = (new \yii\db\Query())
            ->select("name")
            ->from('place_country')
            ->limit(10);

        $query2 = (new \yii\db\Query())
            ->select('title')
            ->from('place_region')
            ->limit(10);

        vdd($query1->union($query2));
    }


    /*
     * Returns the query result as a scalar value.
     * The value returned will be the first column in the first row of the query results.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#scalar()-detail
     */

    public function scalar()
    {
        $query = (new Query())
            ->select('name')
            ->from('place_country')
            ->scalar();

        vdd($query);
    }


    public function rightJoin()
    {
        $query = PlaceCountry::find()
            ->select('name')  // make sure same column name not there in both table
            ->leftJoin('place_adress', 'persons.idadm = admins.idadm')
            ->where(['admins.idadm' => 33])
            ->with('persons')
            ->all();

        vdd($query);
    }

    /*
     * Prepares for building SQL.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#prepare()-detail
     */

    public function prepare()
    {
        $query = (new Query())
            ->select('name')
            ->from('place_country');

//        prepare($query);
    }

    /*
     * Prepends a SQL statement using WITH syntax.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#withQuery()-detail
     */

    public function withQuery()
    {
        $query = (new Query())
            ->select('name')
            ->from('place_country');

    }

    public function orFilterWhere()
    {
        $rows = (new \yii\db\Query())
            ->from('shop_order')
            ->all();
        vdd($rows);
    }
}
