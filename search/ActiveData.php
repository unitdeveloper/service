<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\search;


use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\system\actives\ZActiveData;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use yii\data\ActiveDataProvider;
class ActiveData extends ZFrame
{

    #region Vars

    /* @var ZActiveQuery $query */
    public $query;

    /* @var ZActiveData $provider */
    public $provider;

    /* @var ZActiveData $sort */
    public $sort=[];
    

    public $limit;
    public $pagination=[];
    public $count;
    public $totalCount;


    #endregion

    #region Test

    public function test()
    {
        //    $this->testRun();
        $this->testRunAll();
    }


    public function testRun()
    {

        Az::$app->search->activeData->query = ShopProduct::find();
        
        $all = Az::$app->search->activeData->run();
        vdd($all);
    }

    public function testSort()
    {
        Az::$app->search->activeData->query = User::find();
        Az::$app->search->activeData->sort=[
            'defaultOrder' => [
                'id' => SORT_DESC,
                'name'  => SORT_ASC,
            ]
        ];
        Az::$app->search->activeData->pagination=[];
        $all = Az::$app->search->activeData->run2();
        vdd($all);
    }
    public function testSort2()
    {
        Az::$app->search->activeData->query = User::find();
        Az::$app->search->activeData->sort=[
            'attributes' => [
                'sex',
                'name' => [
                    'asc' => ['email' => SORT_ASC, 'role' => SORT_ASC],
                    'desc' => ['email' => SORT_DESC, 'role' => SORT_DESC],
                    'default' => SORT_DESC,
                    'label' => 'Name',
                ],
            ],
            'defaultOrder' => [
                'name' => SORT_DESC,
            ],
            'enableMultiSort' => true

        ];
        Az::$app->search->activeData->pagination=[];
        $all = Az::$app->search->activeData->run2();
        vdd($all);
    }

    public function testPagination()
    {
        Az::$app->search->activeData->query = User::find();
        Az::$app->search->activeData->pagination = [
            // The default page size.
            'defaultPageSize' => 1,
            // Whether to always have the page parameter in the URL created by createUrl().
            'forcePageParam' => false,
            // The zero-based current page number.
            'page' => 1,
            // Name of the parameter storing the current page index.
            'pageParam' => 'page',
            // The number of items per page.
            'pageSize' => 10,
        ];
        //Az::$app->search->activeData->sort = [];
        $all = Az::$app->search->activeData->run2();
        vdd($all);
    }
    public function testPagination2()
    {
       
    }
    public function testRunAll()
    {

        Az::$app->search->activeData->query = ShopProduct::find();
        Az::$app->search->activeData->pagination = 2;
        $all = Az::$app->search->activeData->run();
        vdd($all);
    }



    #endregion

    #region Main

    public function run()
    {
        $this->provider = new ZActiveData([
            'query' => $this->query,
            /*'totalCount'=>$this->totalCount,
            'count'=> $this->count,*/
            'sort' => $this->sort,
            'pagination' => $this->pagination
        ]);

        // get the posts in the current page
        return $this->provider->getModels();
    }
    public function run2()
    {
        $this->provider = new ActiveDataProvider([
            'query' => $this->query,
            'totalCount'=>$this->totalCount,
            'sort' => $this->sort,
            'pagination' => $this->pagination
        ]);

        // get the posts in the current page
        return $this->provider->getModels();
    }
    
    #endregion

}
