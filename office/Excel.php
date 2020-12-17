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
use yii\data\ActiveDataProvider;
use zetsoft\dbitem\core\ExportItem;
use zetsoft\models\shop\ShopCourier;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZFormatter;
use zetsoft\system\kernels\ZFrame;
use PhpOffice\PhpSpreadsheet\IOFactory;
use zetsoft\system\module\Models;
use zetsoft\widgets\former\ZExportWidget;
use zetsoft\widgets\navigat\ZButtonWidget;

class Excel extends ZFrame
{
    public $tableHeadDone = false;
    public $resultOpen = false;
    public $download = true;
    public $docType = 'excels';
    public $docTemplate = '/binary/excels/call/export.xlsx';
    public $checkkeys = null;
    public $hidden = false;
    //start: MurodovMirbosit 12.10.2020
    public $type = 'model';
    public $provider = null;
    public $checkKeys = [];


    private function getProviderModels()
    {

        /** @var ActiveDataProvider $provider */
        $provider = $this->provider;

        $provider->setPagination(false);
        $provider->prepare();

        return $provider->getModels();

    }

    public function clean()
    {

        $this->provider = null;
        $this->checkKeys = null;
        $this->model = null;

    }

    public $action = null;

    public $export;

    public function dynaExport($widget)
    {
        $exportItem = new ExportItem();
        $exportItem->title = 'EXCEL';
        $exportItem->icon = 'text-success far fa-file-excel';
        $exportItem->url = '/api/core/files/excel';
        $exportItem->method = 'formToExcel';

        $export[] = $exportItem;

        return ZExportWidget::widget([
            'id' => $widget->modelClassName . '-export',
            'model' => $widget->model,
            'data' => ZArrayHelper::merge($export, $widget->_config['export']),
            
            'provider' => $widget->provider,
            'config' => [
                'type' => $widget->type,
                'btnType' => ZButtonWidget::btnType['button'],
                'grapes' => false,
                'hidden' => true,
                'configs' => $widget->model->configs,
                'action' => '',
                'class' => $widget->_config['toolbarButtonsClass'],
                'modelClassName' => $widget->modelClassName,
            ]
        ]);

    }

    public function formToExcel()
    {

        $models = ZArrayHelper::getValue($this->provider, 'allModels');

        $columns = ZArrayHelper::getValue($this->provider, 'columns');

        $excelTemplate = Root . $this->docTemplate;

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = IOFactory::createReader('Xlsx');

        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($excelTemplate);

        $sheet = $spreadsheet->getActiveSheet();

        $activeSheetIndex = $spreadsheet->setActiveSheetIndex(0);

        $lastElement = end($models);
        $className = '';
        foreach ($models as $modelNumber => $modelM) {
            if (ZArrayHelper::keyExists($modelNumber, $models)) {
                $className = $modelM['className'];
            }
            $headerIterator = 0;
            foreach ($modelM as $colName => $colVal) {
                // start|MurodovMirbosit|17.10.2020
                if (ZArrayHelper::keyExists($colName, $columns)) {
                    $pCoord = $this->getNameFromNumber($headerIterator);
                    $activeSheetIndex->setCellValue($pCoord . ($modelNumber + 2), $colVal);
                    $headerIterator++;
                }
                // end|MurodovMirbosit|17.10.2020
            }

            $this->tableHeadDone = true;

        }
        $attrs = 0;
        $titleProvider = '';
        foreach ($columns as $headColName => $headColLabel) {
            if (ZArrayHelper::keyExists($headColName, $columns)) {
                $titleProvider = $headColLabel['title'];
            }
            $pCoord = $this->getNameFromNumber($attrs);
            $activeSheetIndex->setCellValue($pCoord . '1', $titleProvider);
            $sheet->getColumnDimension($pCoord)->setAutoSize(true);
            $attrs++;
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

        $filename = $className . '_' . date('Y-m-d_h-i-s') . '.xlsx';

        $writer->save($location . $filename);

        $fullPath = '../..' . $uploadPath . $filename;

        if ($this->download) {

            $this->urlRedirect([
                'play',
                'file' => $fullPath,
            ]);
        }

        if ($this->resultOpen)
            shell_exec($location . $filename);
    }

    //end: MurodovMirbosit 12.10.2020

    public function run()
    {
        //start: MurodovMirbosit 12.10.2020
        if ($this->type === 'form') {
            return $this->formToExcel();
        }
        //end: MurodovMirbosit 12.10.2020 lines 81

        /** @var Models $model */

        /** @var Models $model */

        $model = $this->model;

        $columns = $model->columns;
        // prepare select statement
        $colsToSelect = [];
        foreach ($columns as $colName => $formDb) {
            if (!$formDb->hiddenFromExport) {
                $colsToSelect[] = $colName;
            }
        }

        $selectEpr = implode(', ', $colsToSelect);


        if ($this->checkkeys)
            $models = $this->modelClass::find()
                ->select($selectEpr)
                ->where(['id' => $this->checkkeys])
                ->orderBy(['id' => SORT_ASC])
                ->all();

        else
            $models = $this->modelClass::find()
                ->select($selectEpr)
                ->all();

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

                if (!in_array($colName, $colsToSelect, true))
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

            if (!in_array($headColName, $colsToSelect, true))
                continue;

            $pCoord = $this->getNameFromNumber($attributeIterator);
            $activeSheetIndex->setCellValue($pCoord . '1', $headColName);$activeSheetIndex->setCellValue($pCoord . '2', $headColLabel);

            $sheet->getColumnDimension($pCoord)->setAutoSize(true);
            $attributeIterator++;
        }

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Data');
        $spreadsheet->getActiveSheet()->getStyle("A2:ZZ2")->getFont()->setBold( true );

        $spreadsheet->getActiveSheet()->getStyle("A1:ZZ1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('464a42');
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

        if ($this->resultOpen)
            shell_exec($location . $filename);
    }

    private function getNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2 - 1) . $letter;
        }

        return $letter;
    }
}
