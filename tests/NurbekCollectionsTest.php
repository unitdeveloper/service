<?php

/**
 * @author NurbekMakhmudov
 */

namespace zetsoft\service\tests;

use Illuminate\Support\Collection;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\kernels\ZFrame;

class NurbekCollectionsTest extends ZFrame
{
    public ?Collection $shop_order = null;
    public ?Collection $user = null;

    public $filter = [];

    public function data($filter = [])
    {

        $this->filter = $filter;

        if ($this->shop_order === null)
            $this->shop_order = collect(ShopOrder::find()->asArray()->all());

        if ($this->user === null)
            $this->user = collect(User::find()->asArray()->all());
    }

    public function testAll()
    {
        $shopOrder = ShopOrder::find()
            ->where(['id' => 1960])
            ->asArray()
            ->all();
        $res = $this->all($shopOrder);
        vd($res);
    }

    public function all(array $model)
    {
        $res = $this->shop_order->all();
        vd($res);
    }

    public function averageTest()
    {
        $shop_order = ShopOrder::find()
            ->asArray()
            ->all();
        $res = $this->average($shop_order);
        vd($res);
    }

    public function average(array $model)
    {
        $average = collect([['foo' => 10], ['foo' => 10], ['foo' => 20], ['foo' => 40]])->avg('foo');
        echo $average . "\n\n";   /* 20 */

        $ave = collect([1, 1, 2, 4])->avg();
        echo $ave . "\n\n";    /* 2  */

        return $this->shop_order->avg('price');
    }

    public function chunkTest()
    {
        $this->chunk();
    }

    public function chunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7]);
        $chunks = $collection->chunk(4);
        $chunks->toArray();
        echo $chunks . "\n\n";      /*  [[1,2,3,4],{"4":5,"5":6,"6":7}] */
    }



}
