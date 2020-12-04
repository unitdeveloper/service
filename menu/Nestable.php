<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\menu;


use Fusonic\Linq\Linq;
use zetsoft\dbitem\ALL\ZAppItem;
use zetsoft\service\cores\Category;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Nestable extends ZFrame
{
    public $modelClassName;
    public $sort;
    public $parent;
    

    private function getChildren($parent, $models)
    {
        $children = [];
        $children_models = Linq::from($models)->where(fn($data) => $data[$this->parent] === $parent->id);
        if ($this->sort)
            $children_models = $children_models->orderBy(fn($data) => $data[$this->sort] );
        if ($children_models !== null) {
            foreach ($children_models as $children_model) {
                $a = [
                    "id" => $children_model['id'],
                    "content" => $children_model['name'],
                ];
                if ($this->getChildren($children_model, $models) != null)
                    $a["children"] = $this->getChildren($children_model, $models);
                $children[] = $a;
            }

            return $children;
        } else
            return null;
    }

    public function getVal($model, $parentAttr, $sortAttr)
    {
        $this->modelClassName = $model;
        $this->parent = $parentAttr;
        $this->sort = $sortAttr;

        $models = $model::find()->all();

        $parents = $model::find()->where([$parentAttr => null]);
        if ($sortAttr)
            $parents = $parents->orderBy([$sortAttr => SORT_ASC]);
        $parents = $parents->all();

        $json = [];
        if ($parents !== null)
            foreach ($parents as $parent) {
                $children = $this->getChildren($parent, $models);
                $a = [
                    'id' => $parent->id,
                    'content' => $parent->name,
                ];
                if ($children != null)
                    $a["children"] = $children;
                $json[] = $a;
            }
        return json_encode($json);
    }

    public function setNestable($data)
    {
        $parent = $this->parent;
        $sort = $this->sort;
        $result_nestable = json_decode($data);

        $i = 0;
        foreach ($result_nestable as $item) {
            $model = $this->modelClassName::findOne($item->id);
            if (property_exists($item, 'parent_id')) {
                $model->$parent = $item->parent_id;
            } else {
                $model->$parent = null;
            }
            if ($sort)
                $model->$sort = $i;
            Az::$app->params['sortCategory'] = true;
            $model->save();
            $i++;
        }
        return true;
    }

}
