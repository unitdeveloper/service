<?php

namespace zetsoft\service\inputs;


use yii\web\Response;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\get;
use function PHPUnit\Framework\returnArgument;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class DepDrops extends ZFrame
{

    public function ajax($args)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $select = $this->httpPost('depdrop_parents');
        $selected_id = $select[0];

        $model_url = $args['model'];
        if(isset($args['get']))
            $model_params = $args['get'];


        if($model_url[0] != "\\")
            $model_url = "\\".$model_url;

        echo $model_url."<br>";
        echo $args['key']."<br>";
        echo $args['value']."<br>";
        echo $args['value']."<br>";



        $out = [];
        if(isset($args['select_by'])) {
            $data = $model_url::find()
                ->select([$args['key'], $args['value']])
                ->where([$args['select_by'] => $selected_id])
                ->all();
            $out = $this->getData($data);
        }
        else
        {
            if (isset($selected_id)) {
                $atributes = \zetsoft\models\page\PageAction::find()->one()->attributes;
                $out = $this->getDbData($atributes);
            }
        }
        return $out;

        $models = $model_url::find()->find()->where(['page_module_id' => $selected_id])->all();
        echo "<pre>";
            print_r($models);
        echo "</pre>";
        die;

        if($model == 'control') {
            if (isset($selected_id)) {
                $controls = \zetsoft\models\page\PageControl::find()
                    ->select(['id', 'name'])
                    ->where(['page_module_id' => $selected_id])
                    ->all();

                $out = $this->getData($controls);
            }
        }
        else if($model == paramAction && isset($model_params) && $model_params == 'attributes')
        {
            if(isset($selected_id)) {
                $atributes = \zetsoft\models\page\PageAction::find()
                    ->where(['page_control_id' => $selected_id])
                    ->one()
                    ->attributes;
                $out = $this->getDbData($atributes);
            }
        }
        elseif ($model == paramAction)
        {
            if (isset($selected_id)) {
                $actions = \zetsoft\models\page\PageAction::find()
                    ->select(['id', 'name'])
                    ->where(['page_control_id' => $selected_id])
                    ->all();

                $out = $this->getData($actions);
            }
        }

        return [
            'output' => $out,
        ];
    }

    public function run($aa, $bb)
    {

        vd($aa);
        vd($bb);
    }


    public function getData($models)
    {
        $out = [];
        foreach ($models as $model) {
            array_push($out, [
                'id' => $model->id,
                'name' => $model->name,
            ]);
        }
        return $out;
    }

    public function getDbData($models)
    {
        $out = [];
        foreach ($models as $key => $value) {
            array_push($out, [
                'id' => $key,
                'name' => $key,
            ]);
        }
        return $out;
    }
}
