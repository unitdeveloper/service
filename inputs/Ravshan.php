<?php

namespace zetsoft\service\inputs;


use yii\web\Response;
use zetsoft\models\page\PageBlocksType;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
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
class Ravshan extends ZFrame
{
    public $select;
    public $selectedId;

    public function init()
    {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $this->select = $this->httpPost('depdrop_parents');
        $this->selectedId = $this->select[0];
    }

    #region Result

    public const type = [
        'model' => 'model',
        'form' => 'form',
        'file' => 'file',
    ];

    private function checkDrop($key, $item, $type)
    {

        $out = [];
        switch ($type) {

            case self::type['form']:
                $out[] = [
                    'id' => $key,
                    'name' => $key
                ];

                break;

            case self::type['file']:

                $baseName = str_replace('.php', '', bname($item));
                $out[] = [
                    'id' => $baseName,
                    'name' => $baseName,
                ];

                break;

            default:

                $out[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                ];

                break;


        }

        return $out;

    }

    #endregion


    public function sample($model, $category)
    {

        $block_type = PageBlocksType::findOne((int)$this->selectedId);
        $root = Root . "/blocks/$block_type->name/azk";
        $data = ZFileHelper::scanFilesPHP($root);
        //return $this->result($data);

        return $this->result($data, self::type['file']);
    }


    private function result($data, $type = self::type['model'])
    {

        if (!$type) {
            $out = [];
            return $out;
        }

        $out = [];
        foreach ($data as $key => $item) {

            switch ($type) {

                case self::type['form']:
                    $out[] = [
                        'id' => $key,
                        'name' => $key
                    ];
                    break;

                case self::type['file']:

                    $baseName = str_replace('.php', '', bname($item));
                    $out[] = [
                        'id' => $baseName,
                        'name' => $baseName,
                    ];
                    break;

                default:

                    $out[] = [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                    break;
            }
            
        }

        return [
            'output' => $out,
        ];

    }


    public function getRelatedJson($model, $column)
    {

        $intVal = intval($this->selectedId);

        if ($intVal === 0)
            return null;


        if (!empty($this->selectedId)) {
                 
            $data = $model::find()
                ->select(['id', 'name'])
                ->where([
                    $column => $this->selectedId
                ])
                ->all();

            $output = $this->result($data, self::type['model']);

        } else {

            $data = $model::find()
                ->one()
                ->attributes;

            $output = $this->result($data, null);

        }

        return $output;

    }


    public function test($model, $json_column, $depend_model)
    {
        if ($this->selectedId === null)
            return null;
        if ($model[0] != "\\")
            $model = "\\" . $model;
        if ($depend_model[0] != "\\")
            $depend_model = "\\" . $depend_model;
        if (isset($this->selectedId)) {
            $object = $model::findOne($this->selectedId);
            $data = $depend_model::find()
                ->select(['id', 'name'])
                ->where([
                    'id' => $object->$json_column
                ])
                ->all();
            $output = $this->result($data, self::type['model']);
        } else {
            $atributes = $depend_model::find()
                ->one()
                ->attributes;
            $output = $this->result($atributes, self::type['form']);
        }
        return $output;
    }

    public function ajax($model, $select_by = null)
    {

        if ($this->selectedId === null)
            return null;

        if ($model[0] != "\\")
            $model = "\\" . $model;

        if (isset($this->selectedId) && $select_by != null) {
            $data = $model::find()
                ->select(['id', 'name'])
                ->where([$select_by => $this->selectedId])
                ->all();
            $output = $this->result($data, self::type['model']);

        } else {
            $atributes = $model::find()
                ->one()
                ->attributes;
            $output = $this->result($atributes, self::type['form']);
        }

        return $output;

    }


    public function map($place_id)
    {
        $place_json = file_get_contents("https://maps.googleapis.com/maps/api/place/details/json?fields=name,photos,rating,formatted_phone_number&key=AIzaSyBkxS5l87lclaC6MIWSGejdCXL13wSShRo&place_id=" . $place_id);
        $place = json_decode($place_json);

        if (isset($place->result->photos[0])) {
            $image = "https://maps.googleapis.com/maps/api/place/photo?key=AIzaSyBkxS5l87lclaC6MIWSGejdCXL13wSShRo&maxwidth=480";
            $image = $image . "&photoreference=" . $place->result->photos[0]->photo_reference;
//&photoreference=CnRtAAAATLZNl354RwP_9UKbQ_5Psy40texXePv4oAlgP4qNEkdIrkyse7rPXYGd9D_Uj1rVsQdWT4oRz4QrYAJNpFX7rzqqMlZw2h2E2y5IKMUZ7ouD_SlcHxYq1yL4KbKUv3qtWgTK0A6QbGh87GB3sscrHRIQiG2RrmU_jF4tENr9wGS_YxoUSSDrYjWmrNfeEHSGSc3FyhNLlBU";

        } else {
            $image = "https://www.salonlfc.com/wp-content/uploads/2018/01/image-not-found-scaled-1150x647.png";
        }

        return $image;
    }
}
