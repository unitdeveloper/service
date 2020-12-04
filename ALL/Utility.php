<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\ALL;

use zetsoft\service\utility\Alert;
use zetsoft\service\utility\Assetz;
use zetsoft\service\utility\Cache;
use zetsoft\service\utility\Child;
use zetsoft\service\utility\Dbclear;
use zetsoft\service\utility\DeepCopy;
use zetsoft\service\utility\ElasticTest;
use zetsoft\service\utility\Execs;
use zetsoft\service\utility\ExecsAsync;
use zetsoft\service\utility\Fast;
use zetsoft\service\utility\File;
use zetsoft\service\utility\FtpFull;
use zetsoft\service\utility\Infinite;
use zetsoft\service\utility\MadelineProto;
use zetsoft\service\utility\Mails;
use zetsoft\service\utility\MailsOtabek;
use zetsoft\service\utility\Mains;
use zetsoft\service\utility\Menu;
use zetsoft\service\utility\Monolog;
use zetsoft\service\utility\Newserve;
use zetsoft\service\utility\Notify;
use zetsoft\service\utility\Phars;
use zetsoft\service\utility\Php74;
use zetsoft\service\utility\Pregs;
use zetsoft\service\utility\serialize;
use zetsoft\service\utility\Spatie;
use zetsoft\service\utility\SwiftMailer;
use zetsoft\service\utility\SwiftMailerO;
use zetsoft\service\utility\SwiftMailerOtabek;
use zetsoft\service\utility\TelegramAsync;
use zetsoft\service\utility\TerrabaytsMonolog;
use zetsoft\service\utility\Test;
use zetsoft\service\utility\Test2;
use zetsoft\service\utility\testGetValue;
use zetsoft\service\utility\test_22;
use zetsoft\service\utility\test_4;
use zetsoft\service\utility\UrlApp;
use zetsoft\service\utility\Views;
use zetsoft\service\utility\Workerman;
use yii\base\Component;



/**
 *
* @property Alert $alert
* @property Assetz $assetz
* @property Cache $cache
* @property Child $child
* @property Dbclear $dbclear
* @property DeepCopy $deepCopy
* @property ElasticTest $elasticTest
* @property Execs $execs
* @property ExecsAsync $execsAsync
* @property Fast $fast
* @property File $file
* @property FtpFull $ftpFull
* @property Infinite $infinite
* @property MadelineProto $madelineProto
* @property Mails $mails
* @property MailsOtabek $mailsOtabek
* @property Mains $mains
* @property Menu $menu
* @property Monolog $monolog
* @property Newserve $newserve
* @property Notify $notify
* @property Phars $phars
* @property Php74 $php74
* @property Pregs $pregs
* @property serialize $serialize
* @property Spatie $spatie
* @property SwiftMailer $swiftMailer
* @property SwiftMailerO $swiftMailerO
* @property SwiftMailerOtabek $swiftMailerOtabek
* @property TelegramAsync $telegramAsync
* @property TerrabaytsMonolog $terrabaytsMonolog
* @property Test $test
* @property Test2 $test2
* @property testGetValue $testGetValue
* @property test_22 $test_22
* @property test_4 $test_4
* @property UrlApp $urlApp
* @property Views $views
* @property Workerman $workerman

 */

class Utility extends Component
{

    
    private $_alert;
    private $_assetz;
    private $_cache;
    private $_child;
    private $_dbclear;
    private $_deepCopy;
    private $_elasticTest;
    private $_execs;
    private $_execsAsync;
    private $_fast;
    private $_file;
    private $_ftpFull;
    private $_infinite;
    private $_madelineProto;
    private $_mails;
    private $_mailsOtabek;
    private $_mains;
    private $_menu;
    private $_monolog;
    private $_newserve;
    private $_notify;
    private $_phars;
    private $_php74;
    private $_pregs;
    private $_serialize;
    private $_spatie;
    private $_swiftMailer;
    private $_swiftMailerO;
    private $_swiftMailerOtabek;
    private $_telegramAsync;
    private $_terrabaytsMonolog;
    private $_test;
    private $_test2;
    private $_testGetValue;
    private $_test_22;
    private $_test_4;
    private $_urlApp;
    private $_views;
    private $_workerman;

    
    public function getAlert()
    {
        if ($this->_alert === null)
            $this->_alert = new Alert();

        return $this->_alert;
    }
    

    public function getAssetz()
    {
        if ($this->_assetz === null)
            $this->_assetz = new Assetz();

        return $this->_assetz;
    }
    

    public function getCache()
    {
        if ($this->_cache === null)
            $this->_cache = new Cache();

        return $this->_cache;
    }
    

    public function getChild()
    {
        if ($this->_child === null)
            $this->_child = new Child();

        return $this->_child;
    }
    

    public function getDbclear()
    {
        if ($this->_dbclear === null)
            $this->_dbclear = new Dbclear();

        return $this->_dbclear;
    }
    

    public function getDeepCopy()
    {
        if ($this->_deepCopy === null)
            $this->_deepCopy = new DeepCopy();

        return $this->_deepCopy;
    }
    

