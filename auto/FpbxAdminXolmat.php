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
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class FpbxAdminXolmat extends ZFrame
{
    public $item;
    public $driver;
    public $name;

    public function init()
    {
        parent::init();
        
        //Driver(['headless' => true]);
        $this->driver = new Driver(['headless'=> true]);
        

    }


    public function createAdminTest(){
        $this->item = new PBXExtItem();
        
        $this->item->extName = "602";
        $this->item->extPassword = "602";

        
        $this->item->adminName = "test2";
        $this->item->adminPassword = "test2";
    }


    public function createAdmin()
    {

        $this->driver->openPage("{$this->item->url}/admin/config.php");

        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getUrl() . "'\n";

        $this->driver->click('#login_admin');

        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getUrl() . "'\n";

        //Вписываем Логин
        $this->driver->fillField('#ui-id-1 #loginform .form-group input[name=username]', $this->item->login);

        //Вписываем Пароль
        $this->driver->fillField('#ui-id-1 #loginform .form-group input[name=password]', $this->item->password);

        //Кликаю по кнопке Логин (Вхожу)
        $this->driver->click('.ui-dialog-buttonset button.ui-button.ui-corner-all.ui-widget:first-child');

        echo "The title is '" . $this->driver->getTitle() . "'\n";

        echo "The current URI is '" . $this->driver->getUrl() . "'\n";


        /*
        *  Edit Admin
        *
        */
        

        // Переходим на страницу Admin manager
        $this->driver->openPage('http://10.10.3.60/admin/config.php?display=ampusers');

        // Вводим username и password

        $this->driver->fillField('#username', $this->item->adminName);
        $this->driver->click('#password');
        $this->driver->fillField('#password', $this->item->adminPassword);

        echo $this->item->adminPassword;


        //Кликаю по кнопке Submit (Создаю)
        $this->driver->clickByXpath('//*[@id="submit"]');
        
    }



    public function deleteAdmin(){





        /*
        *  Delete Admin
        *
        */

        // Переходим на страницу Admin manager
        $this->driver->openPage('http://10.10.3.60/admin/config.php?display=ampusers');

        //
        $this->driver->clickByXpath('//*[@id="fixed-list-button"]');

        sleep(1);

        //Получаем список name админов в строку
        echo $listString = $this->driver->getTextByXpath('//*[@id="table-all-side"]/tbody', 'class');



        /*// Разбываем строку на массив
        $listOfAdmins = explode("\n", $listString);

        $num = 4;

        //Выбираем админ которую хотим удалить
        $i->clickByXpath("//*[@id='table-all-side']/tbody/tr[$num]");*/



        // Ищем админ с таким именем
        $this->driver->fillFieldXpath('//*[@id="floating-nav-bar"]/div/div[1]/div[1]/div[2]/input', $this->item->adminName);

        sleep(1);

        if ($this->driver->getTextByXpath('//*[@id="table-all-side"]/tbody/tr/td') === '')
            //Выбираем админ которую хотим удалить
            $this->driver->clickByXpath("//*[@id='table-all-side']/tbody/tr[1]");

        sleep(1);

        // Нажымаем кнопку delete
        $this->driver->clickByXpath('//*[@id="delete"]');

        // Нажымаем alert yes
        $this->driver->acceptAlert();

    }

    public function deleteAdmin2($adminName){


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
        $i->fillFieldXpath('//*[@id="floating-nav-bar"]/div/div[1]/div[1]/div[2]/input', $adminName);

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
