<?php


namespace zetsoft\service\optima;

//require Root . '/vendori/optima/Html/vendor/autoload.php';
require Root . '/vendori/parser/html/vendor/autoload.php';

use voku\helper\HtmlMin;
use WyriHaximus\HtmlCompress\Factory;
use zetsoft\system\kernels\ZFrame;

/**
 * Class WyrihaximusHtmlCompress
 * @package zetsoft\service\optima
 * @author NurbekMakhmudov
 * @todo Compress/minify your HTML
 * https://packagist.org/packages/wyrihaximus/html-compress
 * https://github.com/WyriHaximus/HtmlCompress
 */
class WyrihaximusHtmlCompress extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-13

    private $compressor;
    private $htmlMin;

    /**
     * initialization
     */
    public function init()
    {
        parent::init();
        $this->compressor = Factory::constructSmallest();
        $this->htmlMin = new HtmlMin();
    }

    /**
     * @param $htmlSource
     * @return mixed
     * @author NurbekMakhmudov
     * @todo Compress/minify your HTML
     */
    public function compressHtml($htmlSource)
    {
        return $this->compressor->compress($htmlSource);
    }

    public function compressHtmlAdvanced($sourceHtml, array $domains)
    {
        $this->htmlMin->doRemoveHttpPrefixFromAttributes();
        $this->htmlMin->doMakeSameDomainsLinksRelative($domains);
        $this->compressor->withHtmlMin($this->htmlMin);
        return $this->compressor->compress($sourceHtml);
    }

    #region Examples

    public function basicExample()
    {
        $sourceHtml = file_get_contents(__DIR__ . '/sample/demo.html');
        $compressedHtml = $this->compressHtml($sourceHtml);
        file_put_contents(__DIR__ . '/results/resultWyrihaximusHtmlCompressDemoBasic.html', $compressedHtml);
    }

    public function advancedExample()
    {
        $sourceHtml = file_get_contents(__DIR__ . '/sample/demo.html');
        $compressedHtml = $this->compressHtmlAdvanced($sourceHtml, ['example.com', 'zetsoft.uz']);
        file_put_contents(__DIR__ . '/results/resultWyrihaximusHtmlCompressDemoAdvanced.html', $compressedHtml);
    }

    #endregion

    //end|NurbekMakhmudov|2020-10-13


}