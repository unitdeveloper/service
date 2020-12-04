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
use zetsoft\service\auto\Driver;
use zetsoft\service\auto\FpbxUser;
use zetsoft\system\kernels\ZFrame;


class FpbxExts extends ZFrame
{
    
    public function createExtensionTest(){

        $this->item = new PBXExtItem();
        $this->item->login = 'kfjskd';


   }

    public function createExtension()
    {

    /*
     * Auto login:
     *
     */

        $m = new FpbxUser;
        $i = $m->loginFreepbx();
        

    /*
     * *
     * *    Create pjextension:
     * *
     */


        //Перехожу во вкладку с созданием номера во вкладку advanced (Extensions)  Get запросом
        $i->openPage("{$m->item->url}/admin/config.php?display=extensions&tech_hardware=pjsip_generic#advanced");

        //Нажимаю на selector Media encrytion
        $i->click('#devinfo_media_encryption');


        //Меняю Media Encrytion на Dtls
        $i->clickByXpath('//*[@id="devinfo_media_encryption"]/option[3]');

        //Включаю Dtls
        $i->click('label[for="dtls_enable1"]');

        //Перехожу во вкладку General
        $i->executeScript(" $( \"a[href='#general']\" ).trigger( \"click\" )");

        //Задаем номер экст
        $i->fillField('#extension', $m->item->extName);

        //Подождем 2 секунды
        sleep(2);

        //Проверяем есть ли такой номер (extension)
        if (!empty($i->findByClass('.duplicate-exten'))) {
            echo 'Duplicate exten';
        } else {
            //Задаем Display name
            $i->fillField('#name', $m->item->extName);

            //Удаляем значение из поля парол
            $i->clearByClass('#devinfo_secret');

            //Задаем парол
            $i->fillField('#devinfo_secret', $m->item->extPassword);

            echo 'run';
            
            //Жму Submit Отправляю запрос
            $i->click('#submit');

            sleep(1);
            echo $i->executeScript('return document.readyState')=== 'complete';

            // Переходим на страницу редактор номера
            $i->openPage("{$m->item->url}/admin/config.php?display=extensions&extdisplay={$m->item->extName}#advanced");

            //Меняем pjsip на chansip
            $i->clickByXpath('//*[@id="devinfo_changecdriver"]');

            //Нажымаем Yes (Alert)
            $i->acceptAlert();

            sleep(1);
            echo $i->executeScript('return document.readyState')=== 'complete';

            //Перехожу во вкладку Advanced
            $i->clickByXpath('//*[@id="frm_extensions"]/div/div[1]/div[3]/ul/li[4]/a');

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

            //Inbound External Calls  'Force'
            $i->clickByXpath('//*[@id="advanced"]/div/div[8]/div[1]/div[1]/div/div/div/div[2]/span/label[1]');

            //Outbound External Calls 'Force'
            $i->clickByXpath('//*[@id="advanced"]/div/div[8]/div[2]/div[1]/div/div/div/div[2]/span/label[1]');

            //Inbound Internal Calls 'Force'
            $i->clickByXpath('//*[@id="advanced"]/div/div[8]/div[3]/div[1]/div/div/div/div[2]/span/label[1]');

            //Outbound Internal Calls 'Force'
            $i->clickByXpath('//*[@id="advanced"]/div/div[8]/div[4]/div[1]/div/div/div/div[2]/span/label[1]');

            //Call Waiting Tone yes
            $i->clickByXpath('//*[@id="advanced"]/div/div[6]/div[7]/div[1]/div/div/div/div[2]/span/label[1]');

            //Запись звонков
            $i->clickByXpath('//*[@id="advanced"]/div/div[8]/div[5]/div[1]/div/div/div/div[2]/span/label[2]');

            //Нажымаем submit
            $i->clickByXpath('//*[@id="submit"]');

            //Нажымаем кнопку apply
            $i->clickByXpath('//*[@id="button_reload"]');

        }

    }
    public function deleteExtension(){


        $m = new FpbxUser;
        $i = $m->loginFreepbx();

        $i->openPage('http://10.10.3.60/admin/config.php?display=extensions');

        //ищу в поиске extension
        $i->fillFieldXpath('//*[@id="alldids"]/div[1]/div[1]/div[3]/input', $m->item->extName);

        // жду загрузки поиска (jquery долго ищет)
        sleep(1);

        //создаем переменную для проверки условия
        $checkForEmty = !empty($i->findByClass("a[href='?display=extensions&extdisplay={$m->item->extName}']"));

        //проверяю есть ли Extension
        if ($checkForEmty) {
            //если есть жмем кнопку удалить
            $i->click("a.clickable.delete[data-id='{$m->item->extName}']");
            $i->acceptAlert();
        } else{
            //если нет то вывожу в консоль то что Extension не найден  
            echo   "{$m->item->extName} Extension not found";
        };
        
    }


}
        

                













        





        


        
        




