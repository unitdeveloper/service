<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver;

class UntitledTestCaseTest extends TestCase
{
    /**
     * @var WebDriver\Remote\RemoteWebDriver
     */
    private $webDriver;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * init webdriver
     */
    public function setUp()
    {
        $desiredCapabilities = WebDriver\Remote\DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability('trustAllSSLCertificates', true);
        $this->webDriver = WebDriver\Remote\RemoteWebDriver::create(
            $_SERVER['SELENIUM_HUB'],
            $desiredCapabilities
        );
        $this->baseUrl = $_SERVER['SELENIUM_BASE_URL'];
    }

    /**
     * Method testUntitledTestCase
     * @test
     */
    public function testUntitledTestCase()
    {
        // open | http://10.10.3.60/admin/config.php |
        $this->webDriver->get("http://10.10.3.60/admin/config.php");
        //  | id=login_admin |
        // ERROR: Caught exception [unknown command []]
        // click | xpath=(//input[@name='username'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='username'])[2]"))->click();
        // type | xpath=(//input[@name='username'])[2] | admin
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='username'])[2]"))->sendKeys("admin");
        // click | xpath=(//input[@name='password'])[2] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='password'])[2]"))->click();
        // type | xpath=(//input[@name='password'])[2] | adminfreepbx
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//input[@name='password'])[2]"))->sendKeys("adminfreepbx");
        // click | xpath=(//button[@type='button'])[3] |
        $this->webDriver->findElement(WebDriver\WebDriverBy::xpath("(//button[@type='button'])[3]"))->click();
        // click | link=Admin |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Admin"))->click();
        // click | link=Administrators |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Administrators"))->click();
        // click | link=Administrators |
        $this->webDriver->findElement(WebDriver\WebDriverBy::linkText("Administrators"))->click();
        // doubleClick | link=Administrators |
        // ERROR: Caught exception [ERROR: Unsupported command [doubleClick | link=Administrators | ]]
        // click | id=username |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("username"))->click();
        // click | id=username |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("username"))->click();
        // type | id=username | te
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("username"))->sendKeys("te");
        // click | id=password |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("password"))->click();
        // type | id=password | te
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("password"))->sendKeys("te");
        // click | id=submit |
        $this->webDriver->findElement(WebDriver\WebDriverBy::id("submit"))->click();
    }

    /**
     * Close the current window.
     */
    public function tearDown()
    {
        $this->webDriver->close();
    }

    /**
     * @param WebDriver\Remote\RemoteWebElement $element
     *
     * @return WebDriver\WebDriverSelect
     * @throws WebDriver\Exception\UnexpectedTagNameException
     */
    private function getSelect(WebDriver\Remote\RemoteWebElement $element): WebDriver\WebDriverSelect
    {
        return new WebDriver\WebDriverSelect($element);
    }
}

