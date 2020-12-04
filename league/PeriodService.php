<?php
/**
 * Class    PeriodService
 * @package zetsoft\service\league
 *
 * @author DilshodKhudoyarov
 *
 * https://github.com/pwm/datetime-period
 * PeriodService Vaqtinchalik intervallar bilan ishlash uchun vaqt oralig'i turini amalga oshirish uchun
 */

namespace zetsoft\service\league;
use DateTime;
use DateTimeImmutable;
use League\Period\Sequence;
use zetsoft\system\kernels\ZFrame;
use League\Period\Period;

class PeriodService extends ZFrame
{

    public function test_case() {
        $this->accessing_propertiesTest();
    }

    public function accessing_propertiesTest() {
        $start_date = '2020-08-04 14:23:37';
        $end_date = '2020-08-04 16:35:49';
        $result=$this->accessing_properties($start_date, $end_date);
        vd($result);
    }

    public function accessing_properties($start_date, $end_date) {
        $interval = new Period(
            new DateTime($start_date),//'2014-10-03 08:12:37' -> example
            new DateTimeImmutable($end_date), //2014-10-03 08:12:37 -> example
    Period::INCLUDE_START_EXCLUDE_END
);
       $start = $interval->getStartDate(); //returns a DateTimeImmutable
       $end = $interval->getEndDate();     //returns a DateTimeImmutable
       $duration = $interval->getDateInterval();       //returns a DateInterval object
       $duration2 = $interval->getTimestampInterval(); //returns the duration in seconds
       echo $interval; //displays '2014-10-03T08:12:37Z/2014-10-03T09:12:37Z'
    }

    public function getter_period_info($start_date, $end_date) {
        $period = new Period($start_date, //'2012-04-01 08:30:25' -> example
                  new DateTime($end_date)); //'2013-09-04 12:35:21' -> example

        $period->getStartDate(); //returns DateTimeImmutable('2012-04-01 08:30:25');
        $period->getEndDate(); //returns DateTimeImmutable('2013-09-04 12:35:21');
        $duration = $period->getDateInterval(); //returns a DateInterval object
        $altduration = $period->getTimestampInterval(); //returns the duration in seconds
        $period->getBoundaryType(); //returns Period::INCLUDE_START_EXCLUDE_END
        $period->isStartExcluded(); //returns false
        $period->isStartIncluded(); //returns true
        $period->isEndExcluded(); //returns true
        $period->isEndIncluded(); //returns false
        vd($period);
    }

    public function json_representation($place_name, $start_date, $end_date) {
        date_default_timezone_set($place_name); //'Africa/Kinshasa' -> example

        $period = new Period($start_date, //'2019-05-01 00:00:00' -> example
                             $end_date); // '2020-08-04 00:00:00' -> example
        vd($period);

        $res = json_decode(json_encode($period), true);

        vd($res);
//  $res will be equivalent to:
// [
//      'startDate' => '2014-04-30T23:00:00.000000Z,
//      'endDate' => '2014-05-07T23:00:00.000000Z',
// ]
    }

    public function adding_intervals($start_date, $end_date) {
        $sequence = new Sequence(new Period($start_date, // '2018-01-01' ->example
                                            $end_date)); // '2018-01-31' ->example
        $sequence->get(0)->format('Y-m-d'); // [2018-01-01, 2018-01-31)
        $sequence->push(
            new Period($start_date, $end_date),
            new Period($start_date, $end_date),
            new Period($start_date, $end_date),
            new Period($start_date, $end_date),
        );
        $sequence->get(0)->format('Y-m-d'); // [2018-01-01, 2018-01-31)
        $sequence[] = new Period($start_date, // '2018-12-20' ->example
                                 $end_date); // '2018-12-21' ->example
        $sequence[4]->format('Y-m-d'); // [2018-12-20, 2018-12-21)
        vd($sequence);
    }

}