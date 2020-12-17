<?php

/*
 * @author SukhrobNuraliev
 * https://packagist.org/packages/brick/math
 * Simple math expressions calculator
 */


namespace zetsoft\service\maths;

require Root . '/vendors/human/maths/vendor/autoload.php';

use League\Uri\Exception;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use NXP\MathExecutor as Executor;

class MathExecutor extends ZFrame
{
    public function example()
    {
        $executor = new Executor();

        echo $executor->execute('1 + 2 * (2 - (4+10))^2 + sin(10)');
    }

    public function run($formula)
    {
        try {
            $executor = new Executor();
            return $executor->execute($formula);

        } catch (\Exception $exception) {
            //  vd($exception->getMessage());
            Az::warning($exception->getMessage());
            return null;
        }
    }
}
