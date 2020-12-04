<?php

/**
 *
 *
 * Author:  Zoirjon Sobirov
 * https://t.me/zoirjon_sobirov
 *
 */

namespace zetsoft\service\freePBX;


use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use zetsoft\system\kernels\ZFrame;

class PBXwebdriverIsmet extends ZFrame
{
    public $headless = false;

    public $driver;

    public function config() {
        $desiredCapabilities = DesiredCapabilities::chrome();

        if ($this->headless) {
            $options = new ChromeOptions();
            $options->addArguments(['headless']);
            $desiredCapabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);
        }

        $this->driver = RemoteWebDriver::create('http://localhost:9515', $desiredCapabilities);
    }

    public function autoLogin(){
        $login = 'admin';
        $password = 'Production';
        $url = 'http://10.10.3.60/admin/config.php';

        $this->driver->get($url);
        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getCurrentURL() . "'\n";
        $this->driver->findElement(WebDriverBy::id('login_admin'))->click();
        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getCurrentURL() . "'\n";
        $mylogin =  $this->driver->findElement(WebDriverBy::name('username')); // find search input element


        $mypass =  $this->driver->findElement(WebDriverBy::name('password')); // find search input element

        vdd($mylogin);

        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Welcome to FreePBX')
        );
        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getCurrentURL() . "'\n";
//        Wait for something
//        $this->driver->wait()->until(
//            WebDriverExpectedCondition::elementTextContains(WebDriverBy::id('loginform-login'), $login)
//        );
        //$this->driver->quit();
    }
}
