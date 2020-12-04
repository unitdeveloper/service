<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;


use zetsoft\system\kernels\ZFrame;

class Davlat extends ZFrame
{

    public $my = '11';

    public function test($a)
    {
        $b = $a . '-' . $this->my;
        $c = $b . '-' . $this->inline();
        return str_repeat($c, 2);
    }


    private function inline()
    {
        return $this::className();

    }

}
