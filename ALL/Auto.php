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

use zetsoft\service\auto\Driver;
use zetsoft\service\auto\FpbxAdmin;
use zetsoft\service\auto\FpbxAdminXolmat;
use zetsoft\service\auto\FpbxExtention;
use zetsoft\service\auto\FpbxExts;
use zetsoft\service\auto\FpbxExtsI;
use zetsoft\service\auto\FpbxExtsX;
use zetsoft\service\auto\FpbxExtsXolmat;
use zetsoft\service\auto\FpbxIvrs;
use zetsoft\service\auto\FpbxTrunks;
use zetsoft\service\auto\FpbxTrunks1;
use zetsoft\service\auto\FpbxUser;
use zetsoft\service\auto\FpbxUserXolmat;
use zetsoft\service\auto\shablon;
use zetsoft\service\auto\UntitledTestCaseTest;
use yii\base\Component;



/**
 *
* @property Driver $driver
* @property FpbxAdmin $fpbxAdmin
* @property FpbxAdminXolmat $fpbxAdminXolmat
* @property FpbxExtention $fpbxExtention
* @property FpbxExts $fpbxExts
* @property FpbxExtsI $fpbxExtsI
* @property FpbxExtsX $fpbxExtsX
* @property FpbxExtsXolmat $fpbxExtsXolmat
* @property FpbxIvrs $fpbxIvrs
* @property FpbxTrunks $fpbxTrunks
* @property FpbxTrunks1 $fpbxTrunks1
* @property FpbxUser $fpbxUser
* @property FpbxUserXolmat $fpbxUserXolmat
* @property shablon $shablon
* @property UntitledTestCaseTest $untitledTestCaseTest

 */

class Auto extends Component
{

    
    private $_driver;
    private $_fpbxAdmin;
    private $_fpbxAdminXolmat;
    private $_fpbxExtention;
    private $_fpbxExts;
    private $_fpbxExtsI;
    private $_fpbxExtsX;
    private $_fpbxExtsXolmat;
    private $_fpbxIvrs;
    private $_fpbxTrunks;
    private $_fpbxTrunks1;
    private $_fpbxUser;
    private $_fpbxUserXolmat;
    private $_shablon;
    private $_untitledTestCaseTest;

    
    public function getDriver()
    {
        if ($this->_driver === null)
            $this->_driver = new Driver();

        return $this->_driver;
    }
    

    public function getFpbxAdmin()
    {
        if ($this->_fpbxAdmin === null)
            $this->_fpbxAdmin = new FpbxAdmin();

        return $this->_fpbxAdmin;
    }
    

    public function getFpbxAdminXolmat()
    {
        if ($this->_fpbxAdminXolmat === null)
            $this->_fpbxAdminXolmat = new FpbxAdminXolmat();

        return $this->_fpbxAdminXolmat;
    }
    

    public function getFpbxExtention()
    {
        if ($this->_fpbxExtention === null)
            $this->_fpbxExtention = new FpbxExtention();

        return $this->_fpbxExtention;
    }
    

    public function getFpbxExts()
    {
        if ($this->_fpbxExts === null)
            $this->_fpbxExts = new FpbxExts();

        return $this->_fpbxExts;
    }
    

    public function getFpbxExtsI()
    {
        if ($this->_fpbxExtsI === null)
            $this->_fpbxExtsI = new FpbxExtsI();

        return $this->_fpbxExtsI;
    }
    

    public function getFpbxExtsX()
    {
        if ($this->_fpbxExtsX === null)
            $this->_fpbxExtsX = new FpbxExtsX();

        return $this->_fpbxExtsX;
    }
    

    public function getFpbxExtsXolmat()
    {
        if ($this->_fpbxExtsXolmat === null)
            $this->_fpbxExtsXolmat = new FpbxExtsXolmat();

        return $this->_fpbxExtsXolmat;
    }
    

    public function getFpbxIvrs()
    {
        if ($this->_fpbxIvrs === null)
            $this->_fpbxIvrs = new FpbxIvrs();

        return $this->_fpbxIvrs;
    }
    

    public function getFpbxTrunks()
    {
        if ($this->_fpbxTrunks === null)
            $this->_fpbxTrunks = new FpbxTrunks();

        return $this->_fpbxTrunks;
    }
    

    public function getFpbxTrunks1()
    {
        if ($this->_fpbxTrunks1 === null)
            $this->_fpbxTrunks1 = new FpbxTrunks1();

        return $this->_fpbxTrunks1;
    }
    

    public function getFpbxUser()
    {
        if ($this->_fpbxUser === null)
            $this->_fpbxUser = new FpbxUser();

        return $this->_fpbxUser;
    }
    

    public function getFpbxUserXolmat()
    {
        if ($this->_fpbxUserXolmat === null)
            $this->_fpbxUserXolmat = new FpbxUserXolmat();

        return $this->_fpbxUserXolmat;
    }
    

    public function getShablon()
    {
        if ($this->_shablon === null)
            $this->_shablon = new shablon();

        return $this->_shablon;
    }
    

    public function getUntitledTestCaseTest()
    {
        if ($this->_untitledTestCaseTest === null)
            $this->_untitledTestCaseTest = new UntitledTestCaseTest();

        return $this->_untitledTestCaseTest;
    }
    


}
