<?php


namespace zetsoft\service\optima;

require Root . '/vendori/optima/Html/vendor/ivoglent/yii2-minify/libs/MinifyHtml.php';

use ivoglent\yii2\minify\libs\MinifyHtml;
use zetsoft\system\kernels\ZFrame;

/**
 * Class IvoglentYii2Minify
 * @package zetsoft\service\optima
 * @author OtabekKarimov
 * @todo An yii2 extension which supported minify html, css and js
 * https://packagist.org/packages/ivoglent/yii2-minify
 * https://github.com/ivoglent/yii2-minify
 */
class IvoglentYii2Minify extends ZFrame
{

    //start|OtabekKarimov|2020-10-13

    #region Functions

    public function minifyHTML($htmlSource)
    {
        return MinifyHtml::minify($htmlSource);
    }

    #endregion

    #region Examples

    public function simpleExample()
    {
        $beforeHtml = file_get_contents(__DIR__ . '/sample/demo.html');
        $afterHtml = $this->minifyHTML($beforeHtml);
        file_put_contents(__DIR__ . '/results/resultIvoglent.html', $afterHtml);
    }

    #endregion
    //end|OtabekKarimov|2020-10-13

}