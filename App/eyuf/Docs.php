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


use Codeception\PHPUnit\ResultPrinter\HTML;
use Dompdf\Dompdf;
use VsWord;
use zetsoft\dbdata\App\eyuf\RoleData;
use zetsoft\models\App\eyuf\EyufDocument;
use zetsoft\models\App\eyuf\EyufDocumentType;
use zetsoft\models\App\eyuf\EyufInvoice;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\user\UserCompany;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use function False\true;


class Docs extends ZFrame
{


    public function toWordByPhpword($attributes)
    {

    }


    public function cores($id)
    {

        $model = EyufScholar::findOne($id);
        $data = [
            'birthdate' => $model->birthdate,
            'nation' => '',//$model->nation,      ?
            'program' => $model->program,
            'degree' => '', //$model->speciality,    ?
            'speciality' => $model->speciality,
            'languages' => $model->getUser()->lang,
            'is_deputy' => '', // $model->    ?
            'address' => $model->address,
            'edu_end' => $model->edu_end,
            'military_cert' => '',
            'nominations' => '',
            'photo' => $model->getUser()->userPhoto(),
            'job' => $model->getUserCompany()->name,
            'job_start' => '',
            'job_end' => '',

            'user_id' => $model->user_id,
        ];

        // Load the template processor
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(Root . '/binary/words/CW.docx');

        $templateProcessor->setValues($data);


        $templateProcessor->setImageValue('photo',
            [
                'path' => Root . '/execut/web/eyuf/upload/CoreUser/photo/' . $data['user_id'] . $data['photo'],
                'width' => 130,
                'height' => 180,
                'ratio' => false,
            ]);

        $templateProcessor->saveAs(Root . "/renders/_output/file_" . date('Y-m-d_H-i-s') . ".docx");;
    }

    public function htmlToWordByVench($html)
    {
        //var_dump($html);
        //exit;
        $doc = new VsWord();
        $parser = new \HtmlParser($doc);
        //$parser->parse($html);
        $parser->parse('<h1>Hello world!</h1>');

        $doc->saveAs("../../../renders/_output/file_" . date('Y-m-d_H-i-s') . ".docx");
    }

    /**
     * converts
     * Function  htmlToPdf
     * @param $html
     */
    public function htmlToPdf($html)
    {
        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();


        // Output the generated PDF to Browser
        // $data = $dompdf->stream();
        $output = $dompdf->output();

        file_put_contents("../../../renders/_output/file_" . date('Y-m-d_H-i-s') . ".pdf", $output);

    }

    public function mySave($model)
    {
        $country = new UserCompany();
        $country->name = $model->name;

        $country->title = (string)$model->name;
        $country->email = (string)$model->name;

        if ($country->save())
            return true;
        else {
            $this->alertDanger($country->errors, 'Error');
            return false;
        }
    }


    public function invoice($id)
    {
        /** @var EyufInvoice $model */

        $model = EyufInvoice::find()
            ->where(['eyuf_scholar_id' => $id])->all();

        if (empty($model))
            return true;

        foreach ($model as $value) {
            if (!($value->status)) return true;
        }

        return false;

    }


    #region Status

    /**
     *
     * Function  changeStatus
     * scholar status change to docready
     * @param $model
     * @return  bool
     *
     */

    //start|JakhongirKudratov|2020-10-27

    public function changeDocReady($model)
    {

        $scholar_id = $model->eyuf_scholar_id;

        if (empty($scholar_id))
            return null;

        $scholar = EyufScholar::findOne($scholar_id);

        if ($scholar === null)
            return false;

        $list = $this->getDocList($scholar->user_id);
        // vd($list);
        if (empty($list)) {

            $scholar->status = EyufScholar::status['docReady'];
            $this->notifyInfo('Информация', 'Ваши загружены', $scholar->user_id);

            if (ZArrayHelper::isIn($scholar->program, ['intern', 'qualify']))
                $this->notifyInfo('Информация', 'Документы нового стипендианта загружены: ' .$scholar->name, RoleData::interqua);
            else
                $this->notifyInfo('Информация', 'Ваши загружены', RoleData::masdoc);
        }


        $scholar->configs->rules = [
            [validatorSafe]
        ];
        $scholar->save();
        return true;
    }

