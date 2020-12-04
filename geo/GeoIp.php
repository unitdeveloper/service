<?php

/**
 * Author:  Xolmat Ravshanov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\geo;

use zetsoft\system\kernels\ZFrame;

class GeoIp extends ZFrame
{
   //228a27f772482b6e2460bdcd81c7137c7c6f2269049b6828e9a84e6ea3b0d1b4
    #region Vars

    public $database = Root . '\vendor/torann/geoip/resources/geoip.mmdb';

    #endregion

    #region Event

    public function test($ip)
    {

        vdd($this->get($ip));

    }

    public function getInfo()
    {
        $sxGeo = new SxGeo(Root . '\binary\geoip\sxgeo\country\SxGeo.dat');
        $ip = "94.158.52.244";
        $return = [];
        $return[] = $sxGeo->get($ip);
        $return[] = $sxGeo->getCountry($ip);
        $return[] = $sxGeo->getCountryId($ip);
        $return[] = $sxGeo->getCity($ip);
        $return[] = $sxGeo->getCityFull($ip);
        $return[] = $sxGeo->about();
        return $return;
    }

     #endregion
}
