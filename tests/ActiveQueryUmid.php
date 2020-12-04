<?php

/**
 * @author  UmidMuminov
 * @todo Join query examples
 *
 */

namespace zetsoft\service\tests;


use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
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

class ActiveQueryUmid extends ZFrame
{

    public function test()
    {

        $this->withQuery();

//                $this->orOnCondition();
//                $this->populate();
//                $this->rightJoin();
//                $this->withQuery();
    }

    #region Successful functions

    /**
     *
     * Function  withQuery
     * @author UmidMo'minov
     * @link https://www.yiiframework.com/doc/api/2.0/yii-db-query#withQuery()-detail
     * @todo Prepends a SQL statement using WITH syntax.
     */
    public function withQuery()
    {

        $initialQuery = (new Query())
            ->select(['id', 'name'])
            ->from(['place_country'])
            ->where(['name' => 'Гонконг']);

        $recursiveQuery = (new Query())
            ->select(['palace_country_id'])
            ->from(['place_region'])
            ->innerJoin('place_region', 'palace_country_id = id');

        $mainQuery = (new Query())
            ->select(['palace_country_id', 'id'])
            ->from('place_region')
            ->withQuery($initialQuery->union($recursiveQuery), 'place_region', true);
        vdd($mainQuery);
    }



    ##endregion
}
