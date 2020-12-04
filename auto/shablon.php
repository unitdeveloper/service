<?php

/**
 *Author:  Saidakbarov Fayzullo
 *         Ergashev Xakimjon
 *         Kadyrov Ismet
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
use zetsoft\system\kernels\ZFrame;
use Facebook\WebDriver;

class shablon extends ZFrame
{
    public $headless = false;
    
    public $webDriver;
    
    public function config() {
        $desiredCapabilities = DesiredCapabilities::chrome();

        if ($this->headless) {
            $options = new ChromeOptions();
            $options->addArguments(['headless']);
            $desiredCapabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);
        }

        $this->webDriver = RemoteWebDriver::create('http://localhost:9515', $desiredCapabilities);
    }


    public function createAdmin()
    {

        

        $this->webDriver->close();
    }
}
