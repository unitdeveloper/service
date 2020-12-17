<?php


namespace zetsoft\service\parser;

require Root . '/vendors/parser/excel/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use \SimpleXLSX;

/**
 * Class ShuchkinSimplexlsxExcelParser
 * @package zetsoft\service\parser
 * @author NurbekMakhmudov
 * @todo Parse and retrieve data from Excel XLSX files. MS Excel 2007  workbooks PHP reader.
 * https://packagist.org/packages/shuchkin/simplexlsx
 * https://github.com/shuchkin/simplexlsx
 */
class ShuchkinSimplexlsxExcelParser extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-13

    /**
     * @param $fileXLSX
     * @return array|bool
     * @author NurbekMakhmudov
     * @todo Parse Excel XLSX files to array.
     */
    public function xlsxToArray($fileXLSX)
    {
        if ($xlsx = SimpleXLSX::parseData($fileXLSX))
            return $xlsx->rows();
        else
            return SimpleXLSX::parseError();
    }

    /**
     * @param $fileXLSX
     * @return array
     * @author NurbekMakhmudov
     * @todo Parse Excel XLSX files to array without header.
     */
    public function xlsxToArrayWithoutHeader($fileXLSX)
    {
        if ($xlsx = SimpleXLSX::parseData($fileXLSX)) {
            $header_values = $rows = [];
            foreach ($xlsx->rows() as $k => $r) {
                if ($k === 0) {
                    $header_values = $r;
                    continue;
                }
                $rows[] = array_combine($header_values, $r);
            }
            return $rows;
        }
    }


    /**
     * @param $fileXLSX
     * @return bool
     * @author NurbekMakhmudov
     * @todo Parse Excel XLSX files to Html Table.
     */
    public function xlsxToHtmlTable($fileXLSX)
    {
        if ($xlsx = SimpleXLSX::parseData($fileXLSX)) {
            $htmlTable = '<table border="1" cellpadding="3" style="border-collapse: collapse">' . "\n";
            foreach ($xlsx->rows() as $r) {
                $htmlTable .= "\t" . '<tr><td>' . implode('</td><td>', $r) . '</td></tr>' . "\n";
            }
            $htmlTable .= '</table>';
            return $htmlTable;
        } else
            return SimpleXLSX::parseError();
    }


    #region   Examples

    public function xlsxToArrayExample()
    {
        $fileXLSX = file_get_contents(__DIR__ . '/sample/books.xlsx');
        $res = $this->xlsxToArray($fileXLSX);
        print_r($res);
    }

    public function xlsxToArrayWithoutHeaderExample()
    {
        $fileXLSX = file_get_contents(__DIR__ . '/sample/books.xlsx');
        $res = $this->xlsxToArrayWithoutHeader($fileXLSX);
        print_r($res);
    }


    public function xlsxToHtmlTableExample()
    {
        $fileXLSX = file_get_contents(__DIR__ . '/sample/books.xlsx');
        $res = $this->xlsxToHtmlTable($fileXLSX);
        print_r($res);
    }


    #endregion

    //end|NurbekMakhmudov|2020-10-13

    // pay OK

}
