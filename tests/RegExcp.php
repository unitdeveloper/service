<?php
/*
* Author: Madaminov Shaykhnazar
*
*/

namespace zetsoft\service\tests;

use Spatie\Regex\Regex;
use zetsoft\system\helpers\ZTest;
use zetsoft\system\kernels\ZFrame;

class RegExcp extends ZFrame
{
    #region Vars

    #endregion
    #region Core

    public function init(){
        return $this->test();
    }
    public function test(){
        return Regex::match('/a/', 'abc');
    }
    #endregion
    public function assertTest(){
        ZTest::assertEquals(1, 2);
    }

}
