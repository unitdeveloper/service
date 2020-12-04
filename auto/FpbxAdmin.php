<?php

/**
 *
 *
 * Author:  Saidakbarov Fayzullo
 *          Ergashev Xakimjon
 *          Kadyrov Ismet
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
use zetsoft\dbitem\pbx\PBXExtItem;
use zetsoft\system\kernels\ZFrame;
use Facebook\WebDriver;

class FpbxAdmin extends ZFrame
{
    public $item;
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

    public function createAdmin(){
        $this->item = new PBXExtItem();
        // open | http://10.10.3.60/admin/config.php |
        $this->webDriver->get("{$this->item->url}");
        //  | id=login_admin |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("login_admin"))->click();
      
        // click | xpath=(//input[@name='username'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='username'])[2]"))->click();
        // type | xpath=(//input[@name='username'])[2] | admin
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='username'])[2]"))->sendKeys("{$this->item->login}");
        // click | xpath=(//input[@name='password'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='password'])[2]"))->click();
        // type | xpath=(//input[@name='password'])[2] | adminfreepbx
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='password'])[2]"))->sendKeys("{$this->item->password}");
        // click | xpath=(//button[@type='button'])[3] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//button[@type='button'])[3]"))->click();
        // click | link=Admin |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Admin"))->click();
        // click | link=Administrators |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Administrators"))->click();
       

        // click | id=username |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("username"))->click();
        // click | id=username |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("username"))->click();
        // type | id=username | te
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("username"))->sendKeys("{$this->item->adminName}");
        // click | id=password |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("password"))->click();
        // type | id=password | te
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("password"))->sendKeys("{$this->item->adminPassword}");
        // click | id=submit |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("submit"))->click();
        //$this->webDriver->close();
    }

    public function createAdmin1()
    {
        
        $m = new FpbxUser;
        $i = $m->loginFreepbx();


        /*
        *  Edit Admin
        *
        */

        // Переходим на страницу Admin manager
        $i->openPage('http://10.10.3.60/admin/config.php?display=ampusers');

        // Вводим username и password

        $i->fillField('#username', $m->item->adminName);
        $i->click('#password');
        $i->fillField('#password', $m->item->adminPassword);

        echo $m->item->adminPassword;


        //Кликаю по кнопке Submit (Создаю)
        $i->clickByXpath('//*[@id="submit"]');
        
    }

    public function deleteAdmin(){


        $m = new FpbxUser;
        $i = $m->loginFreepbx();



        /*
        *  Delete Admin
        *
        */

        // Переходим на страницу Admin manager
        $i->openPage('http://10.10.3.60/admin/config.php?display=ampusers');
        
        $i->clickByXpath('//*[@id="fixed-list-button"]');

        sleep(1);


     /*
        //Получаем список name админов в строку
        echo $listString = $i->getTextByXpath('//*[@id="table-all-side"]/tbody', 'class');

        // Разбываем строку на массив
        $listOfAdmins = explode("\n", $listString);

        vdd($listOfAdmins);
        $num = 2;

        //Выбираем админ которую хотим удалить 
        $i->clickByXpath("//*[@id='table-all-side']/tbody/tr[$num]");


   */
        // Ищем админ с таким именем
         $i->fillFieldXpath('//*[@id="floating-nav-bar"]/div/div[1]/div[1]/div[2]/input', $m->item->adminName);

        sleep(1);

        // Проверяем есть ли admin с таким именем
        if ($i->getAtgetAttributeValueByXpath('//*[@id="table-all-side"]/tbody/tr', 'class') === 'no-records-found'){
            echo 'admin с таким именем не найден.';
        } else {
            //Выбираем админ которую хотим удалить
            $i->clickByXpath("//*[@id='table-all-side']/tbody/tr[1]");

            sleep(1);

            // Нажымаем кнопку delete
            $i->clickByXpath('//*[@id="delete"]');

            // Нажымаем alert yes
            $i->acceptAlert();


        }
           
    }

}