    //end|JakhongirKudratov|2020-10-27

    //10
    public function testChangeStatus()
    {
        $model = EyufDocument::findOne(96);
        $this->changeDocReady($model);
    }

    /**
     *
     * Function  sendNotifyToModerator
     * send notify to moderator when need_verify is true
     * @param $model
     * @return  bool|void
     */

    //start|JakhongirKudratov|2020-10-27

    public function sendNotifyToModerator($model)
    {
        if ($model->need_verify) {
            $title = Az::l('Информация');
            $data = Az::l('Документ на подтверждение Модератору');
            $notify = $this->notifyInfo($title, $data, RoleData::moderator);


        }

        return false;
    }

//5ta
    //end|JakhongirKudratov|2020-10-27

    //start|JakhongirKudratov|2020-10-27

    /**
     *
     * Function  sendNotifyToMonitor
     * @param EyufDocument $model
     * @return  bool|void|null
     */
    public function sendNotifyToMonitor($model)
    {
        if ($model->verified) {
            $title = Az::l('Информация');
            $data = Az::l('Модератор подтвержден документа');
            //$notify = $this->notifyInfo($title, $data, RoleData::monitor);
            if (!empty($model->edu_end)) {
                if ($model->edu_end > date('Y-m-d')) {
                    if ($model->program === 'intern' || $model->program === 'qualify')
                        $notify = $this->notifyInfo($title, $data, RoleData::interqua);
                    else
                        $notify =  $this->notifyInfo($title, $data, RoleData::masdoc);

                } else
                    $notify =  $this->notifyInfo($title, $data, RoleData::monitor);
            }

        }

        $title = Az::l('Информация');
        $data = Az::l('Модератор неподтвердил документа');
        if (!empty($model->edu_end)) {
            if ($model->edu_end > date('Y-m-d')) {
                if ($model->program === 'intern' || $model->program === 'qualify')
                    $notify =  $this->notifyInfo($title, $data, RoleData::interqua);
                else
                    $notify =  $this->notifyInfo($title, $data, RoleData::interqua);

            } else
                $notify =  $this->notifyInfo($title, $data, RoleData::monitor);

        }


    }

    //end|JakhongirKudratov|2020-10-27
    //10ta

    /**
     *
     * Function  sendNotifyToAccounter
     * send notify to accouneter
     * @return  bool|void
     */
    //start|JakhongirKudratov|2020-10-27

    public function sendNotifyToAccounter()
    {
        $title = Az::l('Информация');
        $data = Az::l('Монитор подтвержен документы');

        $notify = $this->notifyInfo($title, $data, RoleData::accounter);

    }
    //4ta
    //end|JakhongirKudratov|2020-10-27

    //start|JakhongirKudratov|2020-10-27

    public function sendToAccounter($model)
    {

        $scholar_id = $model->eyuf_scholar_id;
        if (empty($scholar_id))
            return null;

        if ($this->status($scholar_id)) {
            $this->sendNotifyToAccounter();

            $scholar = EyufScholar::findOne($model->eyuf_scholar_id);
           // $this->notifyInfo('Информация', 'Ваши документы подтверждены', $scholar->user_id);
            return true;
        }


        return false;
    }

    //end|JakhongirKudratov|2020-10-27

    //13ta
    public function status($id)
    {

        /** @var EyufScholar $scholar */
        $scholar = EyufScholar::findOne($id);

        if ($scholar === null)
            return null;

        $docs = EyufDocument::findAll([
            'eyuf_scholar_id' => $id
        ]);

        $status = EyufScholar::status['stipend'];

        foreach ($docs as $doc) {
            if (!$doc->status)
                $status = EyufScholar::status['docReady'];
        }

        $scholar->status = $status;

        $scholar->configs->rules = [
            [validatorSafe]
        ];

        return $scholar->save();
    }


