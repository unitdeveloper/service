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

use zetsoft\service\cores\AppBlock;
use zetsoft\service\cores\AppPage;
use zetsoft\service\cores\Auth;
use zetsoft\service\cores\AuthRest;
use zetsoft\service\cores\BuildApi;
use zetsoft\service\cores\BuildBlock;
use zetsoft\service\cores\BuildWeb;
use zetsoft\service\cores\BuildWidget;
use zetsoft\service\cores\Cache;
use zetsoft\service\cores\Chess;
use zetsoft\service\cores\Date;
use zetsoft\service\cores\Defaults;
use zetsoft\service\cores\GuidOld;
use zetsoft\service\cores\Http2;
use zetsoft\service\cores\Ioc;
use zetsoft\service\cores\Langs;
use zetsoft\service\cores\Menus;
use zetsoft\service\cores\Rbac;
use zetsoft\service\cores\RbacApi;
use zetsoft\service\cores\RbacView;
use zetsoft\service\cores\Rest;
use zetsoft\service\cores\Search;
use zetsoft\service\cores\Session;
use zetsoft\service\cores\Settings;
use zetsoft\service\cores\Tests;
use zetsoft\service\cores\Tranz;
use yii\base\Component;



/**
 *
* @property AppBlock $appBlock
* @property AppPage $appPage
* @property Auth $auth
* @property AuthRest $authRest
* @property BuildApi $buildApi
* @property BuildBlock $buildBlock
* @property BuildWeb $buildWeb
* @property BuildWidget $buildWidget
* @property Cache $cache
* @property Chess $chess
* @property Date $date
* @property Defaults $defaults
* @property GuidOld $guidOld
* @property Http2 $http2
* @property Ioc $ioc
* @property Langs $langs
* @property Menus $menus
* @property Rbac $rbac
* @property RbacApi $rbacApi
* @property RbacView $rbacView
* @property Rest $rest
* @property Search $search
* @property Session $session
* @property Settings $settings
* @property Tests $tests
* @property Tranz $tranz

 */

class Cores extends Component
{

    
    private $_appBlock;
    private $_appPage;
    private $_auth;
    private $_authRest;
    private $_buildApi;
    private $_buildBlock;
    private $_buildWeb;
    private $_buildWidget;
    private $_cache;
    private $_chess;
    private $_date;
    private $_defaults;
    private $_guidOld;
    private $_http2;
    private $_ioc;
    private $_langs;
    private $_menus;
    private $_rbac;
    private $_rbacApi;
    private $_rbacView;
    private $_rest;
    private $_search;
    private $_session;
    private $_settings;
    private $_tests;
    private $_tranz;

    
    public function getAppBlock()
    {
        if ($this->_appBlock === null)
            $this->_appBlock = new AppBlock();

        return $this->_appBlock;
    }
    

    public function getAppPage()
    {
        if ($this->_appPage === null)
            $this->_appPage = new AppPage();

        return $this->_appPage;
    }
    

    public function getAuth()
    {
        if ($this->_auth === null)
            $this->_auth = new Auth();

        return $this->_auth;
    }
    

    public function getAuthRest()
    {
        if ($this->_authRest === null)
            $this->_authRest = new AuthRest();

        return $this->_authRest;
    }
    

    public function getBuildApi()
    {
        if ($this->_buildApi === null)
            $this->_buildApi = new BuildApi();

        return $this->_buildApi;
    }
    

    public function getBuildBlock()
    {
        if ($this->_buildBlock === null)
            $this->_buildBlock = new BuildBlock();

        return $this->_buildBlock;
    }
    

    public function getBuildWeb()
    {
        if ($this->_buildWeb === null)
            $this->_buildWeb = new BuildWeb();

        return $this->_buildWeb;
    }
    

    public function getBuildWidget()
    {
        if ($this->_buildWidget === null)
            $this->_buildWidget = new BuildWidget();

        return $this->_buildWidget;
    }
    

    public function getCache()
    {
        if ($this->_cache === null)
            $this->_cache = new Cache();

        return $this->_cache;
    }
    

    public function getChess()
    {
        if ($this->_chess === null)
            $this->_chess = new Chess();

        return $this->_chess;
    }
    

    public function getDate()
    {
        if ($this->_date === null)
            $this->_date = new Date();

        return $this->_date;
    }
    

    public function getDefaults()
    {
        if ($this->_defaults === null)
            $this->_defaults = new Defaults();

        return $this->_defaults;
    }
    

    public function getGuidOld()
    {
        if ($this->_guidOld === null)
            $this->_guidOld = new GuidOld();

        return $this->_guidOld;
    }
    

    public function getHttp2()
    {
        if ($this->_http2 === null)
            $this->_http2 = new Http2();

        return $this->_http2;
    }
    

    public function getIoc()
    {
        if ($this->_ioc === null)
            $this->_ioc = new Ioc();

        return $this->_ioc;
    }
    

    public function getLangs()
    {
        if ($this->_langs === null)
            $this->_langs = new Langs();

        return $this->_langs;
    }
    

    public function getMenus()
    {
        if ($this->_menus === null)
            $this->_menus = new Menus();

        return $this->_menus;
    }
    

    public function getRbac()
    {
        if ($this->_rbac === null)
            $this->_rbac = new Rbac();

        return $this->_rbac;
    }
    

    public function getRbacApi()
    {
        if ($this->_rbacApi === null)
            $this->_rbacApi = new RbacApi();

        return $this->_rbacApi;
    }
    

    public function getRbacView()
    {
        if ($this->_rbacView === null)
            $this->_rbacView = new RbacView();

        return $this->_rbacView;
    }
    

    public function getRest()
    {
        if ($this->_rest === null)
            $this->_rest = new Rest();

        return $this->_rest;
    }
    

    public function getSearch()
    {
        if ($this->_search === null)
            $this->_search = new Search();

        return $this->_search;
    }
    

    public function getSession()
    {
        if ($this->_session === null)
            $this->_session = new Session();

        return $this->_session;
    }
    

    public function getSettings()
    {
        if ($this->_settings === null)
            $this->_settings = new Settings();

        return $this->_settings;
    }
    

    public function getTests()
    {
        if ($this->_tests === null)
            $this->_tests = new Tests();

        return $this->_tests;
    }
    

    public function getTranz()
    {
        if ($this->_tranz === null)
            $this->_tranz = new Tranz();

        return $this->_tranz;
    }
    


}
