<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\menu;


use zetsoft\system\kernels\ZFrame;

class Davlat extends ZFrame
{

    //public $my = '11';

    public function test($a)
    {
       // $b = $a . '-' . $this->my;
       // $c = $b . '-' . $this->inline();
        return $a;
    }


    private function inline()
    {
        return $this::className();

    }

}
