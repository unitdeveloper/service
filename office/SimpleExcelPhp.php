<?php
namespace zetsoft\service\office;


use zetsoft\system\kernels\ZFrame;
use SimpleExcel\SimpleExcel;
use SimpleExcel\Spreadsheet\Worksheet;

class SimpleExcelPhp extends ZFrame
{

    public function test(){
        $this->ExcelParser();
    }

    public function ExcelParser()
    {
        $excel = new SimpleExcel('CSV');
        $excel->parser->loadFile('D:\test.csv');

//        echo $excel->parser->getCell(1, 1);

        $excel->convertTo('XML');
        $excel->writer->addRow(array('add', 'another', 'row'));
        $excel->writer->saveFile('D:/example.xml');

    }

}