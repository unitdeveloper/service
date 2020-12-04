<?php

/**
 *
 * @package myclabs/deep-copy
 * @author SukhrobNuraliev
 * https://packagist.org/packages/myclabs/deep-copy
 *
 */

namespace zetsoft\service\utility;

use DeepCopy\DeepCopy as DeepCop;
use zetsoft\system\kernels\ZFrame;

class DeepCopy extends ZFrame
{
    /**
     * creates deep copies (clones)  of your objects
     *
     * @param object $object
     */

    public function copyObject($object)
    {
        $copier = new DeepCop();
        $copy = $copier->copy($object);

        vdd($copy);
    }
}
