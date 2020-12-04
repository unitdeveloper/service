<?php

namespace zetsoft\service\parser;


require Root . '/vendori/parser/html/vendor/autoload.php';


use voku\helper\HtmlMin;
use WyriHaximus\HtmlCompress\Factory;
use zetsoft\system\kernels\ZFrame;

/*
 * class WyrihaximusHtmlCompress
 * @package zetsoft/service/parser
 *
 * @author SherzodMangliyev
 *
 * https://packagist.org/packages/wyrihaximus/html-compress
 */

class HtmlCompress extends ZFrame
{

    public ?string $fileSource = null;
    public ?string $fileResult = null;


    public function init()
    {
        parent::init();
    }


    public function minify($html)
    {
        $parser = Factory::constructSmallest();

        $compressedHtml = $parser->compress($html);

        return $compressedHtml;
        
    }
    #endregion


    #region example
    public function example()
    {
//        $this->exampleAdvanced();
        $this->exampleBasic();
    }


    public function exampleBasic()
    {
        $this->fileSource = __DIR__ . '/sample/source.html';
        $this->fileResult = __DIR__ . '/results/result_HtmlCompress.html';
        $html = file_get_contents($this->fileSource);
        $parser = Factory::constructSmallest();
        $compressedHtml = $parser->compress($html);

        file_put_contents($this->fileResult, $compressedHtml);
    }


    public function exampleAdvanced()
    {
        $this->fileSource = __DIR__ . '/sample/source.html';
        $this->fileResult = __DIR__ . '/results/resultAdvanced_HtmlCompress.html';
        $html = file_get_contents($this->fileSource);

        $htmlMin = new HtmlMin();
        $htmlMin->doRemoveHttpPrefixFromAttributes();
        $htmlMin->doMakeSameDomainsLinksRelative(['example.com']);

        $parser = Factory::constructSmallest()->withHtmlMin($htmlMin);
        $compressedHtml = $parser->compress($html);

        file_put_contents($this->fileResult, $compressedHtml);

    }
    #endregion


}
