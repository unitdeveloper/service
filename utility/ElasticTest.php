<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;

use zetsoft\models\App\eyuf\TestElastic;

class ElasticTest
{
    public function runTest()
    {
        for ($i = 0; $i < 10000; $i++) {
            $model = new TestElastic();
            $model->name = $this->generateRandomString(5);
            $model->surname = $this->generateRandomString(8);
            $model->text = $this->generateRandomString(15);
            $model->save();
        }
    }

    public function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    public function deleteAll()
    {
        $model = TestElastic::deleteAll();
        echo 'Deleted';
    }
}
