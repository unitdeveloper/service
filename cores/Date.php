<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cores;


use DateTime;
use Mpdf\Tag\Time;
use Underscore\Types\Number;
use yii\console\widgets\Table;
use yii\helpers\ArrayHelper;
use zetsoft\dbitem\date\DateDiffItem;
use zetsoft\dbitem\date\DateRangeItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\inputes\ZPeriodPickerWidget;

class Date extends ZFrame
{


    public function test()
    {
        $this->dateHourTest();
        $this->minuteTest();
        //$this->dateTimeTest();
    }


    public const formatDate = 'yyyy-mm-dd';

    public const fbFormatDate = 'dd-mm-yyyy';

    public const formatDateTime = 'yyyy-mm-dd hh:ii:ss';

    public const formatDateTimeViceVersa = 'dd-mm-yyyy hh:ii:ss';


    /**
     *
     * Date & Times
     */

    public const hour = 'H';
    public const day = 'd';
    public const month = 'm';
    public const year = 'Y';
    public const minute = 'i';
    public const second = 's';

    public const dateHour = 'Y-m-d H:00:00';
    public const dateHourPlus = 'Y-m-d H-{}';

    public const date = 'Y-m-d';
    public const dateTime = 'Y-m-d H:i:s';

    public const dateTime_Start = 'Y-m-d H:i:00';
    public const dateTime_End = 'Y-m-d H:i:59';

    public const dateTime_Day_Start = 'Y-m-d 00:00:00';
    public const dateTime_Day_End = 'Y-m-d 23:59:59';

    public const dateTime_Month_Start = 'Y-m-01 00:00:00';
    public const dateTime_Month_End = 'Y-m-t 23:59:59';

    public const dateTime_Item_Start = '{Y}-{m}-{d} 00:00:00';
    public const dateTime_Item_End = '{Y}-{m}-{d} 23:59:59';

    public const dateTime_Year_Start = 'Y-01-01 00:00:00';
    public const dateTime_Year_End = 'Y-12-t 23:59:59';


    public const fbDateTime = 'd.m.Y H:i:s';
    public const fbDate = 'd.m.Y';

    public const fbDateTime_Day_Start = 'd.m.Y 00:00:00';
    public const fbDateTime_Month_Start = '01.m.Y 00:00:00';
    public const fbDateTime_Year_Start = '01.01.Y 00:00:00';

    public const fbDateTime_Day_End = 'd.m.Y 23:59:59';
    public const fbDateTime_Month_End = 't.m.Y 23:59:59';
    public const fbDateTime_Year_End = 't.12.Y 23:59:59';

