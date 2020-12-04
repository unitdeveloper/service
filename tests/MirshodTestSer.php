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
use function False\true;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use zetsoft\models\test\TestMirshod2;

class MirshodTestSer extends ZFrame
{

    public function saveMirshod($text, $number)
    {

        $saveinfo = new TestMirshod2();

        $saveinfo->number = $number;
        $saveinfo->username = $text;



        if ($saveinfo->save()){
                         return true;
        }
        return false;
    }


}
