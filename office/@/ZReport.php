<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 *
 */

namespace zetsoft\service\App\eyuf;

use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ZReport extends ZFrame
{


    /**
     *
     *
     * Configurations
     */

    public $bExecute = true;


    /**
     *
     * Fixed Data
     */

    private $_sFolder_Sample = '@asrorz/report';
    private $_formNum = 4;


    private const ALL = 'all';
    private const INC = 'inc';
    private const OUT = 'out';


    private const Type_Word = 1;
    private const Type_Excel = 2;

    private $_iType = self::Type_Word;

    /**
     *
     * Variables
     */

    private $_sTmpWord = 'Native.docx';
    private $_sTmpExcel = 'Native.xlsx';

    private $_sReadyWord;
    private $_sReadyExcel;

    private $_sFolderCompany;


    /**
     *
     * Processing Data
     */

    private $_iReportID;
    private $_iCompanyID;
    private $_sStartDate;
    private $_sEndDate;

    private $_aEnumField;
    private $_aTextField;

    private $_aCall;

    /** @var Company $_company */
    private $_company;

    /** @var Enum[] $_aWhoItem */
    private $_aWhoItem;

    /** @var Field $_who */
    private $_who;

    /** @var TemplateProcessor $_objWord */
    private $_objWord;

    /** @var \PHPExcel $_objExcel */
    private $_objExcel;

    /** @var \PHPExcel_Worksheet $_sheet */
    private $_sheet;


    /**
     *
     * Core Arrays
     */

    private $_aSubject;
    private $_aField;
    private $_aEnum;
    private $_aUser;
    private $_aResult;


    private function _excel()
    {
        $count = \count($this->_aCall);

        for ($i = 1; $i <= $count - 1; $i++) {

            $call = $this->_aCall[$i];
            $iI = $i + 1;

            $this->_call($call, $iI);

        }

    }


    private function _call(array $call, int $iI)
    {
        /**
         * Define Subjects
         */

        $sSubjects = $call['Subjects'];
        $sSubjects = str_replace('<br/>', '', $sSubjects);
        $sSubjects = trim($sSubjects);

        $aCallSubject = explode('- ', $sSubjects);


        /**
         *
         * Operators
         */

        $aUser = Linq::from($this->_aUser)
            ->where(function (User $item) use ($call) {
                return $item->Id === $call['Operator'];
            })
            ->toArray();

        $sAgent = null;

        if (!empty($aUser))
            $sAgent = $aUser[0]->Username;


        /**
         *
         * Fill Excel File
         */

        /* @private $operators array */

        $this->_sheet->setCellValue('A' . $iI, $call['CallNumber']);
        $this->_sheet->setCellValue('B' . $iI, Direction::$core[$call['Direction']]);
        $this->_sheet->setCellValue('C' . $iI, $call['PhoneNumber']);
        $this->_sheet->setCellValue('D' . $iI, $call['StartDate']);
        $this->_sheet->setCellValue('E' . $iI, $call['EndDate']);
        $this->_sheet->setCellValue('F' . $iI, $call['TalkTime']);
        $this->_sheet->setCellValue('G' . $iI, $call['HoldTime']);
        $this->_sheet->setCellValue('H' . $iI, $call['ProcessingTime']);
        $this->_sheet->setCellValue('I' . $iI, $sAgent);
        $this->_sheet->setCellValue('J' . $iI, $sSubjects);

        $this->_sheet->setCellValue('K' . $iI, $call['SwitchToPhoneNumber']);

        if (ArrayHelper::keyExists(1, $aCallSubject))
            $this->_sheet->setCellValue('L' . $iI, $aCallSubject[1]);

        if (ArrayHelper::keyExists(2, $aCallSubject))
            $this->_sheet->setCellValue('M' . $iI, $aCallSubject[2]);

        if (ArrayHelper::keyExists(3, $aCallSubject))
            $this->_sheet->setCellValue('N' . $iI, $aCallSubject[3]);

        if (ArrayHelper::keyExists(4, $aCallSubject))
            $this->_sheet->setCellValue('O' . $iI, $aCallSubject[4]);
    }


    public function excel(int $iReportID = null)
    {

        $this->_iReportID = $iReportID;
        $this->_iType = self::Type_Excel;

        $this->_main();
        $this->_zinit();

        $this->_excel();
        $this->_write();

    }


    /**
     *
     * Function  prepBasicData
     */
    private function _basic()
    {


        /**
         *
         * Set Main Datas
         */

        $this->_objWord->setValue('projectName', $this->_company->Name);
        $this->_objWord->setValue('reportYear', date('Y'));

        /**
         *
         * EyufReport Month
         */
        /** @var array $monthNames */


        $sMonth = $this->zcore->zDate->month($this->_sStartDate);
        $ccReportMonth = $this->zcore->zDate->monthName($sMonth);

        $this->_objWord->setValue('reportMonth', $ccReportMonth);


        /**
         *
         * Export Dates
         */

        $this->_objWord->setValue('startDate', $this->_sStartDate);
        $this->_objWord->setValue('endDate', $this->_sEndDate);


        /**
         *
         * All Calls
         */

        $ccAllCalls = \count($this->_aCall);

        $this->_objWord->setValue('allCalls', $ccAllCalls);
        $this->_objWord->setValue('who-total-all', $ccAllCalls);


        $iAllIncomingCalls = 0;
        $iAllOutgoingCalls = 0;

        foreach ($this->_aCall as $call) {
            if ($this->zcore->zActiveCall->isOut($call))
                $iAllOutgoingCalls++;
            else
                $iAllIncomingCalls++;
        }


        $this->_objWord->setValue('outgoingCalls', $iAllOutgoingCalls);
        $this->_objWord->setValue('who-total-out', $iAllOutgoingCalls);

        $this->_objWord->setValue('incomingCalls', $iAllIncomingCalls);
        $this->_objWord->setValue('who-total-inc', $iAllIncomingCalls);

    }

    private function _subject()
    {
        /**
         *
         *
         *
         * Subjects
         *
         */


        $sjCount = \count($this->_aSubject);
        $this->_objWord->cloneRow('subject', $sjCount);


        /*
        * Process
        */

        $callSubjects = [];
        $callSubjectAll = new ZReportItem();

        foreach ($this->_aSubject as $subject) {
            $callSubject = new ZReportItem();

            foreach ($this->_aCall as $call) {
                if ($call[$subject->Name] === 1) {
                    if ($this->zcore->zActiveCall->isOut($call))
                        $callSubject->out++;
                    else
                        $callSubject->inc++;

                    $callSubject->all++;
                }

            }


            $callSubjects[$subject->Title] = $callSubject;
            $callSubjectAll->inc += $callSubject->inc;
            $callSubjectAll->out += $callSubject->out;
            $callSubjectAll->all += $callSubject->all;

        }


        /**
         *
         *
         * Subjects Write Content
         */

        $sjCounter = 1;

        /** @var ZReportItem $callSubject */
        foreach ($callSubjects as $title => $callSubject) {

            $this->_objWord->setValue('subject#' . $sjCounter, $title);

            $this->_objWord->setValue('subject-inc#' . $sjCounter, $this->_nullValue($callSubject->inc));
            $this->_objWord->setValue('subject-out#' . $sjCounter, $this->_nullValue($callSubject->out));
            $this->_objWord->setValue('subject-all#' . $sjCounter, $this->_nullValue($callSubject->all));

            $sjCounter++;

        }

        $this->_objWord->setValue('subject-total-inc', $this->_nullValue($callSubjectAll->inc));
        $this->_objWord->setValue('subject-total-out', $this->_nullValue($callSubjectAll->out));
        $this->_objWord->setValue('subject-total-all', $this->_nullValue($callSubjectAll->all));


    }

    /**
     *
     * Function  prepWhoCalled
     */
    private function _who()
    {


        /**
         *
         * Clone Row
         */

        $whoCount = \count($this->_aWhoItem);

        for ($i = 1; $i <= 6; $i++)
            $this->_objWord->cloneRow('who', $whoCount);


        /**
         *
         * Who Called Prepare
         */

        if (!$this->_aWhoItem)
            return null;

        $aWhoField = [];

        foreach ($this->_aWhoItem as $whoItem) {

            $callField = new ZReportItem();

            foreach ($this->_aCall as $call) {

                if ($call[$this->_who->Name] === $whoItem->Id) {

                    if ($this->zcore->zActiveCall->isOut($call))
                        $callField->out++;
                    else
                        $callField->inc++;

                    $callField->all++;

                }

            }

            $aWhoField [$whoItem->Name] = $callField;
        }


// var_dump($aWhoField);


        /**
         *
         * Who Called Fill
         */

        $ctCounter = 1;
        /** @var ZReportItem[] $aWhoField */

        foreach ($aWhoField as $key => $whoField) {

            $this->_objWord->setValue('who#' . $ctCounter, $key);

            $this->_objWord->setValue('who-inc#' . $ctCounter, $this->_nullValue($whoField->inc));
            $this->_objWord->setValue('who-out#' . $ctCounter, $this->_nullValue($whoField->out));
            $this->_objWord->setValue('who-all#' . $ctCounter, $this->_nullValue($whoField->all));

            $ctCounter++;

        }


        return true;

    }

    private function _whoDays()
    {

        if (!$this->_aWhoItem)
            return null;


        /**
         *
         * Who Called Days Prepare
         */

        $whoAll = [];
        $whoDecada = [];
        $whoDecadaAll = [];
        $whoFieldAll = [];


        $whoCount = 1;
        foreach ($this->_aWhoItem as $whoItem) {

            for ($day = 1; $day <= 31; $day++) {

                $callField = new ZReportItem();

                foreach ($this->_aCall as $model) {
                    $callDay = (int)date_format(date_create($model['StartDate']), 'j');

                    if ($model[$this->_who->Name] === $whoItem->Id && $day === $callDay) {

                        if ($this->zcore->zActiveCall->isOut($model))
                            $callField->out++;
                        else
                            $callField->inc++;

                        $callField->all++;
                    }
                }


                $whoFieldAll[$day][$whoCount] = $callField;


                if (empty($whoAll[$day]))
                    $whoAll[$day] = 0;
                $whoAll[$day] += $callField->all;

                /**
                 *
                 * Who Decada
                 */

                if (empty($whoDecada[$this->_dekada($day)][$whoCount][self::ALL]))
                    $whoDecada[$this->_dekada($day)][$whoCount][self::ALL] = 0;
                $whoDecada[$this->_dekada($day)][$whoCount][self::ALL] += $callField->all;

                if (empty($whoDecada[$this->_dekada($day)][$whoCount][self::INC]))
                    $whoDecada[$this->_dekada($day)][$whoCount][self::INC] = 0;
                $whoDecada[$this->_dekada($day)][$whoCount][self::INC] += $callField->inc;

                if (empty($whoDecada[$this->_dekada($day)][$whoCount][self::OUT]))
                    $whoDecada[$this->_dekada($day)][$whoCount][self::OUT] = 0;
                $whoDecada[$this->_dekada($day)][$whoCount][self::OUT] += $callField->out;


                /**
                 *
                 * Who Decada All
                 */

                if (empty($whoDecadaAll[$this->_dekada($day)]))
                    $whoDecadaAll[$this->_dekada($day)] = 0;

                $whoDecadaAll[$this->_dekada($day)] += $callField->all;


            }

            $whoCount++;

        }


        /**
         *
         * Who Called Fill
         */

        $whoCount = 1;
        foreach ($this->_aWhoItem as $whoItem) {

            /**
             *
             * Days Filling
             */

            for ($day = 1; $day <= 31; $day++) {

                $this->_objWord->setValue('day-inc-' . $day . '#' . $whoCount, $this->_nullValue($whoFieldAll[$day][$whoCount]->inc));

                $this->_objWord->setValue('day-out-' . $day . '#' . $whoCount, $this->_nullValue($whoFieldAll[$day][$whoCount]->out));
            }


            /**
             *
             * Dekada Filling
             */

            for ($iDekada = 1; $iDekada <= 3; $iDekada++) {

                $this->_objWord->setValue('day-inc-N' . $iDekada . '#' . $whoCount, $this->_nullValue($whoDecada[$iDekada][$whoCount][self::INC]));
                $this->_objWord->setValue('day-out-N' . $iDekada . '#' . $whoCount, $this->_nullValue($whoDecada[$iDekada][$whoCount][self::OUT]));


            }


            $whoCount++;
        }

        for ($day = 1; $day <= 31; $day++) {
            $this->_objWord->setValue('day-all-' . $day, $whoAll[$day]);
        }


        /**
         *
         * Dekada All Filling
         */

        for ($iDekada = 1; $iDekada <= 3; $iDekada++) {

            $this->_objWord->setValue('day-all-N' . $iDekada, $this->_nullValue($whoDecadaAll[$iDekada]));

        }


    }

    private function _whoHour()
    {


        if (!$this->_aWhoItem)
            return null;

        /**
         *
         * Who Called Days Prepare
         */

        $whoAll = [];
        $whoFieldAll = [];


        $whoCount = 1;
        foreach ($this->_aWhoItem as $whoItem) {

            for ($hour = 0; $hour <= 23; $hour++) {

                $callField = new ZReportItem();

                foreach ($this->_aCall as $model) {
                    $callHour = (int)date_format(date_create($model['StartDate']), "G");

                    if ($model[$this->_who->Name] === $whoItem->Id && $hour === $callHour) {

                        if ($this->zcore->zActiveCall->isOut($model))
                            $callField->out++;
                        else
                            $callField->inc++;

                        $callField->all++;
                    }

                }

                $whoFieldAll[$hour][$whoCount] = $callField;

                if (empty($whoAll[$hour]))
                    $whoAll[$hour] = 0;

                $whoAll[$hour] += $callField->all;


            }


            $whoCount++;

        }


//	var_dump($whoField);


        /**
         *
         * Who Called Fill
         */


        $whoCount = 1;
        foreach ($this->_aWhoItem as $whoItem) {

            /**
             *
             * Days Filling
             */

            for ($hour = 0; $hour <= 23; $hour++) {

                $this->_objWord->setValue('hour-inc-' . $hour . '#' . $whoCount, $this->_nullValue($whoFieldAll[$hour][$whoCount]->inc));
                $this->_objWord->setValue('hour-out-' . $hour . '#' . $whoCount, $this->_nullValue($whoFieldAll[$hour][$whoCount]->out));
            }

            $whoCount++;
        }


        for ($hour = 0; $hour <= 23; $hour++) {
            $this->_objWord->setValue('hour-all-' . $hour, $whoAll[$hour]);
        }


    }

    private function _enumField()
    {

        /**
         *
         * Clone
         */

        $enumFieldCnt = \count($this->_aEnumField);


        Az::trace($enumFieldCnt, '$enumFieldCnt');

        $this->_objWord->cloneRow('fe-frm', $enumFieldCnt);
        $this->_objWord->cloneBlock('fields-enum', $enumFieldCnt);

        /** @var Field $enumField */

        $feFrmCnt = 1;
        foreach ($this->_aEnumField as $enumField) {

            /**
             *
             * Title Page
             */
            $this->_objWord->setValue("fe-frm#$feFrmCnt", $this->_formNum);
            $this->_objWord->setValue("fe-title#$feFrmCnt", $enumField->Title);


            /**
             *
             * Prepare Table Variables
             */

            $this->_objWord->setValue("fe-form", $this->_formNum, 1);
            $this->_objWord->setValue("fe-title", $enumField->Title, 1);


            /**
             *
             * Fill Tables
             */

            /** @var Enum[] $enums */
            $enums = Linq::from($this->_aEnum)
                ->where(function (Enum $item) use ($enumField) {
                    return $item->FieldID === $enumField->Id;
                })
                ->toArray();

            /** @var Enum $enum */
            $enum = new Enum();
            $enum->Name = 'Не указан';
            $enum->Id = '';

            $enums[] = $enum;

            $enmCount = \count($enums);

            Az::trace($enmCount, "Enum Count for $enumField->Name = $enmCount");


            $this->_objWord->cloneRow('fe-name', $enmCount);


            /**
             *
             * Prepare Core Table Rows
             */

            $enmField = [];
            $enmFieldAll = new ZReportItem();

            foreach ($enums as $enum) {
                $callField = new ZReportItem();

                foreach ($this->_aCall as $model) {

                    if ($model[$enumField->Name] === $enum->Id) {

                        if ($this->zcore->zActiveCall->isOut($model))
                            $callField->out++;
                        else
                            $callField->inc++;

                        $callField->all++;

                    }

                }

                $enmFieldAll->all += $callField->all;
                $enmFieldAll->inc += $callField->inc;
                $enmFieldAll->out += $callField->out;

                $enmField [$enum->Name] = $callField;
            }


            /**
             *
             * Who Called Fill
             */

            $ctCounter = 1;

            /** @var ZReportItem[] $enmField */

            foreach ($enmField as $key => $zItem) {

                $this->_objWord->setValue('fe-name#' . $ctCounter, $key, 1);

                $this->_objWord->setValue('fe-inc#' . $ctCounter, $this->_nullValue($zItem->inc), 1);
                $this->_objWord->setValue('fe-out#' . $ctCounter, $this->_nullValue($zItem->out), 1);
                $this->_objWord->setValue('fe-all#' . $ctCounter, $this->_nullValue($zItem->all), 1);

                $ctCounter++;

            }

            $this->_objWord->setValue('fe-inc-all', $enmFieldAll->inc, 1);
            $this->_objWord->setValue('fe-out-all', $enmFieldAll->out, 1);
            $this->_objWord->setValue('fe-all-all', $enmFieldAll->all, 1);


            $feFrmCnt++;
            $this->_formNum++;
        }


    }

    private function _textField()
    {


        /**
         *
         * Clone
         */

        $textFieldCnt = \count($this->_aTextField);
//	AZ::info("textFieldCnt = $textFieldCnt", $this->cat);

        $this->_objWord->cloneBlock('fields-text', $textFieldCnt);
        $this->_objWord->cloneRow('ft-frm', $textFieldCnt);

        /** @var Field $textField */

        $ftFrmCnt = 1;
        foreach ($this->_aTextField as $textField) {
            $this->_objWord->setValue("ft-frm#$ftFrmCnt", $this->_formNum);
            $this->_objWord->setValue("ft-title#$ftFrmCnt", $textField->Title);


            /**
             *
             * Prepare Table Variables
             */

            $this->_objWord->setValue("ft-form", $this->_formNum, 1);
            $this->_objWord->setValue("ft-title", $textField->Title, 1);


            /**
             *
             * Prepare Data
             */

            $allTextField = [];

            foreach ($this->_aCall as $model) {

                if (!empty($model[$textField->Name]))
                    $allTextField[] = $model;

            }


            /**
             *
             * Clone Row
             */

            $textCount = \count($allTextField);
//	AZ::info("Text Items Count for $_aTextField->Name = $textCount", $this->cat);

            $this->_objWord->cloneRow('ftnum', $textCount);


            /**
             *
             * Fill Data
             */


            $ctCounter = 1;
            /** @var ZReportItem[] $whoField */

            foreach ($allTextField as $model) {

                $this->_objWord->setValue('ftnum#' . $ctCounter, $ctCounter, 1);

                $this->_objWord->setValue('ft-fio#' . $ctCounter, $model['FIO'], 1);
                $this->_objWord->setValue('ft-time#' . $ctCounter, $model['StartDate'], 1);
                $this->_objWord->setValue('ft-telnumber#' . $ctCounter, $model['PhoneNumber'], 1);
                $this->_objWord->setValue('ft-callnumber#' . $ctCounter, $model['Id'], 1);
                $this->_objWord->setValue('ft-text#' . $ctCounter, $model[$textField->Name], 1);

                $ctCounter++;

            }


            $ftFrmCnt++;
            $this->_formNum++;
        }


        /**
         *
         *  Prepare
         */


    }


    public function zinit()
    {
        $this->_aUser = $this->zcore->zActive->agents();
        $this->_aResult = $this->zcore->zActive->results;

        $this->_sTmpWord = Az::getAlias("{$this->_sFolder_Sample}/{$this->_sTmpWord}");
        $this->_sTmpExcel = Az::getAlias("{$this->_sFolder_Sample}/{$this->_sTmpExcel}");

        $this->_sTmpWord = FileHelper::normalizePath($this->_sTmpWord);
        $this->_sTmpExcel = FileHelper::normalizePath($this->_sTmpExcel);

    }


    public function _dekada($day): int
    {
        if ($day >= 1 && $day <= 10) {
            return 1;
        }

        if ($day >= 11 && $day <= 20) {
            return 2;
        }

        return 3;
    }


    public function _nullValue($iValue): int
    {

        if ((int)$iValue > 0)
            return $iValue;
        else
            return 0;
    }


    public function word(int $iReportID)
    {

        $this->_iReportID = $iReportID;
        $this->_iType = self::Type_Word;

        $this->_main();
        $this->_zinit();

        $this->_basic();
        $this->_subject();

        $this->_who();
        $this->_whoDays();
        $this->_whoHour();

        $this->_enumField();
        $this->_textField();

        $this->_write();

        return true;
    }


    /**
     *
     * Function  _main
     */
    private function _main()
    {

        $report = EyufReport::findOne($this->_iReportID);

        $this->_iCompanyID = $report->CompanyID;
        $this->_sStartDate = $report->StartDate;
        $this->_sEndDate = $report->EndDate;

        $this->_company = Company::findOne($this->_iCompanyID);


        /**
         *
         *
         * Core Data
         */

        $this->_aCall = $this->zcore->zActiveCall->dataAPI($this->_iCompanyID, $this->_sStartDate, $this->_sEndDate);

        $this->_aCall = Linq::from($this->_aCall)
            ->select(function (array $call) {
                return $this->zcore->zActive->arrayType($call, ZTblCallSelect::Int());
            })
            ->toArray();

        $this->_aSubject = $this->zcore->zActive->subjectsByCompanyID($this->_iCompanyID);

        $this->_aField = Field::find()
            ->where([
                'CompanyID' => $this->_iCompanyID,
            ])
            ->andWhere('`IsReport` IS NULL Or `IsReport` = 1')
            ->all();

        $this->_aEnum = $this->zcore->zActive->enums;

        $this->_aEnumField = Linq::from($this->_aField)
            ->where(function (Field $field) {
                return $field->Type === Type::Type_Enum;
            })
            ->toArray();


        $this->_aTextField = Linq::from($this->_aField)
            ->where(function (Field $field) {
                return $field->Type === Type::Type_Text && $field->Name !== 'FIO';
            })
            ->toArray();


        /**
         *
         * Process Who Called
         */

        $whoLinq = Linq::from($this->_aEnumField)
            ->where(function (Field $field) {
                return $field->Name === 'WHO';
            })
            ->toArray();

        if (!empty($whoLinq))
            $this->_who = $whoLinq[0];


        if ($this->_who) {
            $this->_aWhoItem = Linq::from($this->_aEnum)
                ->where(function (Enum $item) {
                    return $item->FieldID === $this->_who->Id;
                })
                ->toArray();

            /** @var Enum $enum */
            $enum = new Enum();
            $enum->Name = 'Не указан';
            $enum->Id = '';

            $this->_aWhoItem[] = $enum;

        }

    }

    private function _zinit()
    {


        /**
         *
         * Folder Company and Reports
         */

        $this->_sFolderCompany = "{$this->zcore->boot->sFolder_Reports}/{$this->_company->Name}/";
        FileHelper::createDirectory($this->_sFolderCompany);


        switch ($this->_iType) {

            case self::Type_Word:

                /**
                 *
                 * Word
                 */

                $this->_sReadyWord = $this->_sFolderCompany . $this->_iReportID . '.docx';

                $this->_sReadyWord = FileHelper::normalizePath($this->_sReadyWord);

                $this->_objWord = new TemplateProcessor(Az::getAlias($this->_sTmpWord));

                if (file_exists($this->_sReadyWord))
                    if (!unlink($this->_sReadyWord))
                        return Az::error($this->_sReadyWord, 'Cannot Remove Ready File Word');

                break;


            case self::Type_Excel:

                /**
                 *
                 * Excel
                 */

                $this->_sReadyExcel = $this->_sFolderCompany . $this->_iReportID . '.xlsx';
                $this->_sReadyExcel = FileHelper::normalizePath($this->_sReadyExcel);
                $reader = PHPExcel_IOFactory::createReader('Excel2007');

                $this->_objExcel = $reader->load(Az::getAlias($this->_sTmpExcel));

                if (file_exists($this->_sReadyExcel))
                    if (!unlink($this->_sReadyExcel))
                        return Az::error($this->_sReadyExcel, 'Cannot Remove Ready File Excel');

                $this->_sheet = $this->_objExcel->getActiveSheet();

                break;

        }


        return true;
    }


    private function _write()
    {

        switch ($this->_iType) {
            case self::Type_Word:

                /**
                 *
                 * Word
                 */
                $this->_objWord->saveAs($this->_sReadyWord);

                if (!file_exists($this->_sReadyWord))
                    return Az::error($this->_sReadyWord, 'Cannot Generate Word File');


                $report = EyufReport::findOne($this->_iReportID);
                $report->ReportWord = $this->_sReadyWord;
                $report->save();

                if ($this->bExecute && $this->zcore->boot->userDeveloper())
                    shell_exec($this->_sReadyWord);

                break;


            case self::Type_Excel:

                $objWriter = PHPExcel_IOFactory::createWriter($this->_objExcel, 'Excel2007');
                $objWriter->save($this->_sReadyExcel);

                if (!file_exists($this->_sReadyExcel))
                    return Az::error($this->_sReadyExcel, 'Cannot Generate Excel File');

                $report = EyufReport::findOne($this->_iReportID);
                $report->ReportExcel = $this->_sReadyExcel;
                $report->save();

                if ($this->bExecute && $this->zcore->boot->userDeveloper())
                    shell_exec($this->_sReadyExcel);

                break;
        }


        return true;

    }
}
