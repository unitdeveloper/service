<?php

/**
 *
 *
 * Author:  Zoirjon Sobirov
 * https://t.me/zoirjon_sobirov
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

class FpbxIvrs extends ZFrame
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
        // open | http://10.10.3.31/admin/config.php |
        $this->webDriver->get("http://10.10.3.31/admin/config.php");
       
        // click | id=login_admin |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("login_admin"))->click();
        // click | xpath=(//input[@name='username'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='username'])[2]"))->click();
        // type | xpath=(//input[@name='username'])[2] | admin
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='username'])[2]"))->sendKeys("admin");
        // click | xpath=(//input[@name='password'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='password'])[2]"))->click();
        // type | xpath=(//input[@name='password'])[2] | adminfreepbx
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='password'])[2]"))->sendKeys("Formula1");
        // click | xpath=(/html/body/div[15]/div[3]/div/button)[1] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(/html/body/div[15]/div[3]/div/button)[1]"))->click();
        // click | link=Connectivity |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Connectivity"))->click();
        // click | link=Trunks |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Trunks"))->click();
        // click | xpath=(//button[@type='button'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//button[@type='button'])[2]"))->click();
        // click | //div[@id='toolbar-all']/div/ul/li[2]/a/strong |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='toolbar-all']/div/ul/li[2]/a/strong"))->click();
        // click | id=trunk_name |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("trunk_name"))->click();
        // type | id=trunk_name | trunkName
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("trunk_name"))->sendKeys("trunkName");
        // click | //div[@id='tgeneral']/div[2]/div/div/div/div/div[2]/label |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[2]/div/div/div/div/div[2]/label"))->click();
        // click | //div[@id='tgeneral']/div[2]/div/div/div/div/div[2]/label[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[2]/div/div/div/div/div[2]/label[2]"))->click();
        // click | id=outcid |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("outcid"))->click();
        // type | id=outcid | outbound
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("outcid"))->sendKeys("outbound");
        // click | //div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label[2]"))->click();
        // click | //div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label[3] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label[3]"))->click();
        // click | //div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label[4] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label[4]"))->click();
        // click | //div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[4]/div/div/div/div/div[2]/label"))->click();
        // click | //div[@id='tgeneral']/div[7]/div/div/div/div/div[2]/label |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[7]/div/div/div/div/div[2]/label"))->click();
        // click | //div[@id='tgeneral']/div[7]/div/div/div/div/div[2]/label[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[7]/div/div/div/div/div[2]/label[2]"))->click();
        // click | //div[@id='tgeneral']/div[8]/div/div/div/div/div[2]/label |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[8]/div/div/div/div/div[2]/label"))->click();
        // click | //div[@id='tgeneral']/div[8]/div/div/div/div/div[2]/label[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[8]/div/div/div/div/div[2]/label[2]"))->click();
        // click | //div[@id='tgeneral']/div[9]/div/div/div/div/div[2]/span/label |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[9]/div/div/div/div/div[2]/span/label"))->click();
        // click | //div[@id='tgeneral']/div[9]/div/div/div/div/div[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[9]/div/div/div/div/div[2]"))->click();
        // type | id=failtrunk | ifMonitor
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("failtrunk"))->sendKeys("ifMonitor");
        // click | //div[@id='tgeneral']/div[9]/div/div/div/div/div[2]/span/label[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//div[@id='tgeneral']/div[9]/div/div/div/div/div[2]/span/label[2]"))->click();
        // click | link=Dialed Number Manipulation Rules |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Dialed Number Manipulation Rules"))->click();
        // click | //a[@id='rowadd0']/i |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("//a[@id='rowadd0']/i"))->click();
        // type | id=prepend_digit_0 |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("prepend_digit_0"))->sendKeys("");
        // type | id=pattern_prefix_0 |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("pattern_prefix_0"))->sendKeys("");
        // type | id=pattern_pass_0 |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("pattern_pass_0"))->sendKeys("");
        // type | xpath=(//*[@id="dialoutprefix"]) | Outbound Dial Prefix
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//*[@id=\"dialoutprefix\"])"))->sendKeys("Outbound Dial Prefix");
        // click | link=sip Settings |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("sip Settings"))->click();
        // type | xpath=(//*[@id="channelid"]) | trunkName
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//*[@id=\"channelid\"])"))->sendKeys("trunkName");
        // sendKeys | id=peerdetails | ${KEY_ENTER}peerDetails
        $this->keys("${KEY_ENTER}peerDetails");
        // click | link=Incoming |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Incoming"))->click();
        // type | id=usercontext | incomingusercontext
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("usercontext"))->sendKeys("incomingusercontext");
        // sendKeys | id=userconfig | ${KEY_ENTER}test
        $this->keys("${KEY_ENTER}test");
        // type | id=register | RegisterString
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("register"))->sendKeys("RegisterString");
        // click | xpath=(//*[@id="submit"]) |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//*[@id=\"submit\"])"))->click();


        $this->webDriver->close();
    }
}
