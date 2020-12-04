<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\tests;


use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

class Dilshod extends ZFrame
{
      public function assertTest(){
          ZTest::assertEquals(1, 1);
      }

    /**
     * You can use multiple joins at once
     * You can use joinLeft() and joinRight()
     * Function  queryJoins
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     */

    public function queryJoins()
    {
        return ShopOrderItem::find()->select('shop_order.total_price')->joinsInner(ShopOrder::class)->all();
    }

    /**
     *
     * Function  queryJoin
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     * * Old version using join() query: ShopOrderItem::find()->join('INNER JOIN', 'shop_order', 'shop_order.id = core_order_item.shop_order_id')->all();
     *
     * Below given new version of join() query
     */
    public function queryJoin(){

        return ShopOrderItem::find()->join('INNER JOIN', ShopOrder::class)->all();
    }

    /**
     *
     * Function  queryJoinWith
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     *  Old version using joinWith() query: ShopOrderItem::find()->joinWith('ShopCatalog')->one();
     * You had to give proper function name as argument so we have changed it to className
     *
     * Below given new version of joinWith() query
     */
    public function queryJoinWith(){

        return ShopOrderItem::find()->joinWith(ShopOrder::class)->all();
    }
}