    public const monthName = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];


    public function extract($dateString)
    {
        // 2017-07-21 19:14:05

        $pattern = '/(\d+\-\d+\-\d+) .*/i';
        $replacement = '$1';

        return preg_replace($pattern, $replacement, $dateString);
    }


    public function dates(string $sSuffix = ZPeriodPickerWidget::Suffix_One): DateRangeItem
    {
        $request = Az::$app->request;
        $session = Az::$app->session;


        $id_Start = "period_start_{$sSuffix}";
        $id_End = "period_end_{$sSuffix}";
        $id_Session = "dates_{$sSuffix}";

        $dates = new DateRangeItem();

        switch (true) {

            case $request->post($id_Start):
                $dates->startDate = $this->dateTime_Start($request->post($id_Start));
                $dates->endDate = $this->dateTime_End($request->post($id_End));
                $session->set($id_Session, $dates);
                break;

            case $request->get($id_Start):
                $dates->startDate = $this->dateTime_Start($request->get($id_Start));
                $dates->endDate = $this->dateTime_End($request->get($id_End));
                $session->set($id_Session, $dates);
                break;

            case $session->get($id_Session):
                $dates = $session->get($id_Session);
                break;

            default:
                $dates->startDate = Az::$app->cores->date->dateTime_Month_Start();
                $dates->endDate = Az::$app->cores->date->dateTime('+1 hours');
        }


        Az::trace($dates, 'Current ZDates');

        return $dates;

    }


    public function monthsBetween(string $startDate, string $endDate): array
    {
        $iStartMonth = $this->month($startDate);
        $iEndMonth = $this->month($endDate);

        $monthsBetween = [];
        for ($iI = $iStartMonth; $iI <= $iEndMonth; $iI++) {
            $monthsBetween[] = $iI;
        }

        return $monthsBetween;
    }

    function daysBetween(string $startDate, string $endDate)
    {

        $iStartMonth = $this->month($startDate);
        $iEndMonth = $this->month($endDate);

        $iStartDay = $this->day($startDate);
        $iEndDay = $this->day($endDate);

        $iStartYear = $this->year($startDate);
        $iEndYear = $this->year($endDate);

        if ($iStartYear != $iEndYear)
            throw new \LogicException('iEndYear & iStartYear not Equal');

        $aDaysBetween = [];

        for ($iMonth = $iStartMonth; $iMonth <= $iEndMonth; $iMonth++) {
            $iDayMax = cal_days_in_month(CAL_GREGORIAN, $iMonth, $iEndYear);
            $iDayMin = 1;

            if ($iMonth == $iEndMonth)
                $iDayMax = $iEndDay;

            if ($iMonth == $iStartMonth)
                $iDayMin = $iStartDay;

            for ($iDay = $iDayMin; $iDay <= $iDayMax; $iDay++) {
                $mktime = mktime(0, 0, 0, $iMonth, $iDay, $iEndYear);
                $date = date(self::date, $mktime);
                $aDaysBetween[] = $date;
            }
        }

        return $aDaysBetween;
    }


    public function monthName(int $iIndex, bool $bPrev = false)
    {
        if ($bPrev)
            if ($iIndex >= 2)
                $iIndex--;
            else
                $iIndex = 12;

        if (ArrayHelper::keyExists($iIndex, self::monthName))
            return self::monthName[$iIndex];

        return null;
    }

    public function hour($string = null)
    {
        if ($string)
            $return = date(self::hour, strtotime($string));
        else
            $return = date(self::hour);

        return (int)$return;
    }


    #region minute


    public function minuteTest()
    {
        $data = $this->minute();
        vd($data);
    }

    public function minute($string = null)
    {
        if ($string)
            $return = date(self::minute, strtotime($string));
        else
            $return = date(self::minute);

        return (int)$return;
    }


    #endregion

    public function month($string = null)
    {
        if ($string)
            $return = date(self::month, strtotime($string));
        else
            $return = date(self::month);

        return (int)$return;

    }

    public function year($string = null)
    {
        if ($string)
            $return = date(self::year, strtotime($string));
        else
            $return = date(self::year);

        return (int)$return;
    }


    public function day($string = null)
    {
        if ($string)
            $return = date(self::day, strtotime($string));
        else
            $return = date(self::day);

        return (int)$return;
    }


    public function diffMicro($startDate, $endDate)
    {
        $start = \DateTime::createFromFormat('Y-m-d H:i:s.u', $startDate);

        $end = \DateTime::createFromFormat('Y-m-d H:i:s.u', $endDate);

        $diffSeconds = $end->getTimestamp() - $start->getTimestamp();

        $diffMilli = ($end->format('u') - $start->format('u')) / 1000000;

        $diffWithMilli = $diffSeconds + $diffMilli;
        
        return $diffWithMilli;
    }


    public function diff(string $startDate, string $endDate = 'now', bool $interval = false)
    {

        $item = new DateDiffItem();

        if ($interval) {

            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            if ($start > $end)
                $item->expired = true;
            else if ($start->format('u') > $end->format('u'))
                $item->expired = true;
            else
                $item->expired = false;

            $diff = $start->diff($end);

            $item->year = $diff->y;
            $item->month = $diff->m;
            $item->day = $diff->d;
            $item->hour = $diff->h;
            $item->minute = $diff->i;
            $item->second = $diff->s;
            $item->allDays = $diff->days;

            return $item;
        }

        $iStrToTime = strtotime($startDate) - strtotime($endDate);

        if ($iStrToTime < 0)
            $item->expired = false;
        else
            $item->expired = true;

        $iDiff = abs($iStrToTime);


        $item->year = floor($iDiff / (365 * 60 * 60 * 24));
        $item->month = floor($iDiff / (30 * 60 * 60 * 24));
        $item->day = floor($iDiff / (60 * 60 * 24));
        $item->hour = floor($iDiff / (60 * 60));
        $item->minute = floor($iDiff / 60);
        $item->second = floor($iDiff);

        return $item;


    }


    public function dateItem(int $iYear, int $iMonth, int $iDay = null)
    {

        if (empty($iDay)) {
            $iDayStart = '01';
            $iDayEnd = 't';
        } else
            $iDayStart = $iDayEnd = $iDay;


        $item = new DateRangeItem();

        $strStart = strtr(self::dateTime_Item_Start, [
            '{Y}' => Number::paddingLeft($iYear, 4),
            '{m}' => Number::paddingLeft($iMonth, 2),
            '{d}' => Number::paddingLeft($iDayStart, 2)
        ]);

        $strEnd = strtr(self::dateTime_Item_End, [
            '{Y}' => Number::paddingLeft($iYear, 4),
            '{m}' => Number::paddingLeft($iMonth, 2),
            '{d}' => Number::paddingLeft($iDayEnd, 2)
        ]);

        $item->startDate = date($strStart);
        $item->endDate = date($strEnd);

        return $item;
    }


    #region dateHour

    public function dateHourTest()
    {
        $date = $this->dateHour('2020-07-09 10:59:33');
        vd($date);

        $date = $this->dateHour('-1 hours');
        vd($date);

        $date = $this->dateHour('+1 hours');
        vd($date);
    }

    public function dateHour($string = null)
    {
        if ($string)
            return date(self::dateHour, strtotime($string));

        return date(self::dateHour);
    }


    #endregion

    public function dateHourPlus($string = null)
    {
        if ($string)
            $dateHourPlus = date(self::dateHourPlus, strtotime($string));
        else
            $dateHourPlus = date(self::dateHourPlus);


        $hourPlus = (int)$this->hour($string) + 1;
        $hourPlus = ($hourPlus == 24) ? '00' : $hourPlus;
        $hourPlus = Number::paddingLeft($hourPlus, 2);

        $dateHourPlus = str_replace('{}', $hourPlus, $dateHourPlus);
        return $dateHourPlus;
    }


    public function date($string = null)
    {
        if ($string)
            return date(self::date, strtotime($string));

        return date(self::date);
    }




    /**
     *
     *
     * Date And Time
     * @param null $string
     * @return false|string
     */


    #region  dateTime

    public function dateTimeTest()
    {
        $date = $this->dateTime();
        vd($date);

        $date = $this->dateTime('-1 hours');
        vd($date);

        $date = $this->dateTime('+1 hours');
        vd($date);
    }

    public function dateTime($string = null): string
    {
        if ($string)
            return date(self::dateTime, strtotime($string));

        return date(self::dateTime);
    }

    public function fbDateTime($string = null): string
    {
        if ($string)
            return date(self::fbDateTime, strtotime($string));

        return date(self::fbDateTime);
    }

    public function fbDate($string = null): string
    {
        if ($string)
            return date(self::fbDate, strtotime($string));

        return date(self::fbDate);
    }
    #endregion


    public function dateTimeFull()
    {
        return $this->dateTime() . '.' . gettimeofday()["usec"];
    }


    /**
     *
     *
     * Current Date And Times
     */


    public function dateTime_Start($string = null)
    {
        if ($string)
            return date(self::dateTime_Start, strtotime($string));
        return date(self::dateTime_Start);
    }

    public function dateTime_End($string = null)
    {
        if ($string)
            return date(self::dateTime_End, strtotime($string));
        return date(self::dateTime_End);
    }


    public function dateTime_Day_Start($string = null)
    {
        if ($string)
            return date(self::dateTime_Day_Start, strtotime($string));
        return date(self::dateTime_Day_Start);
    }


    public function dateTime_Day_End($string = null)
    {
        if ($string)
            return date(self::dateTime_Day_End, strtotime($string));
        return date(self::dateTime_Day_End);
    }


    public function dateTime_Month_Start($string = null)
    {
        if ($string)
            return date(self::dateTime_Month_Start, strtotime($string));
        return date(self::dateTime_Month_Start);
    }


    public function dateTime_Month_End($string = null)
    {
        if ($string)
            return date(self::dateTime_Month_End, strtotime($string));
        return date(self::dateTime_Month_End);
    }

    public function dateTime_Year_Start($string = null)
    {
        if ($string)
            return date(self::dateTime_Year_Start, strtotime($string));
        return date(self::dateTime_Year_Start);
    }

    public function dateTime_Year_End($string = null)
    {
        if ($string)
            return date(self::dateTime_Year_End, strtotime($string));
        return date(self::dateTime_Year_End);
    }


    #region Firebird

    /**
     *
     *
     * Firebird Date Time
     * @param null $string
     * @return false|string
     */


    public function dateTimeFB($string = null)
    {
        if ($string)
            return date(self::fbDateTime, strtotime($string));
        return date(self::fbDateTime);
    }

    public function dateTimeFB_Day_Start($string = null)
    {
        if ($string)
            return date(self::fbDateTime_Day_Start, strtotime($string));
        return date(self::fbDateTime_Day_Start);
    }

    public function dateTimeFB_Day_End($string = null)
    {
        if ($string)
            return date(self::fbDateTime_Day_End, strtotime($string));
        return date(self::fbDateTime_Day_End);
    }

    public function dateTimeFB_Month_Start($string = null)
    {
        if ($string)
            return date(self::fbDateTime_Month_Start, strtotime($string));
        return date(self::fbDateTime_Month_Start);
    }

    public function dateTimeFB_Month_End($string = null)
    {
        if ($string)
            return date(self::fbDateTime_Month_End, strtotime($string));
        return date(self::fbDateTime_Month_End);
    }


    public function dateTimeFB_Year_Start($string = null)
    {
        if ($string)
            return date(self::fbDateTime_Year_Start, strtotime($string));
        return date(self::fbDateTime_Year_Start);
    }

    public function dateTimeFB_Year_End($string = null)
    {
        if ($string)
            return date(self::fbDateTime_Year_End, strtotime($string));
        return date(self::fbDateTime_Year_End);
    }

    #endregion

    public function extractDate(string $date)
    {
        // 2017_07_15\2017_07_15_22_02_31_906_2002.wav

        $pattern = '/(\d+\_\d+\_\d+)\\\.*/i';
        $replacement = '$1';

        return preg_replace($pattern, $replacement, $date);
    }

    /**
     *
     * Function  addTime
     * @param $time1
     * @param $time2
     * @return  string
     * @throws \Exception
     * @todo For adding two time variables
     * @author Daho
     */
    public function addTime($time1, $time2)
    {
        $t1 = explode(':', (string)$time1);
        $t2 = explode(':', (string)$time2);

        $secs = (int)ZArrayHelper::getValue($t1, 2) + (int)ZArrayHelper::getValue($t2, 2);
        $minutes = (int)ZArrayHelper::getValue($t1, 1) + (int)ZArrayHelper::getValue($t2, 1) + (int)($secs / 60);
        $secs %= 60;
        $hours = (int)ZArrayHelper::getValue($t1, 0) + (int)ZArrayHelper::getValue($t2, 0) + (int)($minutes / 60);
        $minutes %= 60;

        if ($secs % 10 === $secs)
            $secs = "0$secs";

        if ($minutes % 10 === $minutes)
            $minutes = "0$minutes";

        if ($hours % 10 === $hours)
            $hours = "0$hours";

        return "$hours:$minutes:$secs";
    }


}
