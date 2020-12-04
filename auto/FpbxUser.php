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

class FpbxUser extends ZFrame
{
    public $item;

    public function editUser()
    {
         $i = $this->loginFreepbx();
         /*
         *  Edit user
         *
         */
         
         // Переходим на страницу user manager
         $i->openPage('http://10.10.3.60/admin/config.php?display=userman');

         // Ищем юзер в поиске
         $i->fillFieldXpath('//*[@id="users"]/div/div[1]/div[1]/div[3]/input', $this->item->extName);

         sleep(2);

         //Проверяем есть ли такой extension
         if ( $i->getAttributeValueByXpath('//*[@id="table-users"]/tbody/tr', 'class') === 'no-records-found' )
             echo 'Нет такого юзера';
         else {

             $i->clickByXpath('//*[@id="table-users"]/tbody/tr/td[9]/a[1]/i');

             if ($i->getAttributeValueByXpath('//*[@id="username"]', 'value') === $this->item->extName){
                 echo 'Good job';
             }  else{
                 echo 'Нет такого юзера';
             }

         }

    }
    public function loginFreepbx()
    {
        $this->item = new PBXExtItem();
        $this->item->login = "admin";
        $this->item->password = "adminfreepbx";
        $this->item->url = "http://10.10.3.60";
        $this->item->extName = "616";
        $this->item->extPassword = "616";

        //admin page
        $this->item->adminName = "test2";
        $this->item->adminPassword = "test2";

        //Trunk
        $this->item->trunkName = 'name';
        $this->item->isHideCallerID = false;
        $this->item->outboundCallerID = '712078030';
        $this->item->maximumChannels = '';



        $i = new Driver(['headless'=> false]);


        //Driver(['headless' => true]);
        $i->openPage("{$this->item->url}/admin/config.php");

        echo "The current URI is '" . $i->getUrl() . "'\n";

        //Нажымаем на administrator
        $i->click('#login_admin');


        //Вписываем Логин
        $i->fillField('#ui-id-1 #loginform .form-group input[name=username]', $this->item->login);

        //Вписываем Пароль
        $i->fillField('#ui-id-1 #loginform .form-group input[name=password]', $this->item->password);

        //Кликаю по кнопке Логин (Вхожу)
        $i->click('.ui-dialog-buttonset button.ui-button.ui-corner-all.ui-widget:first-child');

        return $i;

    }

    public function createUser()
    {

     $i = $this->loginFreepbx();

     $i->openPage('https://google.ru');



    }

}
