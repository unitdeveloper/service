<?php

/**
 *
 *
 * Author:  JamashidIsmailov
 *
 */

namespace zetsoft\service\tests;

use phpDocumentor\Reflection\Types\Null_;
use zetsoft\system\kernels\ZFrame;


class JamshidTest extends ZFrame
{

    public function pluss($s1 = null, $s2=null){
        return $s1 + $s2;
    }

    public function minus($s1, $s2){
        return $s1 - $s2;
    }

    public function delit($s1, $s2){
        return $s1 / $s2;
    }

    public function increasing($s1, $s2){
        return $s1 * $s2;
    }



}
