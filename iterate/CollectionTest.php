<?php

namespace zetsoft\service\iterate;

use Cake\Collection\Collection;
use zetsoft\system\kernels\ZFrame;

class CollectionTest extends  ZFrame
{
    public function test()
    {
        $data = $this->load(['a' => 1, 'b' => 2, 'c' => 3]);
        vd($data);
    }

    public function load($data)
    {
        $collection = new Collection($data);

        $overOne = $collection->filter(function ($value) {
            return $value > 1;
        });

        return $overOne;
    }
}