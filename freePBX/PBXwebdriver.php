<?php

/**
 *
 *
 * Author:  Zoirjon Sobirov
 * https://t.me/zoirjon_sobirov
 *
 */

namespace zetsoft\service\freePBX;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use zetsoft\system\kernels\ZFrame;

class PBXwebdriver extends ZFrame
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


        //Вписываем Логин
        $this->driver->findElement(WebDriverBy::cssSelector('#ui-id-1 #loginform .form-group input[name=username]'))->sendKeys($login);

        //Вписываем Пароль
        $this->driver->findElement(WebDriverBy::cssSelector('#ui-id-1 #loginform .form-group input[name=password]'))->sendKeys($password);
        
        //Кликаю по кнопке Логин (Вхожу)
        $this->driver->findElement(WebDriverBy::cssSelector('.ui-dialog-buttonset button.ui-button.ui-corner-all.ui-widget:first-child'))->click();




        /*$this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains('Welcome to FreePBX')
        );*/
        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getCurrentURL() . "'\n";




        /*$this->driver->findElement(WebDriverBy::cssSelector(".dropdown.admin-btn > .btn.dropdown-toggle.nav-button"))->click();*/
        
    }

   public function createExtension(){

        //Перехожу во вкладку с созданными номерами (Extensions)  Get запросом 
       $this->driver->get("http://10.10.3.60/admin/config.php?display=extensions");

        
       /*$this->driver->findElement(WebDriverBy::cssSelector('#ui-id-1 #loginform .form-group input[name=username]'));
        */

    }
}
