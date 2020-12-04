<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;


use Fusonic\Linq\Linq;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Form;
use zetsoft\former\dyna\DynaFilterForm;
use zetsoft\former\eyuf\EyufCompletedForm;
use zetsoft\former\eyuf\EyufProgramForm;
use zetsoft\former\shop\ShopDailyReportForm;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\user\User;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZTableWrapWidget;

class Main extends ZFrame
{
    //// not working
    public function getData()
    {
        $users = User::find()
            ->where([
                'name' => 'Jahongir'
            ])
            ->all();

        $return = [];

        foreach ($users as $user) {
            $return[] = [
                'name' => $user->name,
                'pass' => $user->password,
            ];
        }

        return $return;
    }////****

    //// working
    public function table()
    {
        $table = [];
        $data = $this->statbycounty();

        //$query = new ZArrayQuery();
        ///$query->from( $data );

        foreach ($data as $key => $item) {

            $table[$key] = ZTableWrapWidget::widget([
                'data' => $item
            ]);
        }
        return $table;
    }/////****

    ////  working               ////
    public function countsbycounty()
    {
        $countries = PlaceCountry::find()->all();

        foreach ($countries as $key => $value) {

            $countA = EyufScholar::find()
                ->where(['place_country_id' => $value->id])
                ->andWhere([
                    'program' => EyufScholar::program
                ])
                ->count();

            $arrmain[$value->alpha2] = $countA;
        }
        return $arrmain;
    } ////****

    /// working
    public function statbycounty()
    {
        $countries = PlaceCountry::find()
            ->asArray()
            ->all();

        $arrtest = [];

        foreach ($countries as $key => $value) {

            foreach (EyufScholar::program as $val) {

                $alfa = $value['alpha2'];

                $countA = EyufScholar::find()
                    ->where(['place_country_id' => $value['id']])
                    ->andWhere(['program' => $val])
                    ->count();

                $countB = EyufScholar::find()
                    ->where(['place_country_id' => $value['id']])
                    ->andWhere(['program' => $val])
                    ->andWhere(['completed' => 't'])
                    ->count();

                $countC = $countA - $countB;
                $arrtest[$alfa][$val]['name'] = $val;
                $arrtest[$alfa][$val]['bitirgan'] = $countB;
                $arrtest[$alfa][$val]['bitirmagan'] = $countC;
                $arrtest[$alfa][$val]['obshi'] = $countA;


            }


        }

        //    vdd($arrtest['DJ']);
        return $arrtest;
    } ////****

    //// working
    public function card()
    {
        $countries = PlaceCountry::find()->all();

        foreach ($countries as $key1 => $val1) {

            $arrMain[$key1][0] = $val1->name;

            foreach (EyufScholar::program as $val2) {

                $countA = EyufScholar::find()
                    ->where(['place_country_id' => $val1->id])
                    ->andWhere(['program' => $val2])
                    ->count();
                $arrMain[$key1] = $countA;
            }
        }
        return $arrMain;
    }////****


    public function EyufScholarSave($model)
    {

        $EyufScholar = new EyufScholar();
        $EyufScholar->user_id = $model->id;

        $EyufScholar->phone = $model->phone;
        $EyufScholar->name = $model->name;
        $EyufScholar->email = $model->email;

        if ($EyufScholar->save())
            return true;

        return false;
    }

    /**
     *
     * Function  userSave
     * @param EyufScholar $model
     * @return  bool
     */

    public function userSave($model)
    {


        return false;
    }


    public function mailer()
    {

        $whom = "sunnat.programmer@gmail.com";  // test

        $send = Az::$app->mailer->compose()
            ->setTo($whom)
            ->setSubject('Тема сообщения')
            ->setTextBody('Текст сообщения')
            ->setHtmlBody('<b> nimadir </b>')
            ->send();
    }

    public function formByCountriesSosya($model)
    {

        $data = [];

        $EyufScholars = EyufScholar::find()->all();
        $countries = PlaceCountry::find()->all();

        $programs = $model->columnsList();

        $id = 1;
        foreach ($countries as $cID => $country) {

            /** @var EyufProgramForm $form */
            $form = new EyufProgramForm();
            //$form = clone $model;
            $form->country = $country->name;
            /*$form->id = $id;*/
            $id++;
            $all = 0;
            foreach ($programs as $program) {
                $myEyufScholars = Linq::from($EyufScholars)
                    ->where(function (EyufScholar $EyufScholar) use ($country, $program) {

                        $one = $EyufScholar->place_country_id === $country->id;
                        $two = $EyufScholar->program === $program;
                        if ($one && $two)
                            return true;

                        return false;
                    })
                    ->toArray();

                $cn = count($myEyufScholars);
                if ($cn > 0) {
                    $form->$program = $cn;
                    $all += $cn;
                } else {
                    if ($program !== 'country' && $program !== 'intern') {
                        $form->$program = 0;
                    }
                    if ($program === 'all') {
                        $form->$program = $all;
                    }
                    continue;
                }

                $data[$cID] = $form;
            }
        }

        return $data;
    }

    //start|MurodovMirbosit|17.10.2020
    public function formByCountriesOld()
    {
        $eyufScholars = EyufScholar::find()->asArray()->all();
        $forms = [];

        /** @var EyufScholar $eyufScholar */
        foreach ($eyufScholars as $eyufScholar) {
            $countries = PlaceCountry::find()
                ->where([
                    'id' => $eyufScholar['place_country_id']
                ])->asArray()->all();

            foreach ($countries as $country) {
                $form = new EyufProgramForm();
                $form->country = $country['name'] ?: 'Не задано';
                $form->masters = $eyufScholar['program'] ?: 'Не задано';
                $form->doctors = $eyufScholar['program'] ?: 'Не задано';
                $form->qualify = $eyufScholar['program'] ?: 'Не задано';
                $form->intern = $eyufScholar['program'] ?: 'Не задано';
                $form->all = count($eyufScholar) ?: 'Не задано';
                $forms[] = $form;
            }
        }
        return $forms;
    }

    public function formByCountries($model)
    {

        $forms = [];
        $all = 0;
        /** @var EyufScholar $eyufScholar */
        $countries = PlaceCountry::find()->all();
        foreach ($countries as $country) {

            $scholar = EyufScholar::find()
                ->where([
                    'place_country_id' => $country->id
                ]);

            $form = new EyufProgramForm();

            $form->country = $country->name;

            $form->masters = $scholar
                ->andWhere([
                    'program' => 'masters'
                ])
                ->count();

            $form->doctors = $scholar
                ->andWhere([
                    'program' => 'doctors'
                ])
                ->count();

            $form->qualify = $scholar
                ->andWhere([
                    'program' => 'qualify'
                ])
                ->count();

            $form->intern = $scholar
                ->andWhere([
                    'program' => 'intern'
                ])
                ->count();

            $form->all = (int)$form->masters + (int)$form->doctors + (int)$form->qualify + (int)$form->intern;
            if (empty($form->all)) {
                continue;
            }
            $forms[] = $form;

        }

        return $forms;
    }

    //end|MurodovMirbosit|17.10.2020


    public function getNames()
    {
        $countries = PlaceCountry::find()
            ->all();

        $data = [];

        foreach ($countries as $country) {
            $key = $country->alpha2;
            $value = $country->name;
            $data[$key] = $value;
        }

        return $data;
    }
}


