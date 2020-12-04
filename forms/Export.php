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

class Export extends ZFrame
{
    public $all = true;
    public $columnSelector = true;
    public $search = true;
    public $filename = 'Full export';
    public $label = null;
    public $alertBeforExportSaving = '';

    public $selectedColumns = null;
    public $noExportColumns = [];
    public $emptyText = 'Data not found';
    public $array = false;
    public $model = null;

    public $hidden = false;
    public $type = 'model';
    public $provider = null;
    public $checkKeys = [];

    public $action = null;

    public $export = [];
    public $exportAdds = [];

    public function run($model, $data = null)
    {
        if ($model === null)
            return null;

        if ($this->label === null)
            $this->label = Az::l('Экспорт');

        if ($data === null)
            $provider = $model->search();
        else
            $provider = $model->searchForm($data);

        $provider->pagination = false;

        $columns = $model->columnsList([dbTypeJsonb]);

        return ZExportMenu::widget([
            'dataProvider' => $provider,
            'columns' => $columns,
        ]);

    }


    public function getProviderModels()
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

    //start | DavlatovRavshan | 10.10.2020
    public function dynaExport($widget)
    {

        if (!$widget->_config['isExport'])
            return null;

        //start|MurodovMirbosit|17.10.2020
        if ($widget->_config['jsonIrina']) {

            $exportItem = new ExportItem();
            $exportItem->title = 'JSON IRINA';
            $exportItem->icon = 'text-warning far fa-file-code';
            $exportItem->url = '/api/core/files/export';
            $exportItem->method = 'formToJsonIra';
            $exportItem->args = true;

            $this->export[] = $exportItem;
        }

        //end|MurodovMirbosit|17.10.2020
        if (!$widget->_config['jsonIrina']) {
            //start: MurodovMirbosit 10.10.2020
            $exportItem = new ExportItem();
            $exportItem->title = 'JSON';
            $exportItem->icon = 'text-warning far fa-file-code';
            $exportItem->url = '/api/core/files/export';
            $exportItem->method = 'modelsToJson';
            //$exportItem->args = true;

            $this->export[] = $exportItem;
            //end
        }

        $exportItem = new ExportItem();
        $exportItem->title = 'EXCEL';
        $exportItem->icon = 'text-success far fa-file-excel';
        $exportItem->url = '/api/core/files/excel';
        $exportItem->method = 'formToExcel';

        $this->export[] = $exportItem;

        return ZExportWidget::widget([
            'id' => $widget->modelClassName . '-export',
            'model' => $widget->model,
            'data' => ZArrayHelper::merge($this->export, $widget->_config['export']),

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
    //end | DavlatovRavshan | 10.10.2020

    //start: MurodovMirbosit 11.10.2020
    public function getSourceByUserCompany()
    {

        $sources = UserCompany::findOne(['type' => 'source']);

        $company_source = [];
        foreach ($sources as $source) {
            $order_sources = ShopOrder::findAll(['source' => $source->id]);
            foreach ($order_sources as $order_source) {
                $company_source = count($order_source);
            }
        }

        return $company_source;

    }
    //end

    //start|MurodovMirbosit|10.10.2020
    public function formToJson()
    {
        $data = ZArrayHelper::getValue($this->provider, 'allModels');

        $columns = ZArrayHelper::getValue($this->provider, 'columns');

        $result = [];
        $models = [];

        foreach ($data as $attributes) {

            $newAttributes = [];
            foreach ($attributes as $key => $value) {

                if (ZArrayHelper::keyExists($key, $columns)) {

                    $column = $columns[$key];

                    $title = ZArrayHelper::getValue($column, 'title');
                    $newAttributes[$title] = $value;
                }
            }

            $models[] = $newAttributes;
        }

        $result[$this->model->configs->title] = $models;

        $json_data = json_encode($result, JSON_UNESCAPED_UNICODE);

        $now = date('d.m.Y_H-i-s');

        $filePath = Root . '/upload/uploaz/market/json_temp/' . "ZDynamicModel-$now" . '.json';

        file_put_contents($filePath, $json_data);

        return $filePath;
    }
    //end|MurodovMirbosit|10.10.2020

    //start|MurodovMirbosit|17.10.2020
    public function formToJsonIra()
    {
        $data = ZArrayHelper::getValue($this->provider, 'allModels');
        $columns = ZArrayHelper::getValue($this->provider, 'columns');

        $result = [];
        $models = [];

        foreach ($data as $attributes) {
            $newAttributes = [];
            foreach ($attributes as $key => $value) {

                if (ZArrayHelper::keyExists($key, $columns)) {

                    $column = $columns[$key];

                    $title = ZArrayHelper::getValue($column, 'title');
                    $newAttributes[$title] = $value;

                    $courier = ShopCourier::findOne([
                        'name' => $attributes['gtd4']
                    ]);

                    if ($courier === null) {
                        $courier = null;
                    }

                    $accept_GUID = Az::$app->guid->sGuid->createLowercase();
                    $courier_GUID = Az::$app->guid->sGuid->createLowercase();

                    $newAttributes['Курьер_GUID'] = $courier_GUID;
                    $newAttributes['Курьер_Код'] = $courier->id;
                    $newAttributes['УникальныйИдентификатор'] = $accept_GUID;
                    $newAttributes['КоличествоВыпПоПроектам'] = $this->getSourceByUserCompany();
                }
            }

            $models[] = $newAttributes;
        }

        $result['Приемки'] = $models;

        $json_data = json_encode($result, JSON_UNESCAPED_UNICODE);

        $now = date('d.m.Y_H-i-s');

        $filePath = Root . '/upload/uploaz/market/json_temp/' . "ZDynamicModel-$now" . '.json';

        file_put_contents($filePath, $json_data);

        return $filePath;
    }

    //end

    public function modelsToJson($for1C = false)
    {
        //start: MurodovMirbosit 10.10.2020
        if ($this->type === 'form') {
            return $this->formToJson();
        }
        //end | MurodovMirbosit | 10.10.2020

        $data = $this->getProviderModels();
        $model = $this->model;

        $result = [];

        $checkKeys = ZFormatter::filterValue($this->checkKeys);

        $titles = [];
        foreach ($model->columns as $key => $column) {
            $titles[$key] = $column->title;
        }

        $models = [];
        foreach ($data as $attributes) {

            $id = ZArrayHelper::getValue($attributes, 'id');
            if (!empty($checkKeys)) {
                if (!ZArrayHelper::isIn($id, $checkKeys))
                    continue;
            }

            $newAttributes = [];
            foreach ($attributes as $key => $value) {
                if (ZArrayHelper::keyExists($key, $titles))
                    $newAttributes[$titles[$key]] = $value;
            }

            $models[] = $newAttributes;

        }

        if (!$for1C)
            $result[$model->configs->title] = $models;
        else
            $result[$model->configs->title][] = $models;

        $json_data = json_encode($result, JSON_UNESCAPED_UNICODE);

        $now = date('d.m.Y_H-i-s');
        $filePath = Root . '/upload/uploaz/market/json_temp/' . "$model->className-$now" . '.json';

        file_put_contents($filePath, $json_data);

        return $filePath;


    }


}
