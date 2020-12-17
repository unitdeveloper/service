<?php

/**
 * @author NurbekMakhmudov
 */

namespace zetsoft\service\parser;


require Root . '/vendors/fileapp/office/vendor/autoload.php';

use \PhpOffice\PhpWord\IOFactory;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use zetsoft\system\kernels\ZFrame;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class PhpExcelImport
 * @package zetsoft\service\parser
 * XML-based formats such as OfficeOpen XML, Excel2003 XML, OASIS and Gnumeric are susceptible
 * to XML External Entity Processing (XXE) injection attacks when reading spreadsheet files.
 * https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-files/
 * @author NurbekMakhmudov
 */
class PhpExcelImport extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-23
    
    private $spreadsheet;

    /**
     * initialization
     */
    public function init()
    {
        parent::init();
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * @param $filePath
     * @param string $inputFileType
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @author NurbekMakhmudov
     * Parse Excel file to array 
     */
    public function excelToArray($filePath, $inputFileType = 'Xlsx')
    {

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        $reader->setReadDataOnly(true);

        $worksheetData = $reader->listWorksheetInfo($filePath);

        foreach ($worksheetData as $worksheet) {

            $sheetName = $worksheet['worksheetName'];

            /*echo "<h4>$sheetName</h4>";*/

            $reader->setLoadSheetsOnly($sheetName);
            $this->spreadsheet = $reader->load($filePath);

            $worksheet = $this->spreadsheet->getActiveSheet();

            return $worksheet->toArray();
        }
    }


    #region Examples


    public function excelParserExample()
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/sampleData/example1.xlsx';

        $res = $this->excelToArray($inputFileName, $inputFileType);

        print_r($res);
    }

    #endregion


    //end|NurbekMakhmudov|2020-10-23

}
