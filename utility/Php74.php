<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\service\cores\Cache;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class Php74 extends ZFrame
{
    public function test()
    {
        $this->arrowFunction();
    }

    public function arrowFunction()
    {
        $this->require( '/render\navigat\ZNestable2Widget\sample.php');
//        $y = 1;
//        $fn1 = fn($x) => $x + $y;
//        // equivalent to using $y by value:
//        $fn2 = function ($x) use ($y) {
//            return $x + $y;
//        };
//
//        vd($fn1(3));
    }

    public function nestedArrowFunction()
    {
        $z = 1;
        $fn = fn($x) => fn($y) => $x * $y + $z;
// Outputs 51
        vd($fn(5)(10));
    }

    public function notWorkingArrowFunction()
    {
        $x = 1;
        $fn = fn() => $x++; // Has no effect
        $fn();
        var_export($x);  // Outputs 1
    }


}
