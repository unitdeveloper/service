<?php
/**
 * Author:  Xolmat Ravshanov
 * Date: 31.05.2020
 *
 */
namespace zetsoft\service\search;

use zetsoft\service\smart\Model;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


class Elastic extends ZFrame
{
    public function index()
    {
        $classes = Az::$app->smart->migra->scan();
        foreach ($classes as $class) {
            $object1 = new $class();
            if($object1->configs->indexSearch){
                /** @var Models $object */
                $object = $class::find()->all();
                Az::$app->search->elasticsearch->model = $class;
                // check if index exits
                if (!Az::$app->search->elasticsearch->indexExists())
                    Az::$app->search->elasticsearch->createIndex();
                //set index
                $arr = [];
                /** @var Model $model */
                foreach ($object as $model) {
                    foreach ($model->columns as $key => $column){
                        if ($column->indexSearch)
                            $arr[$key] = $model->$key;
                    }
                    Az::$app->search->elasticsearch->body = $arr;
                    Az::$app->search->elasticsearch->createdoc($model->id);
                }
            }else{
                continue;
            }
       }
   }



   
   
}

