<?php

/**
 * Class    LibreOffice
 * @package zetsoft\service\office
 *
 * @author Asror Zakirov
 * @license DilshodKhudoyarov
 *
 * http://sypexgeo.net/
 * Sypex Geo - bu Sypex Dumper-ning ishlab chiquvchilari tomonidan IP-manzil bo'yicha foydalanuvchi
 * joylashuvini aniqlash uchun mo'ljallangan mahsulot.
 */

namespace zetsoft\service\geo;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Sypex extends ZFrame
{
    /**
     *
     * Function  getInformationByIp
     * https://sypexgeo.net/ limit 30 000 requests per month
     */

    public function test()
    {
        Az::$app->request->userIP;
        $this->getInformationByIpTest();
    }

    public function getInformationByIp(string $ip)
    {
        $return = [];
        if ($ip === '127.0.0.1') {
            $return['city'] = 'Локальный компьютер';
            $return['lat'] = 'Локальный компьютер';
            $return['lon'] = 'Локальный компьютер';
            $return['region'] = 'Локальный компьютер';
            $return['timezone'] = 'Локальный компьютер';
            $return['utc'] = 'Локальный компьютер';
            $return['country'] = 'Локальный компьютер';
            $return['iso'] = 'UZ';
            return $return;
        }
        elseif ($ip !== null) {
            $request = file_get_contents("http://api.sypexgeo.net/json/" . $ip);
            $array = json_decode($request);

            $return['city'] = $array->city->name_ru;
            $return['lat'] = $array->city->lat;
            $return['lon'] = $array->city->lon;
            $return['region'] = $array->region->name_ru;
            $return['timezone'] = $array->region->timezone;
            $return['utc'] = $array->region->utc;
            $return['country'] = $array->country->name_ru;
            $return['iso'] = $array->country->iso;

            return $return;
        }
        //vdd($return);
    }

    public function getInformationByIpTest()
    {
        $ip = '94.158.52.244'; // enter here path
        $res = Az::$app->geo->sypex->getInformationByIp($ip);
        vd($res);
    }

}
