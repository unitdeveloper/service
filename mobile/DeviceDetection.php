<?php
/**
 * Class    DeviceDetection
 * @package zetsoft\service\mobile
 *
 * @author DilshodKhudoyarov
 *
 * Class file hamma device informatsiyalani chiqarib beradi
 */

namespace zetsoft\service\mobile;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
use DeviceDetector\Parser\Bot as BotParser;

class DeviceDetection extends ZFrame
{
    public $client;


    public function init()
    {
        parent::init();
        $this->client = new DeviceDetector();

    }


    public function checkMobile(){
        return $this->client->isMobile();
    }

        

    public function  checkDesktop(){
        return $this->client->isDesktop();
    }



    #region Test
    public function test_case()
    {
        $this->detect_all_infoTest();
        $this->checking_is_user_botTest();
    }

    #endregion

    public function detect_all_infoTest()
    {
        $userAgent = 'enter a useragent you want to parse here!';
        /***
         * @param $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
         */
        $result = Az::$app->mobile->deviceDetection->detect_all_info($userAgent);
        vd($result);
    }

    public function detect_all_info($userAgent)
    {

        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
        /**
         * @param $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
         */

        $dd = new DeviceDetector($userAgent);

        $dd->parse();

        if ($dd->isBot()) {
            // handle bots,spiders,crawlers,...
            $botInfo = $dd->getBot();
            vdd($botInfo);
        } else {
            $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
            $osInfo = $dd->getOs();
            $device = $dd->getDeviceName();
            $brand = $dd->getBrandName();
            $model = $dd->getModel();
            vdd($clientInfo, $osInfo, $device, $brand, $model);
        }
    }

    public function checking_is_user_botTest()
    {
        $userAgent = 'enter a useragent you want to parse here!';
        // $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
        $result = Az::$app->mobile->deviceDetection->checking_is_user_bot($userAgent);
        vd($result);
    }

    public function checking_is_user_bot($userAgent)
    {

        /**
         * @param $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse
         */
        $botParser = new BotParser();
        $botParser->setUserAgent($userAgent);

        /**
         * OPTIONAL: discard bot information. parse() will then return true instead of information
         */
        $botParser->discardDetails();

        $result = $botParser->parse();

        if (!is_null($result)) {
            // do not do anything if a bot is detected
            return;
        }
        // handle non-bot requests
    }

}
