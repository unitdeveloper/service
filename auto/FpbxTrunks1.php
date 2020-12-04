<?php

/**
 *
 *
 * Author:  Zoirjon Sobirov
 * https://t.me/zoirjon_sobirov
 *
 */

namespace zetsoft\service\auto;


use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use zetsoft\service\auto\FpbxUser;
use zetsoft\system\kernels\ZFrame;

class FpbxTrunks1 extends ZFrame
{


    function createTrunk(){

        

        $m = new FpbxUser;
        $i = $m->loginFreepbx();

        // Переходим на страницу создания chansip trunk
        $i->openPage("{$m->item->url}/admin/config.php?display=trunks&tech=SIP");

        // Trunk Name
        $i->fillFieldXpath('//*[@id="trunk_name"]', $m->item->trunkName);

        // Hide CallerID
        if ($m->item->isHideCallerID){

            // Hide CallerID YES
            $i->clickByXpath('//*[@id="tgeneral"]/div[2]/div[1]/div/div/div/div[2]/label[1]');

        } else {

            // Hide CallerID NO
            $i->clickByXpath('//*[@id="tgeneral"]/div[2]/div[1]/div/div/div/div[2]/label[2]');

            // Outbound CallerID
            $i->fillFieldXpath('//*[@id="outcid"]', $m->item->outboundCallerID);
        }

        // CID options

        
        // Maximum Channels
        $i->fillFieldXpath('//*[@id="maxchans"]', $m->item->maximumChannels);

        //Asterisk Trunk Dial Options
        // Hide CallerID
        if ($m->item->isHideCallerID){

            // Hide CallerID YES
            $i->clickByXpath('//*[@id="tgeneral"]/div[2]/div[1]/div/div/div/div[2]/label[1]');

        } else {

            // Hide CallerID NO
            $i->clickByXpath('//*[@id="tgeneral"]/div[2]/div[1]/div/div/div/div[2]/label[2]');

            // Outbound CallerID
            $i->fillFieldXpath('//*[@id="outcid"]', $m->item->outboundCallerID);
        }


    }
}
