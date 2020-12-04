<?php

/**
 * Author: Sardor
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\illuminate;



use zetsoft\system\kernels\ZFrame;
use Illuminate\Support\Collection;

class ZLCollection extends ZFrame
{
     public function collect($value = null): Collection
     {
        return new Collection($value);
     }
     public function index(){
        return $this->collect([1,2,3,4])->all();
     }
}
