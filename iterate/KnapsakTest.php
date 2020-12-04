<?php

namespace zetsoft\service\iterate;

use DusanKasan\Knapsack\Collection;
use zetsoft\system\kernels\ZFrame;

class KnapsakTest extends ZFrame
{
    public function test()
    {
        $data = $this->mapReduce([1, 2, 3]);
        vd($data);
    }

    public function mapReduce($data)
    {
        $result = Collection::from($data)
            ->map(function($v) {return $v * 2;})
            ->reduce(function($tmp, $v) {return $tmp + $v;}, 0);

        return $result;
    }
}