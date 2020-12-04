<?php

/**
 *
 * Author:  Xolmat Ravshanov
 *
 */

namespace zetsoft\service\auto;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use zetsoft\system\kernels\ZFrame;


class FpbxExtsX extends ZFrame
{
    public $headless = false;
    public $driver;

    public function config()
    {
        $desiredCapabilities = DesiredCapabilities::chrome();

        if ($this->headless) {
            $options = new ChromeOptions();
            $options->addArguments(['headless']);
            $desiredCapabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);
        }

        $this->driver = RemoteWebDriver::create('http://localhost:9515', $desiredCapabilities);
    }

    public function createExtension()
    {
        /*
         * Auto login:
         *
         */
        $login = 'admin';
        $password = 'Production';
        $url = 'http://10.10.3.60/admin/config.php';

        $extName = "387";
        $extPassword = $extName;

        $i = new Driver(['headless' => true]);
        $i->openPage($url);
        echo "The title is '" . $i->getTitle() . "'\n";

        echo "The current URI is '" . $i->getUrl() . "'\n";

        $i->click('#login_admin');

        echo "The title is '" . $i->getTitle() . "'\n";

        echo "The current URI is '" . $i->getUrl() . "'\n";

        //Вписываем Логин
        $i->fillField('#ui-id-1 #loginform .form-group input[name=username]', $login);

        //Вписываем Пароль
        $i->fillField('#ui-id-1 #loginform .form-group input[name=password]', $password);

        //Кликаю по кнопке Логин (Вхожу)
        $i->click('.ui-dialog-buttonset button.ui-button.ui-corner-all.ui-widget:first-child');

        echo "The title is '" . $i->getTitle() . "'\n";

        echo "The current URI is '" . $i->getUrl() . "'\n";


        /*
         * *
         * *    Create pjextension:
         * *
         */

        /* //Перехожу во вкладку с созданными номерами (Extensions)  Get запросом
         //$this->driver->get("http://10.10.3.60/admin/config.php?display=extensions&tech_hardware=pjsip_generic");

         //Перехожу во вкладку с созданием номера во вкладку advanced (Extensions)  Get запросом
         $i->openPage("http://10.10.3.60/admin/config.php?display=extensions&tech_hardware=pjsip_generic#advanced");

         //Нажимаю на selector Media encrytion
         //$i->click('#devinfo_media_encryption');


         //Меняю Media Encrytion на Dtls
         $i->clickByXpath('//*[@id="devinfo_media_encryption"]/option[3]');

         //Включаю Dtls
         $i->click('label[for="dtls_enable1"]');

         //Перехожу во вкладку General
         $i->executeScript(" $( \"a[href='#general']\" ).trigger( \"click\" )");

         //Задаем номер экст
         $i->fillField('#extension', $extName);

         //Подождем 2 секунды
         sleep(2);

         //Проверяем есть ли такой номер (extension)
         if (!empty($i->findByClass('.duplicate-exten'))) {
             echo 'Duplicate exten';
         } else {
             //Задаем Display name
             $i->fillField('#name', $extName);

             //Удаляем значение из поля парол
             $i->clearByClass('#devinfo_secret');

             //Задаем парол
             $i->fillField('#devinfo_secret', $extPassword);

             //Жму Submit Отправляю запрос
             $i->click('#submit');

             sleep(2);

         }*/


        //Переходим на страницу редактор номера
        $i->openPage("$url?display=extensions&extdisplay=$extName#advanced");

        //Меняем pjsip на chansip
        //  $i->clickByXpath('//*[@id="devinfo_changecdriver"]');

        //Нажымаем Yes (Alert)
        //  $i->acceptAlert();

        sleep(5);
        //Перехожу во вкладку Advanced
        // $i->clickByXpath('//*[@id="frm_extensions"]/div/div[1]/div[3]/ul/li[4]/a');

        //Переключаем user = Phone на yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[8]/div[1]/div/div/div/div[2]/span/label[2]');

        //Переключаем Transport на All WSS primary
        $i->clickByXpath('//*[@id="devinfo_transport"]/option[5]');

        //Enable AVPF yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[17]/div[1]/div/div/div/div[2]/span/label[2]');

        //Force AVP yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[18]/div[1]/div/div/div/div[2]/span/label[2]');

        //Enable ICE Support tes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[19]/div[1]/div/div/div/div[2]/span/label[2]');

        //Enable rtcp Mux yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[20]/div[1]/div/div/div/div[2]/span/label[2]');

        //Enable Encryption yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[21]/div[1]/div/div/div/div[2]/span/label[2]');

        //Video Support yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[4]/div[22]/div[1]/div/div/div/div[2]/span/label[2]');

        //Call Waiting Tone yes
        $i->clickByXpath('//*[@id="advanced"]/div/div[6]/div[7]/div[1]/div/div/div/div[2]/span/label[1]');

        //
    }
    
    public function deleteExtension(){
        $extName = "395";

        $this->driver->findElement(WebDriverBy::cssSelector('#submit'))->click();

        if (count($this->driver->findElements(WebDriverBy::xpath("image-e4e"))) === 0) {
            echo 'not found';
        }
        else{
            echo "true";
        }
    }


    public function triggerHref($hrefAttr){
        //метод для быстрого перемещения по вкладкам
        $this->driver->executeScript(" $( \"a[href=\"+arguments[0]+\"]\" ).trigger( \"click\" )",[$hrefAttr]) ;

    }
}































