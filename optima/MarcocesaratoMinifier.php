<?php

namespace zetsoft\service\optima;

require Root . '/vendori/optima/Html/vendor/autoload.php';
/**
 * Author:
 * @licence AlisherXayrillayev
 */

/**
 * Class MarcocesaratoMinifier
 * @package zetsoft\service\optima
 * https://packagist.org/packages/marcocesarato/minifier
 */


use marcocesarato\minifier\Minifier;
use zetsoft\system\kernels\ZFrame;


// Cannot minify large files

class MarcocesaratoMinifier extends ZFrame
{

    #region Core
    public function init()
    {
        parent::init();
    }

    #endregion

    //start|AlisherXayrillayev|2020-10-12

    #region Example

    public function sample() {
        $this->exampleTwo();
    }

    public function exampleTwo()
    {
        $sourceHtml = __DIR__ . '/sample/market.html';
        $resultHtml = __DIR__ . '/results/resultMarket_Marcocesarato.html';

        $html = file_get_contents($sourceHtml);

        $minifier = new Minifier();
        $min_html = $minifier->minifyHTML($html);

        file_put_contents($resultHtml, $min_html);
    }

    #endregion

    //end|AlisherXayrillayev|2020-10-12

    #region Old Code

    public function example()
    {
        ob_start();

        $html = <<<EOD
<html>
<head>
    <title>Hello World</title>
</head>
<body>
    <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Default box -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Title</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                        <i class="fas fa-minus"></i></button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove" data-toggle="tooltip" title="Remove">
                                        <i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                Start creating your amazing application!
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                Footer
                            </div>
                            <!-- /.card-footer-->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>
</body>
</html>
EOD;

        echo $html;

        $content = ob_get_contents();
        ob_clean();

        $minifier = new Minifier();
        $min_html = $minifier->minifyHTML($content);

        echo $min_html;
    }

    #endregion
}

