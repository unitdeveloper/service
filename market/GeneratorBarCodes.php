<?php

/**
 * Author:  Abdurakhmonov Umid
 * Date:    03.07.2020
 *
 */

namespace zetsoft\service\market;


use Picqer\Barcode\BarcodeGeneratorJPG;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendors/image/ALL/vendor/autoload.php';

class GeneratorBarCodes extends ZFrame
{

    public $order;

    public function generateBarcodeJpgTest()
    {
        $this->generateBarcodeJpg(213);
    }

    #region init

    public function init()
    {
        parent::init();
    }

    #endregion

    ##region generate
    public function generateBarcodeJpg($id)
    {
        
            $generatorJpg = new BarcodeGeneratorJPG();

            $fileJpg = Root . '/upload/imagez/image' . $id . '.jpg';

            $barCode = $id;

            file_put_contents($fileJpg, $generatorJpg->getBarcode($barCode, $generatorJpg::TYPE_CODE_128, 2, 120));

    }

    #endregion
}



