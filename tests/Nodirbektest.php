<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\tests;


use zetsoft\models\shop\ShopCatalog;
use zetsoft\models\test\TestDep;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function Dash\where;

class Nodirbektest extends ZFrame
{
    public function actionRun()
    {
        Az::$app->tests->activeQuery->test();
}



    public function insertStatus()
    {
        $array = [
            "Обзвон",
            "Одобрен",
            "Отказ",
            "Не заказывал",
            "Дубль",
            "Некорректный",
            "Выкуп",
            "На исполнении",
            "Новый",
            "Готов к отгрузке",
            "Передан в подотчёт",
            "Отказ во время доставки",
            "Проверка",
            "Перенос дата доставки",
            "Аннулирован",
            "Оплачен частично",
            "Возврат частично",
            "Отменен",
            "Не комплект",
            "К назначению курьера",
            "На комплектации",
            "В ожидании комплектации",
        ];

        foreach ($array as $item) {
            $model = new ShopStatus();
            $model->name = $item;
            $model->save();
        }
    }

    public function link()
    {
        $value = [
            "page_module_id" => "19",
            "core_control_id" => "301",
            "page_action_id" => "707",
            "name" => "cccc",
        ];

        $attr = 'form';
        $model = TestDep::findOne(4);

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $model;
        Az::$app->forms->wiData->attribute = $attr;

        $value = Az::$app->forms->wiData->value();
        vdd($value);

    }


    public function test()
    {
        $catalogs = ShopCatalog::find()->where("offer <@ '{\"offer\": \"super_offer\"}'")->all();
        vdd($catalogs);
    }

    private function isTelNumber(&$value)
    {
        $value = str_replace(array('+', '-', ' '), '', $value);
        return is_numeric($value);
    }

    public function cat3()
    {
        /* $a = exec('cmd /c ping mail.ru -t');*/
        /*       shell_exec('cmd /c ping mail.ru -t');
               passthru('cmd /c ping mail.ru -t');
               exec('cmd /c ping mail.ru -t');*/

    }

    public function cat2()
    {

        /*  $item = new ServiceItem();
          $item->namespace = 'reacts';
          $item->service = 'ChildProcess';
          $item->method = 'runCommand';

          $item->args = 'cmd /c ping mail.ru -t';

          echo Az::$app->utility->execs->service($item);*/


//        $item = new ServiceItem();
//        $item->App = true;
//        $item->service = 'Davlat';
//        $item->method = 'test';
//
//        $item->args = ['asdfasdf'];
//
//        echo Az::$app->utility->execs->service($item);


    }

    public function cat()
    {

        /*
                $mains = CoreCategoryOne::find()
                    ->where([
                        'child' => []
                    ])
                    ->all();*/


        /*
                $childs = CoreCategoryOne::find()
                    ->where([
                        'id' => $category->child
                    ])
                    ->all();*/


        /*  vd($mains);*/

    }


}
