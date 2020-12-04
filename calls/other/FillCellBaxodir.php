<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\calls;


use Fusonic\Linq\Linq;
use yii\helpers\ArrayHelper;
use zetsoft\models\App\eyuf\db2\Cdr;
use zetsoft\models\calls\CallsCdr;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class FillCell extends ZFrame
{

    /* @var CallsCdr $callCdr */
    public $callCdr;

    /* @var array $calls */
    public $calls;


    public $startDate;

    #region Time

    public function real()
    {
        $this->startDate = $this->sessionGet('calldate');
        $this->run();
    }

    public function hour()
    {
        $this->startDate = Az::$app->cores->date->dateTime('-1 hours');
    }

    public function day()
    {

    }


    #endregion

    public function init()
    {
        parent::init();
    }


    public function run()
    {
        $this->all();

    }


    public function all()
    {


        $calls = Cdr::find()
            ->where([
                '>', 'calldate', $this->startDate
            ])
            ->asArray()
            ->all();

        foreach ($calls as $call) {

            /** @var CallsCdr $exists */
            $exists = CallsCdr::find()
                ->where([
                    'calldate' => $call['calldate'],
                    'uniqueid' => $call['uniqueid']
                ])
                ->exists();

            /*    $sPrefix = "# {$iIndex}/{$iCallItemsCount}";

                $sSuffix = "{$this->_company->Name}/{$this->_company->Id} |{$this->_platform->CrmIP} | StartDate = {$aCall['StartDate']}";*/


            if ($exists) {
                Az::trace("{$sPrefix} | UPDATE | ID = {$exists->Id} | {$sSuffix}");

                $model = CallsCdr::find()
                    ->where([
                        'calldate' => $call['calldate'],
                        'uniqueid' => $call['uniqueid']
                    ])
                    ->limit(1)
                    ->one();

            } else {
                Az::trace("{$sPrefix} | CREATE | CallNumber = {$aCall['ID']} | {$sSuffix}");

                $model = new CallsCdr();
            }


            $this->_model($model, $call);
        }

    }

    protected function _model($model, array $call)
    {
        /** @var Subject $subject */
        /** @var Enum $enum */


        $this->model = $model;

        /**
         *
         *
         * Dates
         */

        $this->callCdr->calldate = $call['calldate'];


        /**
         *
         *
         * Times
         */

        $this->model->TalkTime = $call['TalkTime'];
        $this->model->HoldTime = $call['HoldTime'];
        $this->model->DIDNumber = $call['NUMERKUDO'];
        $this->model->ProcessingTime = $call['ProcessingTime'];


        /**
         *
         *
         * Numbers
         */

        $this->model->FIO = $call['FIO'];
        $this->model->PhoneNumber = $call['PhoneNumber'];
        $this->model->CallNumber = $call['CallNumber'];
        $this->model->SwitchToPhoneNumber = $call['SwitchToPhoneNumber'];


        /**
         *
         *
         * Guids
         */

        $this->model->Guids = $call['ID'];
        $this->model->RecordGuids = $call['IntegrationID'];


        /**
         *
         *
         * Direction
         */

        if ($call['DirectionID']) {
            $this->model->Direction = Direction::Direct_Out;
            $this->aCallDirection = [
                'CallsOut' => 1
            ];
            $this->bIsOutgoing = true;
        } else {
            $this->model->Direction = Direction::Direct_In;
            $this->aCallDirection = [
                'CallsIn' => 1
            ];
            $this->bIsOutgoing = false;
        }


        Az::trace(Direction::$core[$this->model->Direction], '#    Added Direction', Cat_InsertField);


        /**
         *
         *
         * Extra Variables
         */


        $this->model->CompanyID = $this->_company->Id;


        /**
         *
         *
         * Result
         */

        $sItemResult = $call['ResultID'];

        /** @var Result $result */

        if (!empty($sItemResult)) {
            if (ArrayHelper::keyExists($sItemResult, $this->_aResult))
                $result = $this->_aResult[$sItemResult];
            else
                Az::warning($sItemResult, 'Undefined index in Results');

            if ($result) {
                $this->model->ResultID = $result->Id;
                Az::trace($result->Name, '#    Added Result', Cat_InsertField);
                $this->statResult = $result;
            }
        } else {
            $this->model->ResultID = null;
        }


        /**
         *
         *
         * Get Enums
         */

        $aEnums = [
            'Enum_1',
            'Enum_2',
            'Enum_3',
            'Enum_4',
            'Enum_5',
            'Enum_6',
            'Enum_7',
            'Enum_8',
            'WHO',
        ];


        foreach ($aEnums as $enumID) {
            if (!empty($call[$enumID])) {

                if (ArrayHelper::keyExists($call[$enumID], $this->_aEnum))
                    $enum = $this->_aEnum[$call[$enumID]];
                else {
                    Az::warning("Undefined index in Enum!  Company = {$this->_company->Name} Enum ID = {$call[$enumID]} | ID = {$this->model->Id} | CallNumber = {$this->model->CallNumber}");
                    continue;
                }

                if ($enum) {
                    $this->model->setAttribute($enumID, $enum->Id);
                    Az::trace($enum->Name, '#    Added Enum', Cat_InsertField);

                    $this->aStatEnum[] = $enum;
                }
            } else {
                $this->model->setAttribute($enumID, null);
            }
        }


        /**
         *
         *
         * Subjects
         */

        $aSubjects = Linq::from($this->_aSubject)
            ->where(function (Subject $subject) {
                if ($subject->CompanyID === $this->_company->Id)
                    return true;
                return false;
            })
            ->toArray();

        $callSubjectString = null;


        foreach ($aSubjects as $subject) {

            if ($call[$subject->Name] === 1) {
                $callSubjectString .= '- ' . $subject->Title . PHP_EOL . '<br/>';
                Az::trace($subject->Title, '#    Added Subject', Cat_InsertField);

                $this->aStatSubject[] = $subject;
            }
        }

        $this->model->Subjects = $callSubjectString;


        /**
         *
         *
         * User
         */

        /** @var User $users */

        $users = Linq::from($this->_aUser)
            ->where(function (User $user) use ($call) {
                if ($user->Guids === $call['CreatedByID'])
                    return true;

                return false;
            })->toArray();


        if (!empty($users)) {

            /** @var User $user */
            $user = $users[0];
            $this->model->Operator = $user->Id;

            Az::trace($user->Username, '#    Added Agent', Cat_InsertField);

            $this->statAgent = $user;

        }


        /**
         *
         *
         * Bools
         */

        for ($i = 1; $i <= 30; $i++) {
            $sFieldName = "Bool_$i";

            $this->model->setAttribute($sFieldName, $call[$sFieldName]);

            if ($call[$sFieldName])
                Az::trace($sFieldName, '#    Added Bool', Cat_InsertField);
        }


        /**
         *
         *
         * Texts
         */

        for ($i = 1; $i <= 14; $i++) {
            $sFieldName = "Text_$i";

            $this->model->setAttribute($sFieldName, $call[$sFieldName]);

            if ($call[$sFieldName])
                Az::trace($sFieldName, '#    Added Text', Cat_InsertField);
        }


        /**
         *
         *
         * Dates
         */

        for ($i = 1; $i <= 2; $i++) {
            $sFieldName = "Date_$i";

            $this->model->setAttribute($sFieldName, $call[$sFieldName]);

            if ($call[$sFieldName])
                Az::trace($sFieldName, '#    Added Date', Cat_InsertField);
        }


        /**
         *
         *
         * Set Is Counted
         */

        if (!$this->model->IsCounted)
            if ($this->zcore->zStat->run($this))
                $this->model->IsCounted = 1;


        /**
         *
         *
         * Save TblCall
         */

        if ($this->model->isNewRecord) {
            if ($this->model->save())
                Az::trace($this->model->Id, 'TblCall Success Saved! ID', Cat_ModelSave);

        } else {
            $aChangedAttributes = $this->model->getDirtyAttributes();

            if (!empty($aChangedAttributes))
                if ($this->model->save())
                    Az::trace($aChangedAttributes, 'TblCall Changes Success Saved!', Cat_ModelSave);

        }

    }

}
