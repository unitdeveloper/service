<?php

namespace zetsoft\service\iterate;

use Spatie\Typed\Collection;
use zetsoft\system\kernels\ZFrame;

/**
 *
 * Class    MacrosTest
 * @package zetsoft\service\iterate
 *
 *
 * https://laravel.com/docs/7.x/collections
 * https://pineco.de/extendig-laravels-collection-with-macros/
 * https://laravel.com/docs/7.x/providers
 * https://www.google.com/search?q=extensd+laravel+Collection+macro&sourceid=chrome&ie=UTF-8
 * https://styde.net/php-traits-en-laravel-5-1/
 */

class MacrosTest extends ZFrame
{



    public function test()
    {
        $data = $this->retriveAt([1,2,3], 2);
        vd($data);
    }

    public function retriveAt($collection, $currentItem)
    {
        $collection = collect($collection);
        $result = $collection->after($currentItem);
        return $result;
    }
}
