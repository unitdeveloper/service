<?php

/*
 * @author SukhrobNuraliev
 * https://packagist.org/packages/brick/math
 * Arbitrary-precision arithmetic library
 */


namespace zetsoft\service\maths;

require Root.'/vendori/maths/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigRational;


class Math extends ZFrame
{
    public function example()
    {
        $biginteger = BigInteger::of('1.00'); // 1
        $bigdecimal = BigDecimal::of(1/8);    // 0.125
        $bigrational = BigRational::of('1.1'); // 11/10
        echo $bigdecimal;
    }
}
