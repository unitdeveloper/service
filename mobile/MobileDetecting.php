<?php

/**
 * class MobileDetecting
 * @package zetsoft/service/mobile
 * @author Uzakbaev Axmet
 * Mobil  qurilmalarini aniqlaydi
 */

namespace zetsoft\service\mobile;

use zetsoft\system\kernels\ZFrame;
use Detection\MobileDetect;

class MobileDetecting extends ZFrame
{
    public $mobile;

    public function init()
    {
        parent::init();
        $this->mobile = new MobileDetect();
    }

    public function checkMobileDevice()
    {
        if ($this->checkMobile() || $this->checkTable() || $this->checkPhone())
            return true;

        return false;
    }

    public function checkMobile()
    {
        return $this->mobile->isMobile();
    }

    public function checkPhone()
    {
        $this->mobile->isIphone();
    }

    public function checkTable()
    {
        return $this->mobile->isTablet();
    }

    #region Test
    public function test_case()
    {
        $this->detectTest();
        $this->isTest();
        $this->setUserAgentTest();
        $this->versionTest();
        $this->setHttpHeaderTest();

    }

    #endregion


    public function detectTest()
    {
        $useragent = ' Mozilla/5.0 (Linux; Android 4.0.4; Desire HD Build/IMM76D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19';
        $url = 'http://www.example.com';
        $httpHeaders = get_headers($url);
        $data = $this->detect($useragent, $httpHeaders);

        vd($data);
    }

    public function detect($userAgent, $httpHeaders)
    {
        $detect = new MobileDetect();
// Any mobile device (phones or tablets).
        if ($detect->isMobile($userAgent, $httpHeaders)) {
            echo 'This is mobile ';
        }
// Any tablet device.
        if ($detect->isTablet($userAgent, $httpHeaders)) {
            echo 'This is Tablet ';
        }
// Exclude tablets.
        if ($detect->isMobile() && !$detect->isTablet($userAgent, $httpHeaders)) {
            echo 'This is Mobile ';
        } else {
            echo "This is Desktop  ";
        }
// Check for a specific platform with the help of the magic methods:
        if ($detect->isiOS($userAgent, $httpHeaders)) {
            echo 'This is IOS ';
        }
        if ($detect->isAndroidOS($userAgent, $httpHeaders)) {
            echo 'This is Android ';
        }
    }

    public function isTest()
    {
        $useragent = ' Mozilla/5.0 (Linux; Android 4.0.4; Desire HD Build/IMM76D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19';
        $url = 'http://www.example.com';
        $httpHeaders = get_headers($url);
        $key = 1;
        $data = $this->is($key, $useragent, $httpHeaders);

        vd($data);
    }

    public function is($key, $userAgent, $httpHeaders)
    {
// Alternative method is() for checking specific properties.
// WARNING: this method is in BETA, some keyword properties will change in the future.
        $detect = new MobileDetect();
        if ($detect->is('Chrome')) {
            echo 'This is Chrome';
        }
        if ($detect->is('iOS')) {
            echo 'This is IOS';
        }
        if ($detect->is('UCBrowser')) {
            echo 'This is UCBrowser';
        }
        if ($detect->is('Opera')) {
            echo 'This is Opera';
        }
    }

    public function setUserAgentTest()
    {
        $useragent = ' Mozilla/5.0 (Linux; Android 4.0.4; Desire HD Build/IMM76D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19';
        $data = $this->setUserAgent($useragent);

        vd($data);
    }

    public function setUserAgent($setUserAgent)
    {
        $detect = new MobileDetect();
        // Batch mode using setUserAgent():
        $detect->setUserAgent($setUserAgent);
    }


    public function versionTest()
    {
        $propertyName = 'Android';
        $type = 1;
        $data = $this->version($propertyName, $type);

        vd($data);
    }

    public function version($propertyName, $type)
    {
        $detect = new MobileDetect();
        // Get the version() of components.
// WARNING: this method is in BETA, some keyword properties will change in the future.

        $detect->version($propertyName, $type);
    }


    public function setHttpHeaderTest()
    {
        $url = 'http://www.example.com';
        $httpHeaders = get_headers($url);
        $data = $this->setHttpHeader($httpHeaders);
        vd($data);
    }

    public function setHttpHeader($httpHeaders)
    {
        $detect = new MobileDetect();
        $detect->setHttpHeaders($httpHeaders);
    }
}

