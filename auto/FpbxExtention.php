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

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use zetsoft\dbitem\pbx\PBXExtItem;
use zetsoft\models\user\User;
use zetsoft\service\auto\Driver;
use zetsoft\service\auto\FpbxUser;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class FpbxExtention extends ZFrame
{
    #region  Vars
    public $item;

    public $driver;
    #endregion


    #region Cores
    public function init()
    {
        parent::init();

        $this->driver = new Driver(['headless' => false]);
    }

    #endregion


    public function createExtensionTest()
    {

    }

    public function loginFreepbxTest()
    {

        $this->item = new PBXExtItem();

        $this->item->extName = "34234234";
        
        $this->item->extPassword = "34234234";
        
       $this->createExtension();
    }


    public function loginFreepbx()
    {
        //Driver(['headless' => true]);
        $this->driver->openPage("{$this->item->url}/admin/config.php");

        echo "The current URI is '" . $this->driver->getUrl() . "'\n";

        //Нажымаем на administrator
        $this->driver->click('#login_admin');

        //Вписываем Логин
        $this->driver->fillField('#ui-id-1 #loginform .form-group input[name=username]', $this->item->login);

        //Вписываем Пароль
        $this->driver->fillField('#ui-id-1 #loginform .form-group input[name=password]', $this->item->password);

        //Кликаю по кнопке Логин (Вхожу)
        $this->driver->click('.ui-dialog-buttonset button.ui-button.ui-corner-all.ui-widget:first-child');
        

    }


    public function createExtension()
    {

        /*
         * Auto login:
         *
         */

        $this->loginFreepbx();

        /*
         * *
         * *    Create pjextension:
         * *
         */


        //Перехожу во вкладку с созданием номера во вкладку advanced (Extensions)  Get запросом
        $this->driver->openPage("{$this->item->url}/admin/config.php?display=extensions&tech_hardware=pjsip_generic#advanced");


        $a = $this->driver->executeScript('return document.readyState');

        vdd($a);
        
        //Нажимаю на selector Media encrytion
        $this->driver->click('#devinfo_media_encryption');


        //Меняю Media Encrytion на Dtls
        $this->driver->clickByXpath('//*[@id="devinfo_media_encryption"]/option[3]');

        //Включаю Dtls
        $this->driver->click('label[for="dtls_enable1"]');

        //Перехожу во вкладку General
        $this->driver->executeScript(" $( \"a[href='#general']\" ).trigger( \"click\" )");

        //Задаем номер экст
        $this->driver->fillField('#extension', $this->item->extName);

        //Подождем 2 секунды
        sleep(2);

        //Проверяем есть ли такой номер (extension)
        if (empty($this->driver->findByClass('.duplicate-exten'))) {
            //Задаем Display name
            $this->driver->fillField('#name', $this->item->extName);

            //Удаляем значение из поля парол
            $this->driver->clearByClass('#devinfo_secret');

            //Задаем парол
            $this->driver->fillField('#devinfo_secret', $this->item->extPassword);

            //Жму Submit Отправляю запрос
            $this->driver->click('#submit');


      
            sleep(15);

            // Переходим на страницу редактор номера
            $this->driver->openPage("{$this->item->url}/admin/config.php?display=extensions&extdisplay={$this->item->extName}#advanced");

            //Меняем pjsip на chansip
            $this->driver->clickByXpath('//*[@id="devinfo_changecdriver"]');

            //Нажымаем Yes (Alert)
            $this->driver->acceptAlert();

            sleep(5);
            //Перехожу во вкладку Advanced
            $this->driver->clickByXpath('//*[@id="frm_extensions"]/div/div[1]/div[3]/ul/li[4]/a');

            //Переключаем user = Phone на yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[8]/div[1]/div/div/div/div[2]/span/label[2]');

            //Переключаем Transport на All WSS primary
            $this->driver->clickByXpath('//*[@id="devinfo_transport"]/option[5]');

            //Enable AVPF yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[17]/div[1]/div/div/div/div[2]/span/label[2]');

            //Force AVP yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[18]/div[1]/div/div/div/div[2]/span/label[2]');


            //Enable ICE Support tes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[19]/div[1]/div/div/div/div[2]/span/label[2]');

            //Enable rtcp Mux yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[20]/div[1]/div/div/div/div[2]/span/label[2]');

            //Enable Encryption yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[21]/div[1]/div/div/div/div[2]/span/label[2]');

            //Video Support yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[4]/div[22]/div[1]/div/div/div/div[2]/span/label[2]');

            //Inbound External Calls  'Force'
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[8]/div[1]/div[1]/div/div/div/div[2]/span/label[1]');

            //Outbound External Calls 'Force'
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[8]/div[2]/div[1]/div/div/div/div[2]/span/label[1]');

            //Inbound Internal Calls 'Force'
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[8]/div[3]/div[1]/div/div/div/div[2]/span/label[1]');

            //Outbound Internal Calls 'Force'
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[8]/div[4]/div[1]/div/div/div/div[2]/span/label[1]');

            //Call Waiting Tone yes
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[6]/div[7]/div[1]/div/div/div/div[2]/span/label[1]');

            //Запись звонков
            $this->driver->clickByXpath('//*[@id="advanced"]/div/div[8]/div[5]/div[1]/div/div/div/div[2]/span/label[2]');

            //Нажымаем submit
            $this->driver->clickByXpath('//*[@id="submit"]');

            //Нажымаем кнопку apply
            $this->driver->clickByXpath('//*[@id="button_reload"]');

        }
    }

    public function deleteExtension()
    {

        $this->loginFreepbx();

        $this->driver->openPage('http://10.10.3.60/admin/config.php?display=extensions');

        //ищу в поиске extension
        $this->driver->fillFieldXpath('//*[@id="alldids"]/div[1]/div[1]/div[3]/input', $this->item->extName);

        // жду загрузки поиска (jquery долго ищет)
        sleep(1);


        //создаем переменную для проверки условия
        $checkForEmty = !empty($this->driver->findByClass("a[href='?display=extensions&extdisplay={$this->item->extName}']"));

        //проверяю есть ли Extension
        if ($checkForEmty) {
            //если есть жмем кнопку удалить
            $this->driver->click("a.clickable.delete[data-id='{$this->item->extName}']");
            $this->driver->acceptAlert();
        } else {
            //если нет то вывожу в консоль то что Extension не найден  
            echo "{$this->item->extName} Extension not found";
        };

    }

    public function createAllExtentions()
    {
        $agents = User::find()
            ->where(['role' => 'agent'])
            ->all();

            
        /** @var User $agent */
        foreach ($agents as $agent) {
             if(!empty($agent->number) && !emtpy($agent->extpass)){
                 $item = new PBXExtItem();
                 $this->item = $item;
                 $item->extName = $agent->number;
                 $item->extPassword = $agent->extpass;
                 $this->createExtension();
             }
        }
        

    }



}
        

                













        





        


        
        




