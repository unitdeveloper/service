<?php

namespace zetsoft\service\optima;

//require Root . './vendors/optima/Html/vendor/autoload.php';
require Root . './vendors/parser/html/vendor/autoload.php';

/**
 * Author:
 * @licence: AlisherXayrillayev
 */

/**
 * Class VokuHtmlMin
 * @package zetsoft\service\optima
 * https://packagist.org/packages/voku/html-min
 *
 * @todo HtmlMin is a fast and very easy to use PHP library that minifies given HTML5 source by removing extra whitespaces, comments and other unneeded characters without breaking the content structure
 */

use voku\helper\HtmlMin;
use zetsoft\system\kernels\ZFrame;

class VokuHtmlMin extends ZFrame
{

    #region Vars

    #endregion


    //start|AlisherXayrillayev|2020-10-12


    #region Example
    public function example()
    {
        $this->exampleTwo();
    }

    /**
     * @param $html
     * @return string
     *
     */
    public function minify($html)
    {
        if ($html === null) return null;

        $htmlMin = new HtmlMin();
        return $htmlMin->minify($html);
    }

    public function exampleTwo()
    {
        $sourceHtml = __DIR__ . '/sample/market.html';
        $resultHtml = __DIR__ . '/results/resultMarket_Voku.html';

        $html = file_get_contents($sourceHtml);

        $minifiedHtml = $this->minify($html);

        file_put_contents($resultHtml, $minifiedHtml);
    }

    #endregoin

    //end|AlisherXayrillayev|2020-10-12


    #region Old Code

    public function init()
    {
        parent::init();
    }

    public function minify2($html)
    {
        $htmlMin = new HtmlMin();
        return $htmlMin->minify($html);
    }

    public function exampleTwo2()
    {
        $sourceHtml = __DIR__ . '/sample/market.html';
        $resultHtml = __DIR__ . '/results/resultMarket_Voku.html';
        //vdd($sourceHtml);
        $html = file_get_contents($sourceHtml);

        $htmlMin = new HtmlMin();

        $minifiedHtml = $htmlMin->minify($html);

        file_put_contents($resultHtml, $minifiedHtml);
    }

    public function sample()
    {
        $html = "
            <html>
              \r\n\t
              <body>
                <ul style=''>
                  <li style='display: inline;' class='foo'>
                    \xc3\xa0
                  </li>
                  <li class='foo' style='display: inline;'>
                    \xc3\xa1
                  </li>
                </ul>
              </body>
              \r\n\t
            </html>
            ";

        $htmlMin = new HtmlMin();

        echo $htmlMin->minify($html);

    }


    public function exampleOne()
    {
        $html = "
            <html>
          \r\n\t
          <body>
            <ul style=''>
              <li style='display: inline;' class='foo'>
                \xc3\xa0
              </li>
              <li class='foo' style='display: inline;'>
                \xc3\xa1
              </li>
            </ul>
          </body>
          \r\n\t
        </html>
        ";
        $htmlMin = new HtmlMin();

        echo $htmlMin->minify($html);
    }
    #endregion
}

