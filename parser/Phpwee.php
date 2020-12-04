<?php

namespace zetsoft\service\parser;


require Root . '/vendori/parser/html/vendor/autoload.php';



use zetsoft\system\kernels\ZFrame;


/*
 * class TinyHtmlMinifier
 * @package zetsoft/service/parser
 *
 * @author Sukhrob Nuraliev
 * https://packagist.org/packages/pfaciana/tiny-html-minifier
 */

class Phpwee extends ZFrame
{

    /**
     *
     * Function  minify
     * @param $html
     * @return  string
     */
    #region minify
    public function minify($html){
        return \PHPWee\Minify::html($html);
    }
    #endregion




}
