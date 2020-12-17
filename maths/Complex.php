<?php

/*
 * @author Amir
 * https://packagist.org/packages/brick/math
 * PHP Class for working with complex numbers
 *
 */


namespace zetsoft\service\maths;

require Root.'/vendors/maths/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use Complex\Complex as MathComplex;

class Complex extends ZFrame
{
    public function example()
    {
        $real = 1.23;
        $imaginary = -4.56;
        $suffix = 'i';

        $complexObject = new MathComplex($real, $imaginary, $suffix);
        echo $complexObject;
    }
}
