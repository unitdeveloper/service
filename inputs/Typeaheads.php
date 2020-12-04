<?php

namespace zetsoft\service\inputs;


use yii\helpers\ArrayHelper;
use yii\web\Response;
use zetsoft\models\core\CoreInput;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function Dash\Curry\get;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class Typeaheads extends ZFrame
{

    public function init()
    {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function ajax($model, $column, $query)
    {
        if ($model[0] != "\\")
            $model = "\\" . $model;

        $data = $model::find()
            ->select([$column])
            ->where(['like', $column , $query])
            ->all();
        $result = ArrayHelper::getColumn($data,$column);

        $out = array();
        foreach ($result as $d) {
            $o['value'] = $d;
            $out[] = $o;
        }
        return $out;
    }


    

    public function run($aa, $bb)
    {

        vd($aa);
        vd($bb);
    }

    public function savePlace($place_name , $LangLt) {

        $model = CoreInput::find()->where(["id" => 10])->one();
        $all_LangLt = $model->jsonb_9;
        array_push($all_LangLt,$LangLt);
        $model->jsonb_9 = $all_LangLt;
        $model->save();
    }

    public function select2($model, $column, $query = "")
    {
        if ($model[0] != "\\")
            $model = "\\" . $model;

        $data = $model::find()
            ->select(["id",$column])
            ->where(['like', $column , $query])
            ->all();

        $out = array();
        foreach ($data as $d) {
            $o['id'] = $d->id;
            $o['text'] = $d->email;
            $out[] = $o;
        }
        return [
            "results" => $out
        ];
    }

    public function select2Data($model, $column, $selected_id = null)
    {
        if ($model[0] != "\\")
            $model = "\\" . $model;

        $data = $model::find()
            ->select(["id",$column])
            ->where(['id' => $selected_id])
            ->one();

        $out = array();
        $o['id'] = $data->id;
        $o['text'] = $data->email;
        $out[] = $o;
        return [
            "results" => $out
        ];
    }
}
