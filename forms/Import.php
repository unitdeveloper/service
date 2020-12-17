<?php

/**
 *
 *
 * @author: DavlatovRavshan
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\forms;


use yii\data\ActiveDataProvider;
use zetsoft\dbitem\core\ExportItem;
use zetsoft\models\dyna\DynaImport;
use zetsoft\models\shop\ShopCourier;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\UserCompany;
use zetsoft\models\ware\WareAccept;
use zetsoft\service\App\eyuf\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFormatter;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZExportMenu;
use zetsoft\widgets\former\ZExportJsonBtnWidget;
use zetsoft\widgets\former\ZExportWidget;
use zetsoft\widgets\navigat\ZButtonWidget;

class Import extends ZFrame
{
    public function import(DynaImport $dynaImport)
    {
        $className = $this->bootFull($dynaImport->className);
        $model = new $className;
        foreach ($dynaImport->excel as $item) {
            $inputFileName = Root . "/upload/uploaz/market/DynaImport/excel/{$dynaImport->id}/{$item}";

            /**  Identify the type of $inputFileName  **/
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $spreadsheet = $reader->load($inputFileName);
                $schdeules = $spreadsheet->getActiveSheet()->toArray();
                foreach ($schdeules as $key => $value) {
                    $success = false;
                    foreach ($value as $iter => $column_value) {
                        if ($column_value !== null && $key !== 0 && $key !== 1 && $schdeules[0][$iter] !== null && $iter !== 0) {
                            $column = $schdeules[0][$iter];
                            $model->$column = $column_value;
                            $success = true;
                        }
                    };
                    if ($success) {
                        $model->configs->rules = validatorSafe;
                        $model->columns();
                        $model->save();
                    }
                };

            } catch (\Exception $exception) {
                print_r($exception->getMessage() . $exception->getFile());
                exit();
            }
        }

    }

}
