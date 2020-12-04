<?php

namespace zetsoft\service\optima;

//require Root . '/vendori/optima/Html/vendor/autoload.php';
require Root . '/vendori/parser/html/vendor/autoload.php';


use Minifier\TinyMinify;
use zetsoft\system\kernels\ZFrame;

/**
 * Class TinyHtmlMinifier
 * @package zetsoft\service\optima
 * @author NurbekMakhmudov
 *
 * @todo Minify HTML in PHP with just a single class
 * https://packagist.org/packages/pfaciana/tiny-html-minifier
 * https://github.com/pfaciana/tiny-html-minifier
 */
class TinyHtmlMinifier extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-13

    /**
     * @param $html
     * @return string
     * @author NurbekMakhmudov
     * @todo Minifier html file
     */
    public function htmlMinifier($html)
    {
        return TinyMinify::html($html);
    }

    /**
     * @param $html
     * @param $collapseWhitespace
     * @param $disableComments
     * @return string
     * @author NurbekMakhmudov
     * @todo Minifier html file with options
     */
    public function htmlMinifierWithOptions($html, $collapseWhitespace = false, $disableComments = false)
    {
        return TinyMinify::html($html, $options = [
            'collapse_whitespace' => $collapseWhitespace,
            'disable_comments' => $disableComments,
        ]);
    }


    #region Example

    public function simpleExample()
    {
        $beforeHtml = file_get_contents(__DIR__ . '/sample/demo.html');
        $afterHtml = $this->htmlMinifier($beforeHtml);
        file_put_contents(__DIR__ . '/results/resultTinyHtmlMinifierDemo.html', $afterHtml);
    }

    public function exampleMinifierOptions()
    {
        $htmlSource = file_get_contents(__DIR__ . '/sample/demo.html');
        $htmlRes = $this->htmlMinifierWithOptions($htmlSource, false, false);
        file_put_contents(__DIR__ . '/results/resultTinyHtmlMinifierOptionsDemo.html', $htmlRes);
    }

    #endregion

    //end|NurbekMakhmudov|2020-10-13

    // pay OK

}
