<?php

/**
 *
 *
 * Author:  Zoirjon Sobirov
 * https://t.me/zoirjon_sobirov
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
use zetsoft\service\auto\Driver;
use zetsoft\system\kernels\ZFrame;


class FpbxExtsI extends ZFrame
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

        $extName = "392";
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
         * *    Create extension:
         * *
         */

        //Перехожу во вкладку с созданными номерами (Extensions)  Get запросом
        /*$this->driver->get("http://10.10.3.60/admin/config.php?display=extensions&tech_hardware=pjsip_generic");*/

        //Перехожу во вкладку с созданием номера во вкладку advanced (Extensions)  Get запросом
        $i->openPage("http://10.10.3.60/admin/config.php?display=extensions&tech_hardware=pjsip_generic#advanced");

        //Нажимаю на selector Media encrytion
        //$i->click('#devinfo_media_encryption');


        //Меняю Media Encrytion на Dtls
        //$this->driver->findElement(WebDriverBy::cssSelector('option[value="dtls"]'))->click();
        $i->clickByXpath('//*[@id="devinfo_media_encryption"]/option[3]');

        //Включаю Dtls
        $i->click('label[for="dtls_enable1"]');

        //Перехожу во вкладку General
        // $this->triggerHref("#general");
        $i->executeScript(" $( \"a[href='#general']\" ).trigger( \"click\" )") ;
        //Задаем номер экст
        $i->fillField('#extension', $extName);

        //Подождем 2 секунды
        sleep(2);

        //Задаем Display name
        $i->fillField('#name', $extName);



        /*$isEmpty = $i->findElement(WebDriverBy::className('duplicate-exten'))->getAttribute('value');
        if (!empty($isEmpty)){
            echo "if";
        } else
            echo "else";*/


        /*        $number= $this->driver->findElement(WebDriverBy::className('duplicate-exten'))->getAttribute('value');
                echo $number;*/

        /*
           if(!$this->driver->wait(10)->until(
                   WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('duplicate-exten'))) ){
               $this->driver->findElement(WebDriverBy::cssSelector('#name'))->sendKeys($extName);
               $this->driver->findElement(WebDriverBy::cssSelector('#devinfo_secret'))->sendKeys($extPassword);
               $this->driver->findElement(WebDriverBy::cssSelector('#submit'))->click();

                   } else {
               echo "Duplicate";

                   }  */

        /*$this->driver->findElement(WebDriverBy::cssSelector('#name'))->sendKeys($extName);
        $this->driver->findElement(WebDriverBy::cssSelector('#submit'))->click();*/





        /*  try{

              $this->driver->wait()->until(
                  WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('duplicate-exten')));

             }
          catch (TimeoutException $e) {
                return "Timeout Exception because".$e->getMessage();
          }     */


        /*  $this->driver->wait(5)->until(
              WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('duplicate-exten')));*/

        /* if ( !empty($number))
         {
             // ok

             $this->driver->findElement(WebDriverBy::cssSelector('#submit'))->click();
         }
         else
         {
             // error actions
             echo "Duplicate";
         }*/

        //   $myVarCheck =  $this->driver->findElement(WebDriverBy::xpath("//*[contains(text(), \"360\")]"))->getText();














        /* $this->driver->findElement(WebDriverBy::cssSelector('.nav.nav-tabs.list li[data-name="general"].change-tab'))->click();*/


        //Жму Submit Отправляю запрос
        /*$this->driver->findElement(WebDriverBy::cssSelector('#submit'))->click();*/



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
    public function editExtension(){
        $login = 'admin';
        $password = 'Production';
        $url = "http://10.10.3.60/admin/config.php?display=extensions";

        $extName = "395";
        $extPassword = $extName;

        $i = new Driver(['headless' => true]);
        $i->openPage($url);
        echo "The title is '" . $i->getTitle() . "'\n";

        echo "The current URI is '" . $i->getUrl() . "'\n";

        $i->click('#login_admin');

        

        //Вписываем Логин
        $i->fillField('#ui-id-1 #loginform .form-group input[name=username]', $login);

        //Вписываем Пароль
        $i->fillField('#ui-id-1 #loginform .form-group input[name=password]', $password);

        //Кликаю по кнопке Логин (Вхожу)
        $i->click('.ui-dialog-buttonset button.ui-button.ui-corner-all.ui-widget:first-child');
        

        /*$i->fillField("a[href='?display=extensions&extdisplay=395']");*/

        //ищу в поиске extension 
        $i->fillFieldXpath('//*[@id="alldids"]/div[1]/div[1]/div[3]/input', $extName);



        sleep(1);
        $checkForEmty = !empty($i->findByClass("a[href='?display=extensions&extdisplay=$extName']"));

        //проверяю есть ли он
        if ($checkForEmty) {
            $i->click("a[href='?display=extensions&extdisplay=$extName']");

        } else{
            echo   "$extName Extension not found";
        };


        







        

    }

}


        

                













        





        


        
        




