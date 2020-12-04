<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\office;


use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use zetsoft\models\shop\ShopOrder;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use PhpOffice\PhpSpreadsheet\IOFactory;
use zetsoft\system\module\Models;

class ExcelQuery extends ZFrame
{
    public $tableHeadDone = false;
    public $resultOpen = false;
    public $download = true;
    public $docType = 'excels';
    public $docTemplate = '/binary/excels/call/export.xlsx';


    public $modelClass;
    public $query = null;


    public function run()
    {
        /** @var Models $model */
        $model = new $this->modelClass();
        $columns = $model->columns;

        // prepare select statement
        $colsToSelect = [];
        foreach ($columns as $colName => $formDb) {
            if (!$formDb->hiddenFromExport)
                $colsToSelect[] = $colName;
        }
        $selectEpr = implode(', ', $colsToSelect);

        /*
            $models = $this->modelClass::find()
                ->select($selectEpr)
                ->where($this->query)
                 ->orderBy(['id' => SORT_ASC])
                ->all();
        */

        $models = $this->query;


        $attributeLabels = $model->attributeLabels();

        $excelTemplate = Root . $this->docTemplate;

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = IOFactory::createReader('Xlsx');

        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($excelTemplate);

        $sheet = $spreadsheet->getActiveSheet();

        $activeSheetIndex = $spreadsheet->setActiveSheetIndex(0);


        $lastElement = end($models);

        // filling table
        foreach ($models as $modelNumber => $modelM) {
            $headerIterator = 0;
            foreach ($modelM as $colName => $colVal) {

                if (!in_array($colName, $colsToSelect))
                    continue;

                Az::$app->forms->wiData->clean();
                Az::$app->forms->wiData->model = $modelM;
                Az::$app->forms->wiData->attribute = $colName;
                $value = Az::$app->forms->wiData->value(false);

                $pCoord = $this->getNameFromNumber($headerIterator);
                $activeSheetIndex->setCellValue($pCoord . ($modelNumber + 2), $value);

                /*if($modelM == $lastElement) {
                    $activeSheetIndex->setCellValue($pCoord . '1', $attributeLabels[$colName]);

                    $sheet->getColumnDimension($pCoord)->setAutoSize(true);
                }*/

                $headerIterator++;
            }
            $this->tableHeadDone = true;
        }


        // setting table columns heads
        $attributeIterator = 0;
        foreach ($attributeLabels as $headColName => $headColLabel) {

            if (!in_array($headColName, $colsToSelect))
                continue;

            $pCoord = $this->getNameFromNumber($attributeIterator);
            $activeSheetIndex->setCellValue($pCoord . '1', $headColLabel);

            $sheet->getColumnDimension($pCoord)->setAutoSize(true);
            $attributeIterator++;
        }

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Data');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $root = Az::getAlias('@zetsoft');
        $uploadPath = '/upload/excelz/eyuf/';
        $location = $root . $uploadPath;


        ZFileHelper::createDirectory($location);

        $filename = $model->configs->title . '_' . date('Y-m-d_h-i-s') . '.xlsx';

        $writer->save($location . $filename);

        $fullPath = '../..' . $uploadPath . $filename;

        if ($this->download) {

            $this->urlRedirect([
                'play',
                'file' => $fullPath,
            ]);
        }


        if ($this->resultOpen) {
            shell_exec($location . $filename);
        }

    }

    private function getNameFromNumber($num)
    {

        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);

        if ($num2 > 0)
            return $this->getNameFromNumber($num2 - 1) . $letter;
        else
            return $letter;

    }


}