    public function getElasticTest()
    {
        if ($this->_elasticTest === null)
            $this->_elasticTest = new ElasticTest();

        return $this->_elasticTest;
    }
    

    public function getExecs()
    {
        if ($this->_execs === null)
            $this->_execs = new Execs();

        return $this->_execs;
    }
    

    public function getExecsAsync()
    {
        if ($this->_execsAsync === null)
            $this->_execsAsync = new ExecsAsync();

        return $this->_execsAsync;
    }
    

    public function getFast()
    {
        if ($this->_fast === null)
            $this->_fast = new Fast();

        return $this->_fast;
    }
    

    public function getFile()
    {
        if ($this->_file === null)
            $this->_file = new File();

        return $this->_file;
    }
    

    public function getFtpFull()
    {
        if ($this->_ftpFull === null)
            $this->_ftpFull = new FtpFull();

        return $this->_ftpFull;
    }
    

    public function getInfinite()
    {
        if ($this->_infinite === null)
            $this->_infinite = new Infinite();

        return $this->_infinite;
    }
    

    public function getMadelineProto()
    {
        if ($this->_madelineProto === null)
            $this->_madelineProto = new MadelineProto();

        return $this->_madelineProto;
    }
    

    public function getMails()
    {
        if ($this->_mails === null)
            $this->_mails = new Mails();

        return $this->_mails;
    }
    

    public function getMailsOtabek()
    {
        if ($this->_mailsOtabek === null)
            $this->_mailsOtabek = new MailsOtabek();

        return $this->_mailsOtabek;
    }
    

    public function getMains()
    {
        if ($this->_mains === null)
            $this->_mains = new Mains();

        return $this->_mains;
    }
    

    public function getMenu()
    {
        if ($this->_menu === null)
            $this->_menu = new Menu();

        return $this->_menu;
    }
    

    public function getMonolog()
    {
        if ($this->_monolog === null)
            $this->_monolog = new Monolog();

        return $this->_monolog;
    }
    

    public function getNewserve()
    {
        if ($this->_newserve === null)
            $this->_newserve = new Newserve();

        return $this->_newserve;
    }
    

    public function getNotify()
    {
        if ($this->_notify === null)
            $this->_notify = new Notify();

        return $this->_notify;
    }
    

    public function getPhars()
    {
        if ($this->_phars === null)
            $this->_phars = new Phars();

        return $this->_phars;
    }
    

    public function getPhp74()
    {
        if ($this->_php74 === null)
            $this->_php74 = new Php74();

        return $this->_php74;
    }
    

    public function getPregs()
    {
        if ($this->_pregs === null)
            $this->_pregs = new Pregs();

        return $this->_pregs;
    }
    

    public function getSerialize()
    {
        if ($this->_serialize === null)
            $this->_serialize = new serialize();

        return $this->_serialize;
    }
    

    public function getSpatie()
    {
        if ($this->_spatie === null)
            $this->_spatie = new Spatie();

        return $this->_spatie;
    }
    

    public function getSwiftMailer()
    {
        if ($this->_swiftMailer === null)
            $this->_swiftMailer = new SwiftMailer();

        return $this->_swiftMailer;
    }
    

    public function getSwiftMailerO()
    {
        if ($this->_swiftMailerO === null)
            $this->_swiftMailerO = new SwiftMailerO();

        return $this->_swiftMailerO;
    }
    

    public function getSwiftMailerOtabek()
    {
        if ($this->_swiftMailerOtabek === null)
            $this->_swiftMailerOtabek = new SwiftMailerOtabek();

        return $this->_swiftMailerOtabek;
    }
    

    public function getTelegramAsync()
    {
        if ($this->_telegramAsync === null)
            $this->_telegramAsync = new TelegramAsync();

        return $this->_telegramAsync;
    }
    

    public function getTerrabaytsMonolog()
    {
        if ($this->_terrabaytsMonolog === null)
            $this->_terrabaytsMonolog = new TerrabaytsMonolog();

        return $this->_terrabaytsMonolog;
    }
    

    public function getTest()
    {
        if ($this->_test === null)
            $this->_test = new Test();

        return $this->_test;
    }
    

    public function getTest2()
    {
        if ($this->_test2 === null)
            $this->_test2 = new Test2();

        return $this->_test2;
    }
    

    public function getTestGetValue()
    {
        if ($this->_testGetValue === null)
            $this->_testGetValue = new testGetValue();

        return $this->_testGetValue;
    }
    

    public function getTest_22()
    {
        if ($this->_test_22 === null)
            $this->_test_22 = new test_22();

        return $this->_test_22;
    }
    

    public function getTest_4()
    {
        if ($this->_test_4 === null)
            $this->_test_4 = new test_4();

        return $this->_test_4;
    }
    

    public function getUrlApp()
    {
        if ($this->_urlApp === null)
            $this->_urlApp = new UrlApp();

        return $this->_urlApp;
    }
    

    public function getViews()
    {
        if ($this->_views === null)
            $this->_views = new Views();

        return $this->_views;
    }
    

    public function getWorkerman()
    {
        if ($this->_workerman === null)
            $this->_workerman = new Workerman();

        return $this->_workerman;
    }
    


}
