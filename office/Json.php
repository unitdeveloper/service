<?php

/**
 *
 *
 * Author:  Bahodir
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\office;


use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use PhpOffice\PhpSpreadsheet\IOFactory;
use zetsoft\system\module\Models;

class Json extends ZFrame
{
    public $query = null;
    public $modelName = null;
    public $checkkeys = null;

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

        $this->query = unserialize($this->query, ['string']);
//          $this->query = json_encode($this->)

        if ( $this->checkkeys ){ 
            if ($this->query)
                $models = $this->modelClass::find()
                    ->select($selectEpr)
                    ->where($this->query)
                    ->andWhere(['id'=>$this->checkkeys])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();
            else
                $models = $this->modelClass::find()
                    ->select($selectEpr)
                    ->Where(['id'=>$this->checkkeys])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();
        }

        else
            $models = $this->modelClass::find()
                ->select($selectEpr)
                ->where($this->query)
                ->orderBy(['id' => SORT_ASC])
                ->all();



        $attributeLabels = $model->attributeLabels();

        // setting table columns heads
        $columnnames = [];
        foreach ($attributeLabels as $headColName => $headColLabel) {

            if (!in_array($headColName, $colsToSelect))
                continue;

            //$headColLabel;
            $columnnames[] = $headColLabel;
        }
        // filling json
        $parent = null;
        foreach ($models as $modelNumber => $modelM) {
            $child = null;
            $counter = 0;
            foreach ($modelM as $colName => $colVal) {

                if (!in_array($colName, $colsToSelect))
                    continue;

                Az::$app->forms->wiData->clean();
                Az::$app->forms->wiData->model = $modelM;
                Az::$app->forms->wiData->attribute = $colName;
                $value = Az::$app->forms->wiData->value(false);
                $child[ $columnnames[$counter] ]=$value;
                $counter++;
            }
            $parent['data'][]=$child;
        }

        $json_data = json_encode($parent, JSON_UNESCAPED_UNICODE);

        $directory = Root . '/upload/uploaz/market/json_temp/';
        $now = date('d.m.Y_H-i-s');
        $filePath = $directory . $this->modelName .'_' . $now . '.json';
        file_put_contents($filePath, $json_data);
        return $filePath;



    }
}
