<?php
/**
 * Class    Docto
 * @package zetsoft\service\office
 *
 * https://github.com/tobya/DocTo
 * @author UzakbaevAxmet
 * @author DilshodKhudoyarov
 * Class file formatlarni boshqa formatga convert qiladi
 */

namespace zetsoft\service\office;

use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

/**
 * Class    Import
 * @package zetsoft\service\office
 * @author Daho
 */
class Import extends ZFrame
{
    public $modelClass;
    public $attributes = [];

    /**
     *
     * Function  run
     * @throws \Exception
     * @author Daho
     */
    public function run()
    {
        $arr = [];
        $this->attributes = ZArrayHelper::getValue($arr, 0);

        foreach ($arr as $values) {
            if ($values === $this->attributes)
                continue;

            $this->createModel($values);
        }

    }

    /**
     *
     * Function  createModel
     * @param array $values
     * @throws \Exception
     * @author Daho
     */
    public function createModel($values)
    {
        $modelClassName = $this->bootFull($this->modelClass);
        /** @var ZActiveRecord $model */
        $model = new $modelClassName();
        $this->paramSet(paramFull, true);
        $this->paramSet(paramNoEvent, true);
        $model->columns();

        foreach ($this->attributes as $key => $attribute) {
            if (ZArrayHelper::keyExists($model->attributes, $attribute))
                $model->$attribute = ZArrayHelper::getValue($values, $key);
        }
        $model->configs->rules = validatorSafe;
        $model->save();
    }


}
