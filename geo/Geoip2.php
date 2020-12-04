<?php

/*
 * Author: Axrorbek Nisonboyev
 *          Aziz Ro'zmetov
 *
 *
 * */
namespace zetsoft\service\geo;
use GeoIp2\Database\Reader;

use zetsoft\system\kernels\ZFrame;


class Geoip2 extends ZFrame
{
    #region Vars

    public $databaseFile = Root . '\vendor\GeoIP2-City.mmdb';

    #endregion

    public function test($ip){
        vdd($this->getInfos($ip));
    }
    public function getInfos($ip){
        $reader = new Reader($this->databaseFile);
        $record = $reader->city($ip);

        $countryIsoCode = $record->country->isoCode;
        // 'US'
        $countryName = $record->country->name;
        // 'United States'
        $stateName = $record->mostSpecificSubdivision->name;
        // 'Minnesota'
        $cityName = $record->city->name;
        // 'minneapolis'
        $cityIsoCode = $record->mostSpecificSubdivision->isoCode;
        // 'MN'
        $postalCode = $record->postal->code;
        // '55455'
        $locLatitude = $record->location->latitude;
        // 44.9733
        $locLongtitude = $record->location->longitude;
        // -93.2323
        $network = $record->traits->network;
        // '128.101.101.101/32'
        $countryNames = $record->country->nameOn;
        /*print(['zh-CN'] . "\n"); // '美国'*/
        $datas = [
            'nameOn' => $countryNames,
            'countryIsoCode' => $countryIsoCode,
            'countryName' => $countryName,
            'stateName' => $stateName,
            'cityName' => $cityName,
            'cityIsoCode' => $cityIsoCode,
            'postalCode' => $postalCode,
            'locLatitude' => $locLatitude,
            'locLongtitude' => $locLongtitude,
            'network' => $network,
        ] ;

        $reader->close();
        return $datas;
    }





}



