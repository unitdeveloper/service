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
use
    kartik\form\ActiveForm;
use Yii;
use zetsoft\dbdata\App\eyuf\RoleData;
use zetsoft\former\eyuf\EyufCompletedForm;
use zetsoft\former\eyuf\EyufProgramForm;
use zetsoft\models\App\eyuf\EyufReport;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\user\User;

use zetsoft\models\App\eyuf\EyufDocumentType;
use zetsoft\system\actives\ZArrayQuery;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\ZTableWrapWidget;
use zetsoft\widgets\inputes\ZFileInputWidget;
use kartik\widgets\Growl;
use yii\web\Response;

class Scholar extends ZFrame
{
    public function test()
    {
        echo 'test';
    }

    public function getId()
    {

        $scholar = EyufScholar::find()
            ->where([
                'user_id' => $this->userIdentity()->id
            ])
            ->one();

        if ($scholar === null)
            return null;

        return $scholar->id;
    }

    public function docList()
    {
        /** @var EyufScholar $scholar */
        /*        $scholar = EyufScholar::findOne([
                    'user_id' => $this->userIdentity()->id
                ]);


                $docs = $scholar->getDocuments();

                $docTypes = DocumentType::findAll([
                    'program' => $scholar->program
                ]);*/
        /*
                foreach ($docs as $doc) {

                }*/

        $scholars = $this->getScholars();

        $return = [];

        foreach ($scholars as $scholar) {
            $return[] = [
                'name' => $scholar->program,
                'user' => $scholar->user_id,
            ];
        }

        return $return;
    }


    private function getScholars()
    {
        $allScholar = EyufScholar::find()->all();
        return $allScholar;
    }

    public function getAge($birthDate)
    {
        if (empty($birthDate)) return null;
        $birthDate = explode("-", $birthDate);
        if (empty($birthDate[0]) || empty($birthDate[1]) || empty($birthDate[2])) return null;
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));
        if ($age < 0) $age = 0;
        return $age;
    }

    public function invoiceStatus($model = null)
    {
        if (empty($model)) return true;
        foreach ($model->status as $value) {
            if (!($value)) return true;
        }
        return false;
    }

    public function getMainUrl()
    {
        $user = $this->userIdentity();
//        $role = $user->userRole();

//        switch ($role) {
//            case 'scholar':
//                $url = $this->bootEnv('urlScholarIndex');
//                return $url;
//                break;
//
//            case 'user':
//            case 'user':
//                return "/cores/main/index";
//                break;
//
//            default;
//                return "/logics/$role/index";
//        }
    }

    /**
     *
     * Function  sendNotify
     * send notify when change status
     * @param $model
     * @return  bool|void
     */

    //start|JakhongirKudratov|2020-10-27

    public function sendNotify($model)
    {
        $title = Az::l('Информация');
        $data = Az::l('Ваш статус изменился на {status}', [
            'status' => $model->status
        ]);
        $scholar_id = $model->user_id;
        $this->notifyInfo($title, $data, $scholar_id);

        $scholar = new EyufScholar();

        $data = Az::l('Статус стипендианта {title} изменился на {status}', [
            'status' => $scholar->_status[$model->status],
            'title' => $model->title
        ]);

        $this->notifyInfo($title, $data, RoleData::admin);
        $this->notifyInfo($title, $data, RoleData::dev);


        switch ($model->status) {


            case EyufScholar::status['docReady']:
                $data = Az::l('Есть новый стипендиант');

                if (!empty($model->edu_end)) {

                    if ($model->edu_end > date('Y-m-d')) {
                        if ($model->program === 'intern' || $model->program === 'qualify') {
                            $url = '/eyuf/logics/interqua/doc-list-accept.aspx' . "?id=$model->id";
                            $this->notifyInfo($title, $data, RoleData::interqua, $url);

                        } else {
                            $url = '/eyuf/logics/masdoc/doc-list-accept.aspx' . "?id=$model->id";
                            $this->notifyInfo($title, $data, RoleData::masdoc);
                        }
                    } else {
                        $url = '/eyuf/logics/monitor/doc-list-accept.aspx';
                        $this->notifyInfo($title, $data, RoleData::monitor);
                    }

                }
                break;

            case EyufScholar::status['stipend']:
                $this->notifyInfo(
                    Az::l('Информация'),
                    Az::l('Все ваши документы были приняты отделом мониторинга.'),
                    $scholar->user_id
                );

                $this->notifyInfo(
                    Az::l('Информация'),
                    Az::l('Документы стипентианта были приняты.'),
                    RoleData::masdoc
                );
                $this->notifyInfo(
                    Az::l('Информация'),
                    Az::l('Документы стипентианта были приняты.'),
                    RoleData::interqua
                );

                break;


            case EyufScholar::status['register']:
                $title = 'Информация';
                $data = 'Есть новый стипендиант.' . $model->title;
                $notify = $this->notifyInfo($title, $data, RoleData::admin);

                break;


        }


    }

    //end|JakhongirKudratov|2020-10-27

    /**
     *
     * Function  sendNotifyToAdmin
     * @param EyufScholar $model
     * @return  bool|null
     *
     * @author JakhongirKudratov
     */

    //start|JakhongirKudratov|2020-10-30

    public function sendNotifyToAdmin(EyufScholar $model)
    {
        if ($model->isNewRecord)
            return null;

        $oldAttrs = $model->oldAttributes;
        $attrs = $model->attributes;
        $changes = [];


        foreach ($attrs as $key => $value) {
            //     vd($key, $value);
            if (!ZArrayHelper::isIn($key, systemColumn))
                if ($value !== ZArrayHelper::getValue($oldAttrs, $key))
                    $changes[$key] = $value;

        }

        if (!empty($changes)) {

            $data = Az::l('Измененные атрибуты ') . $model->name . ' ';
            foreach ($changes as $key => $val) {
                $data .= Az::l($key . ' ' . $value . ' ,');
            }
            $title = Az::l('информация о студенте изменилась');

            $this->notifyInfo($title, $data, RoleData::admin);

            if (!empty($model->edu_end)) {
                if ($model->edu_end > date('Y-m-d')) {
                    if ($model->program === 'intern' || $model->program === 'qualify')
                        $this->notifyInfo($title, $data, RoleData::interqua);
                    else

                        $this->notifyInfo($title, $data, RoleData::masdoc);

                } else

                    $this->notifyInfo($title, $data, RoleData::monitor);

            }

            return true;

        }

        return null;
    }

    //end|JakhongirKudratov|2020-10-30


    public function sendNotifyPerMonth(EyufScholar $model)
    {
        $reports = EyufReport::find()
            ->where([
                'eyuf_scholar_id' => $model->id
            ])
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->one();

        //vdd(count($reports));
        $different = $this->getMonthDifferent($reports->created_at);
        if ($different >= 6) {
            $this->sendReportNotify($model->id);
            if (!empty($model->email)) {
                $this->sendReportNotifyToEmail($model->email);
            }
        }
        return true;
    }

    public function getMonthDifferent($last)
    {
        $last = new \DateTime($last);
        $today = date('Y-m-d');
        $today = new \DateTime($today);
        $diff = $today->diff($last);
        $months = $diff->y * 12 + $diff->m + $diff->d / 30;

        return (int)round($months);
    }

    public function sendReportNotify($id)
    {
        $title = 'Внимание';
        $data = 'Необходимо предоставить отчеты';

        $this->notifyInfo($title, $data, $id);
    }

    public function sendReportNotifyToEmail($email)
    {

        Az::$app->utility->swiftMailer->sendReportNotify($email);


    }

}


