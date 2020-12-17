<?php


namespace zetsoft\service\parser;

require Root . '/vendors/fileapp/office/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use zetsoft\system\kernels\ZFrame;


class PhpSpreadsheet extends ZFrame
{

public function read()
{

    $fileXLS = file_get_contents(__DIR__ . '/sample/dataNMa.xls');



}
    


}
