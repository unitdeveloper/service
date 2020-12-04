<?php
/**
 * Author:  Xolmat Ravshanov
 */
namespace zetsoft\service\parser;


use zetsoft\system\kernels\ZFrame;
use voku\helper\HtmlMin;

/**
 * Class Htmlmain
 * @package zetsoft\service\parser
 *
 * https://packagist.org/packages/voku/html-min
 */

class HtmlMinification extends ZFrame
{

   #region Vars

   #endregion

    public function example()
    {
        $this->exampleTwo();
    }

    public function exampleTwo()
    {
        $htmlMin = new HtmlMin();

        $html = file_get_contents('test.html');
        echo $htmlMin->minify($html);
    }
}

