<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\select;

use zetsoft\dbitem\data\FormDb;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class Select2Xolmat extends ZFrame
{

    private function getRelatedClass($attribute, $columns)
    {

        switch (true) {

            case !empty($columns[$attribute]->fkTable):
                $relatedTable = $columns[$attribute]->fkTable;
                break;

            case  ZStringHelper::endsWith($attribute, '_id'):
            case  ZStringHelper::endsWith($attribute, '_ids'):
                $relatedTable = str_replace(['_ids', '_id'], '', $attribute);
                break;

            default:
                $relatedTable = null;
                break;
        }
        return ZInflector::camelize($relatedTable);
    }


    private function data($columnData)
    {
        $data = [];
        foreach ($columnData as $key => $value) {
            $data[] = [
                'id' => $key,
                'text' => $value,
            ];
        }
        return $data;
    }

                    
    public function run()
    {
        $result = [];

        $page = $this->getPage();
        $modelClassname = $this->httpGet('modelClassName');

        $attribute = $this->httpGet('attribute');
        $modelId = $this->httpGet('modelId');
        $search = $this->httpGet('term');

        $modelClass = $this->bootFull($modelClassname);

        $model = new $modelClass();
        if (!empty($modelId))
            $model = $modelClass::findOne($modelId);

        $columns = $model->columns;
        $column = $model->columns[$attribute];

        if (!empty($column->data)) {
            switch (true) {
                case is_callable($column->data):
                    $result = $column->data;
                    $elements = $this->data($result());
                    break;
                case is_array($column->data):
                    $elements = $this->data($column->data);
                    break;
                case class_exists($column->data):
                    $data = new $column->data;
                    $elements = $this->data($data->lang());
                    break;
                default:
                    return [];
            }

            $elements = zcollect($elements);
            if (!empty($search))
                $elements = $elements->like('text', $search);

            $limit = $this->getLimit();

            if ($elements->count()) {
                $elements = $elements->paginate($limit, 'page', $page, $elements->count())->toArray();
                $result = $elements['data'];
            }

        } else {

            /** @var ZActiveRecord $relatedClass */
            $relatedClass = $this->getRelatedClass($attribute, $columns);

            $relatedClass = $this->bootFull($relatedClass);

            if ($relatedClass === null)
                return [];

            /** @var Models $relatedClass */
            $Q = $relatedClass::find();

            /** @var FormDb $column */
            if (!empty($column->fkQuery)) {
                $Q->where($column->fkQuery);
            }

            if (!empty($column->fkOrQuery)) {
                $Q->orWhere($column->fkOrQuery);
            }

            if (!empty($column->fkAndQuery)) {
                $Q->andWhere($column->fkAndQuery);
            }

            $elements = $Q
                ->asArray()
                ->all();

            $count = $Q
                ->count();

            if (!empty($search)) {
                $attr = (new $relatedClass())->configs->name;
                $elements = $Q
                    ->andWhere(['like', $attr, $search])
                    ->asArray()
                    ->all();

                $count = $Q
                    ->andWhere(['like', $attr, $search])
                    ->count();

                if (empty($elements))
                    return [];

            }
            $limit = $this->getLimit();

            $result = zcollect($elements)
                ->paginate($limit, 'page', $page, $count)
                ->toArray();

            $result = ZArrayHelper::createArray($result['data'], 'id', 'name');
        }

        $out['results'] = $result;
        
        $out['pagination'] = [
            'more' => true
        ];
        
        if (empty($result)) {
            $out['pagination'] = [
                'more' => false
            ];
        }
        return $out;
    }


    private function getPage()
    {
    
        $page = $this->httpGet('page');
        $page = intval($page);
        if (!$page)
            $page = 1;

        return $page;

    }


    private function getLimit()
    {
        switch (true) {

            case !empty($this->httpGet('limit')):
                $limit = $this->httpGet('limit');
                break;

            default:
                $limit = 5;
                break;

        }
        
        return $limit;
    }
}