    public function statusAccount($id)
    {
        /** @var EyufScholar $scholar */
        $scholar = EyufScholar::findOne([
            'id' => $id
        ]);

        $docs = EyufInvoice::findAll([
            'eyuf_scholar_id' => $scholar->id
        ]);

        if (count($docs) === 0)
            return false;

        $status = EyufScholar::status['accounter'];

        foreach ($docs as $doc) {
            if ($doc->status === false)
                $status = EyufScholar::status['stipend'];
        }

        $scholar->status = $status;
        $scholar->configs->rules = validatorSafe;

        if ($scholar->save())
            Az::debug('OK');
    }


    #endregion


    public function getDocList($user_id = null)
    {
        $user = $this->userIdentity()->id;
        if ($user_id)
            $user = $user_id;
        /** @var  EyufScholar $scholar */
        $scholar = EyufScholar::findOne(['user_id' => $user]);

        $doctypes = $this->getDocumentTypes($scholar);
        $list = $this->makeDocumentList($scholar, $doctypes);
        $return = [];
        if (!empty($list))
            foreach ($list as $item) {
                if ($item['document'] === null)
                    $return[] = $item;
            }

        return $return;
    }

    /** @param EyufScholar $obj */
    public function getEmptyColumns($obj)
    {
        $return = [];

        $columns = $obj->columnsList();
        $labels = $obj->attributeLabels();
        foreach ($columns as $column) {
            if (empty($obj->$column) || ($obj->$column === null) || ($obj->$column == 0) || ($obj->$column === '""') || ($obj->$column == [])) {
                $return[] = [
                    'name' => $column,
                    'label' => $labels[$column]
                ];

            }
        }
        return $return;
    }

    public function getEmptyColumnsHTM()
    {
        $Sch = EyufScholar::findOne(['user_id' => $this->userIdentity()->id]);

        $columns = $this->getEmptyColumns($Sch);
        $htm = '<ol>';
        foreach ($columns as $item) {
            $htm .= '<li>' . $item['label'] . '</li>';
        }
        return $htm;
    }


    public function getDocListHTM($list)
    {
        $htm = <<<HTML
    <h5>Необходимые документы для рассмотрения вашей заявки:</h5> 
    <ol>
HTML;
        foreach ($list as $item)
            $htm .= '<li>' . $item['type']->name . '</li>';

        $htm .= <<<HTML
    </ol>
HTML;
        return $htm;
    }

    private function makeDocumentList($EyufScholar, $doctypes)
    {
        if ($EyufScholar === null)
            return false;

        $return = [];

        $documents = EyufDocument::findAll([
            'eyuf_scholar_id' => $EyufScholar->id,
        ]);
        $count = count($documents);
        foreach ($doctypes as $type) {
            $res = [
                'type' => $type,
                'document' => null
            ];
            foreach ($documents as $doc) {
                $doc_type_id = $doc->eyuf_document_type_id;
                $type_id = $type->id;
                if ($doc_type_id === $type_id)
                    $res['document'] = $doc;
            }
            $return[] = $res;
        }
        return $return;
    }


    private function getEyufScholar($user)
    {
        return EyufScholar::findOne(['user_id' => $user->id]);
    }

    private function getDocumentTypes($EyufScholar)
    {
        //vdd($EyufScholar);
        if ($EyufScholar === null)
            return false;
        //vdd($EyufScholar);
        $value = $EyufScholar->program;
        $key = 'program';
        $docTypes = EyufDocumentType::find()
            ->all();
        $return = [];

        foreach ($docTypes as $type) {
            //vdd($type);
            if ($type->program)
                foreach ($type->program as $program) {
                    if ($program == $EyufScholar->program)
                        $return[] = $type;
                }
        }
        return $return;
    }
}


