<?php

namespace zetsoft\service\gitapp;


require Root.'/vendors/gitapp/vendor/autoload.php';

use yii\base\ErrorException;
use zetsoft\system\kernels\ZFrame;


/*
 * class PhpGitHooks
 * @package zetsoft/service/gitapp
 * @author Sherzod Mangliyev
 * https://packagist.org/packages/wyrihaximus/html-compress
 */

class PhpGitHooks extends ZFrame
{


   #region example

    public function example()
    {
        require dirname(__DIR__) . '/vendor/autoload.php';

        $parser = Factory::constructSmallest();
        $compressedHtml = $parser->compress($sourceHtml);
        vd($compressedHtml);
    }


    #endregion
}
