<?php


namespace zetsoft\service\office;

require Root . '/vendors/parser/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use ncjoes\office-converter\src\OfficeConverter\OfficeConverter;

/**
 * Class OfficeConverter
 * @package zetsoft\service\office
 *
 * https://packagist.org/packages/ncjoes/office-converter
 */

class OfficeConvert extends ZFrame
{


    public ?string $converter = null;



    public function example(){

        $this->exampleOne();

    }


    public function exampleOne(){

        // $this->converter = new OfficeConverter(__DIR__ . '/sample/test-file.docx');
        $this->converter = new OfficeConverter('D:\doc1.docx');
        // $this->converter->convertTo(__DIR__ . '/result/'. 'output-file.pdf');
        $this->converter->convertTo('D:\doc1.pdf');
    }
}
