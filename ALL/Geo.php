<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\ALL;

use zetsoft\service\geo\BigDataCloud;
use zetsoft\service\geo\ExtremeIp;
use zetsoft\service\geo\FreePbx;
use zetsoft\service\geo\Geocoder;
use zetsoft\service\geo\Geodecoder;
use zetsoft\service\geo\GeoIp;
use zetsoft\service\geo\Geoip2;
use zetsoft\service\geo\Ip2Loacation;
use zetsoft\service\geo\IpApi2;
use zetsoft\service\geo\IpGeoLocation;
use zetsoft\service\geo\IpInfoDb;
use zetsoft\service\geo\IpInfos;
use zetsoft\service\geo\IpStack;
use zetsoft\service\geo\SxGeo;
use zetsoft\service\geo\Sypex;
use zetsoft\service\geo\SypexGeo;
use zetsoft\service\geo\UapPhp;
use yii\base\Component;



/**
 *
* @property BigDataCloud $bigDataCloud
* @property ExtremeIp $extremeIp
* @property FreePbx $freePbx
* @property Geocoder $geocoder
* @property Geodecoder $geodecoder
* @property GeoIp $geoIp
* @property Geoip2 $geoip2
* @property Ip2Loacation $ip2Loacation
* @property IpApi2 $ipApi2
* @property IpGeoLocation $ipGeoLocation
* @property IpInfoDb $ipInfoDb
* @property IpInfos $ipInfos
* @property IpStack $ipStack
* @property SxGeo $sxGeo
* @property Sypex $sypex
* @property SypexGeo $sypexGeo
* @property UapPhp $uapPhp

 */

class Geo extends Component
{

    
    private $_bigDataCloud;
    private $_extremeIp;
    private $_freePbx;
    private $_geocoder;
    private $_geodecoder;
    private $_geoIp;
    private $_geoip2;
    private $_ip2Loacation;
    private $_ipApi2;
    private $_ipGeoLocation;
    private $_ipInfoDb;
    private $_ipInfos;
    private $_ipStack;
    private $_sxGeo;
    private $_sypex;
    private $_sypexGeo;
    private $_uapPhp;

    
    public function getBigDataCloud()
    {
        if ($this->_bigDataCloud === null)
            $this->_bigDataCloud = new BigDataCloud();

        return $this->_bigDataCloud;
    }
    

    public function getExtremeIp()
    {
        if ($this->_extremeIp === null)
            $this->_extremeIp = new ExtremeIp();

        return $this->_extremeIp;
    }
    

    public function getFreePbx()
    {
        if ($this->_freePbx === null)
            $this->_freePbx = new FreePbx();

        return $this->_freePbx;
    }
    

    public function getGeocoder()
    {
        if ($this->_geocoder === null)
            $this->_geocoder = new Geocoder();

        return $this->_geocoder;
    }
    

    public function getGeodecoder()
    {
        if ($this->_geodecoder === null)
            $this->_geodecoder = new Geodecoder();

        return $this->_geodecoder;
    }
    

    public function getGeoIp()
    {
        if ($this->_geoIp === null)
            $this->_geoIp = new GeoIp();

        return $this->_geoIp;
    }
    

    public function getGeoip2()
    {
        if ($this->_geoip2 === null)
            $this->_geoip2 = new Geoip2();

        return $this->_geoip2;
    }
    

    public function getIp2Loacation()
    {
        if ($this->_ip2Loacation === null)
            $this->_ip2Loacation = new Ip2Loacation();

        return $this->_ip2Loacation;
    }
    

    public function getIpApi2()
    {
        if ($this->_ipApi2 === null)
            $this->_ipApi2 = new IpApi2();

        return $this->_ipApi2;
    }
    

    public function getIpGeoLocation()
    {
        if ($this->_ipGeoLocation === null)
            $this->_ipGeoLocation = new IpGeoLocation();

        return $this->_ipGeoLocation;
    }
    

    public function getIpInfoDb()
    {
        if ($this->_ipInfoDb === null)
            $this->_ipInfoDb = new IpInfoDb();

        return $this->_ipInfoDb;
    }
    

    public function getIpInfos()
    {
        if ($this->_ipInfos === null)
            $this->_ipInfos = new IpInfos();

        return $this->_ipInfos;
    }
    

    public function getIpStack()
    {
        if ($this->_ipStack === null)
            $this->_ipStack = new IpStack();

        return $this->_ipStack;
    }
    

    public function getSxGeo()
    {
        if ($this->_sxGeo === null)
            $this->_sxGeo = new SxGeo();

        return $this->_sxGeo;
    }
    

    public function getSypex()
    {
        if ($this->_sypex === null)
            $this->_sypex = new Sypex();

        return $this->_sypex;
    }
    

    public function getSypexGeo()
    {
        if ($this->_sypexGeo === null)
            $this->_sypexGeo = new SypexGeo();

        return $this->_sypexGeo;
    }
    

    public function getUapPhp()
    {
        if ($this->_uapPhp === null)
            $this->_uapPhp = new UapPhp();

        return $this->_uapPhp;
    }
    


}
