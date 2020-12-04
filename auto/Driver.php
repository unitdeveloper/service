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
use Codeception\PHPUnit\Constraint\WebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverPoint;
use Facebook\WebDriver\WebDriverWait;
use zetsoft\service\https\Guzzle;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;

class Driver extends ZFrame
{
    public $headless = false;
    public $domain = null;
    public $driver;
    public $port = 9515;
    public $source = self::source['chrome'];

    public const source = [
        'Guzzle' => Guzzle::class,
        'BrowserKit' => 'BrowserKit',
        'chrome' => 'chrome',
        'firefox' => 'firefox',
    ];

    public function run()
    {
        switch ($this->source) {
            case self::source['chrome']:
                $desiredCapabilities = DesiredCapabilities::chrome();
                $options = new ChromeOptions();
                if ($this->headless) {
                    $options->addArguments(['headless']);
                }
                $desiredCapabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY,$options);

                break;
            case self::source['firefox']:
                $desiredCapabilities = DesiredCapabilities::firefox();
                break;
        }
        $this->driver = RemoteWebDriver::create('http://localhost:' . $this->port, $desiredCapabilities);
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->run();
    }

    public function openPage($url)
    {
        if (ZStringHelper::startsWith($url, 'http')) {
            $this->driver->get($url);
            return;
        }
        if ($this->domain === null) {
            $this->driver->get($url);
        } else {
            if (ZStringHelper::endsWith($this->domain, '/'))
                $this->driver->get($this->domain . $url);
            else
                $this->driver->get($this->domain . '/' . $url);
        }
    }

    #region Fill Field

    public function fillFieldByName($find, $value = null)
    {
        if (is_array($find)) {
            foreach ($find as $key => $item) {
                $this->driver->findElement(WebDriverBy::name($key)) // find search input element
                ->sendKeys($item);
            }
            return;
        }
        $this->driver->findElement(WebDriverBy::name($find)) // find search input element
        ->sendKeys($value);
    }

    public function fillField($find, $value = null)
    {
        if (is_array($find)) {
            foreach ($find as $key => $item) {
                $this->driver->findElement(WebDriverBy::cssSelector($key)) // find search input element
                ->sendKeys($item);
            }
            return;
        }
        $this->driver->findElement(WebDriverBy::cssSelector($find)) // find search input element
        ->sendKeys($value);
    }

    public function fillFieldXpath($find, $value = null)
    {
        if (is_array($find)) {
            foreach ($find as $key => $item) {
                $this->driver->findElement(WebDriverBy::xpath($key)) // find search input element
                ->sendKeys($item);
            }
            return;
        }
        elseif ( !empty($value) )
            $this->driver->findElement(WebDriverBy::xpath($find)) // find search input element
            ->sendKeys($value);
        else
            $this->driver->findElement(WebDriverBy::xpath($find)) // find search input element
            ->clear();
    }

    #endregion

    #region Submit
    public function submit($key)
    {
        $this->driver->findElement(WebDriverBy::cssSelector($key))->submit();
    }

    public function submitByName($key)
    {
        $this->driver->findElement(WebDriverBy::name($key))->submit();
    }

    public function submitByXpath($key)
    {
        $this->driver->findElement(WebDriverBy::xpath($key))->submit();
    }
    #endregion

    #region Click
    public function click($key)
    {
        $this->driver->findElement(WebDriverBy::cssSelector($key))->click();
    }

    public function clickByText($key)
    {
        $this->driver->findElement(WebDriverBy::linkText($key))->click();
    }

    public function clickByXpath($key)
    {
        $this->driver->findElement(WebDriverBy::xpath($key))->click();
    }

    public function clickByName($key)
    {
        $this->driver->findElement(WebDriverBy::name($key))->click();
    }
    public function findByClass($key)
    {
       return $this->driver->findElements(WebDriverBy::cssSelector($key));
    }
    public function findByXpath($key)
    {
       return $this->driver->findElements(WebDriverBy::xpath($key));
    }
    public function clearByClass($key)
    {
        $this->driver->findElement(WebDriverBy::cssSelector($key)) // find search input element
        ->clear();
    }
    public function clearByXpath($key)
    {
        $this->driver->findElement(WebDriverBy::xpath($key)) // find search input element
        ->clear();
    }


    public function shiftClick($key)
    {
        $element = $this->driver->findElement(WebDriverBy::cssSelector($key));
        $action = new \Facebook\WebDriver\Interactions\WebDriverActions($this->driver);
        $shiftKey = \Facebook\WebDriver\WebDriverKeys::SHIFT;
        $action->keyDown(null, $shiftKey)->click($element)->keyUp(null, $shiftKey)->perform();
    }

    public function ctrlClick($key)
    {
        $element = $this->driver->findElement(WebDriverBy::cssSelector($key));
        $action = new \Facebook\WebDriver\Interactions\WebDriverActions($this->driver);
        $ctrlKey = \Facebook\WebDriver\WebDriverKeys::CONTROL;
        $action->keyDown(null, $ctrlKey)->click($element)->keyUp(null, $ctrlKey)->perform();
    }

    public function doubleClick($coordinates = null)
    {
        $this->driver->getMouse()->doubleClick($coordinates);
    }

    public function contextClick($coordinates = null)
    {
        $this->driver->getMouse()->contextClick($coordinates);
    }

    public function dragAndDrop(WebDriverElement $downLocation, WebDriverElement $where)
    {
        $this->driver->action()->dragAndDrop($downLocation, $where)->perform();
    }
    #endregion

    #region Navigation
    public function refresh(){
        $this->driver->navigate()->refresh();
    }
    #endregion

    #region General
    public function getTitle()
    {
        return $this->driver->getTitle();
    }

    public function findElement($key, $multiple = false)
    {
        if ($multiple)
            $value = $this->driver->findElements(WebDriverBy::cssSelector($key));
        else
            $value = $this->driver->findElement(WebDriverBy::cssSelector($key));
        return $value;
    }

    public function getUrl()
    {
        return $this->driver->getCurrentURL();
    }

    /* Take screenshot
     * For example: Root . '/upload/tempz.png'
     * */

    public function takeScreenshot(string $save_as = null)
    {
        $this->driver->takeScreenshot($save_as);
    }

    public function getPageSource()
    {
        return $this->driver->getPageSource();
    }

    public function close()
    {
        $this->driver->close();
    }

    public function waitTitle($text)
    {
        $this->driver->wait()->until(
            WebDriverExpectedCondition::titleContains($text)
        );
    }

    public function quit()
    {
        $this->driver->quit();
    }


    #endregion

    #region Alert
    public function getAlertText()
    {
        return $this->driver->switchTo()->alert()->getText();
    }

    public function closeAlert()
    {
        $this->driver->switchTo()->alert()->dismiss();
    }

    public function acceptAlert()
    {
        $this->driver->switchTo()->alert()->accept();
    }

    public function waitForAlert($text)
    {
        $this->driver->wait()->until(
            WebDriverExpectedCondition::alertIsPresent(),
            $text
        );
    }

    public function enterToAlert($text)
    {
        $this->driver->switchTo()->alert()->sendKeys($text);
    }
    #endregion

    #region JavaScript
    public function executeScript(string $script, $argument = [])
    {
       return $this->driver->executeScript($script, $argument);
    }

    public function executeAsyncScript(string $script, $argument = [])
    {
        $this->driver->executeAsyncScript($script, $argument);
    }
    #endregion

    #region Press Key

    public function pressKey($key)
    {
        $this->driver->getKeyboard()->keyDown($key);
    }
    public function pressKeyByCtrl($key)
    {
        $this->driver->sendKeys(array(WebDriverKeys::CONTROL, $key));
    }

    public function pressKeys($key1, $key2)
    {
        $action = new \Facebook\WebDriver\Interactions\WebDriverActions($this->driver);
        $action->keyDown(null, $key1)->keyDown(null, $key2)->keyUp(null, $key1)->keyUp(null, $key2)->perform();
    }

    #endregion


    #region Object Coordinates
    public function getCoordinates($key)
    {
        $myElement = $this->driver->findElement(WebDriverBy::cssSelector($key));
        return $myElement->getCoordinates();
    }

    public function moveMouseCursor($key)
    {
        $myElement = $this->driver->findElement(WebDriverBy::cssSelector($key));

        if ( !empty($myElement))
        {
            $coordinates = $myElement->getCoordinates();
            $this->driver->getMouse()->mouseMove($coordinates);
        }
        else
        {
            echo 'error';
        }

    }
    #endregion

    #region Page navigation
    public function back()
    {
        $this->driver->navigate()->back();
    }

    public function forward()
    {
        $this->driver->navigate()->forward();
    }

    public function gotolink($link)
    {
        $this->driver->navigate()->to($link);
    }
    #endregion

    #region Cookies
    public function addCookie($name, $value)
    {
        $this->driver->manage()->addCookie(['name' => $name, 'value' => $value]);
    }

    public function getCookie($name)
    {
        $cookie =  $this->driver->manage()->getCookieNamed($name);
        return $cookie["value"];
    }

    public function deleteCookie($name)
    {
        $this->driver->manage()->deleteCookieNamed($name);
    }

    public function deleteAllCookies()
    {
        $this->driver->manage()->deleteAllCookies();
    }
    #endregion

    #region Attributes
    public function getAttributeValue($key, $attributename)
    {
        return $this->driver->findElement(WebDriverBy::cssSelector($key))->getAttribute($attributename);
    }

    public function getAttributeValueByText($key, $attributename)
    {
        return $this->driver->findElement(WebDriverBy::linkText($key))->getAttribute($attributename);
    }

    public function getAtgetAttributeValueByXpath($key, $attributename)
    {
        return $this->driver->findElement(WebDriverBy::xpath($key))->getAttribute($attributename);
    }
    public function getTextByXpath($key, $attributename)
    {
        return $this->driver->findElement(WebDriverBy::xpath($key))->getText($attributename);
    }

    public function getAttributeValueByName($key, $attributename)
    {
        return $this->driver->findElement(WebDriverBy::name($key))->getAttribute($attributename);
    }

    #endregion

    public function test()
    {
        $this->driver->get("http://mplace.mains.uz/");
        $login_button = $this->driver->findElement(WebDriverBy::cssSelector('.fa-sign-in-alt'));
        $this->driver->getMouse()->mouseMove($login_button->getCoordinates());
        $this->driver->getMouse()->click();
    }



}
