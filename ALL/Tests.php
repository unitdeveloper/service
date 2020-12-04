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

use zetsoft\service\tests\ActiveQuery;
use zetsoft\service\tests\ActiveQueryKeldiyor;
use zetsoft\service\tests\ActiveQueryN;
use zetsoft\service\tests\ActiveQueryNurbek;
use zetsoft\service\tests\ActiveQuerySukhrob;
use zetsoft\service\tests\ActiveQueryUmid;
use zetsoft\service\tests\Amir2Test;
use zetsoft\service\tests\AmirTest;
use zetsoft\service\tests\AxrorTest;
use zetsoft\service\tests\BahodirTest;
use zetsoft\service\tests\demoAmmo;
use zetsoft\service\tests\DilmurodTests;
use zetsoft\service\tests\Dilshod;
use zetsoft\service\tests\Farangis;
use zetsoft\service\tests\feya;
use zetsoft\service\tests\GeoSsl;
use zetsoft\service\tests\ImageOptim;
use zetsoft\service\tests\JamshidTest;
use zetsoft\service\tests\Javohir;
use zetsoft\service\tests\Joins;
use zetsoft\service\tests\Micro;
use zetsoft\service\tests\MirshodTestSer;
use zetsoft\service\tests\MyEventSubscriber;
use zetsoft\service\tests\MyTask;
use zetsoft\service\tests\Nestable2;
use zetsoft\service\tests\Nodirbektest;
use zetsoft\service\tests\NurbekCollectionsTest;
use zetsoft\service\tests\NurbekTest;
use zetsoft\service\tests\OdilovTest;
use zetsoft\service\tests\OtabekTest;
use zetsoft\service\tests\randomTest;
use zetsoft\service\tests\RegExcp;
use zetsoft\service\tests\Serializer;
use zetsoft\service\tests\Service1;
use zetsoft\service\tests\SocketIoNodirbek;
use zetsoft\service\tests\SukhrobService;
use zetsoft\service\tests\Test;
use zetsoft\service\tests\TestAxror;
use zetsoft\service\tests\TestPirmuhammad;
use zetsoft\service\tests\TestServiceSalohiddin;
use zetsoft\service\tests\TestSherzod;
use zetsoft\service\tests\UmidTest;
use yii\base\Component;



/**
 *
* @property ActiveQuery $activeQuery
* @property ActiveQueryKeldiyor $activeQueryKeldiyor
* @property ActiveQueryN $activeQueryN
* @property ActiveQueryNurbek $activeQueryNurbek
* @property ActiveQuerySukhrob $activeQuerySukhrob
* @property ActiveQueryUmid $activeQueryUmid
* @property Amir2Test $amir2Test
* @property AmirTest $amirTest
* @property AxrorTest $axrorTest
* @property BahodirTest $bahodirTest
* @property demoAmmo $demoAmmo
* @property DilmurodTests $dilmurodTests
* @property Dilshod $dilshod
* @property Farangis $farangis
* @property feya $feya
* @property GeoSsl $geoSsl
* @property ImageOptim $imageOptim
* @property JamshidTest $jamshidTest
* @property Javohir $javohir
* @property Joins $joins
* @property Micro $micro
* @property MirshodTestSer $mirshodTestSer
* @property MyEventSubscriber $myEventSubscriber
* @property MyTask $myTask
* @property Nestable2 $nestable2
* @property Nodirbektest $nodirbektest
* @property NurbekCollectionsTest $nurbekCollectionsTest
* @property NurbekTest $nurbekTest
* @property OdilovTest $odilovTest
* @property OtabekTest $otabekTest
* @property randomTest $randomTest
* @property RegExcp $regExcp
* @property Serializer $serializer
* @property Service1 $service1
* @property SocketIoNodirbek $socketIoNodirbek
* @property SukhrobService $sukhrobService
* @property Test $test
* @property TestAxror $testAxror
* @property TestPirmuhammad $testPirmuhammad
* @property TestServiceSalohiddin $testServiceSalohiddin
* @property TestSherzod $testSherzod
* @property UmidTest $umidTest

 */

class Tests extends Component
{

    
    private $_activeQuery;
    private $_activeQueryKeldiyor;
    private $_activeQueryN;
    private $_activeQueryNurbek;
    private $_activeQuerySukhrob;
    private $_activeQueryUmid;
    private $_amir2Test;
    private $_amirTest;
    private $_axrorTest;
    private $_bahodirTest;
    private $_demoAmmo;
    private $_dilmurodTests;
    private $_dilshod;
    private $_farangis;
    private $_feya;
    private $_geoSsl;
    private $_imageOptim;
    private $_jamshidTest;
    private $_javohir;
    private $_joins;
    private $_micro;
    private $_mirshodTestSer;
    private $_myEventSubscriber;
    private $_myTask;
    private $_nestable2;
    private $_nodirbektest;
    private $_nurbekCollectionsTest;
    private $_nurbekTest;
    private $_odilovTest;
    private $_otabekTest;
    private $_randomTest;
    private $_regExcp;
    private $_serializer;
    private $_service1;
    private $_socketIoNodirbek;
    private $_sukhrobService;
    private $_test;
    private $_testAxror;
    private $_testPirmuhammad;
    private $_testServiceSalohiddin;
    private $_testSherzod;
    private $_umidTest;

    
    public function getActiveQuery()
    {
        if ($this->_activeQuery === null)
            $this->_activeQuery = new ActiveQuery();

        return $this->_activeQuery;
    }
    

    public function getActiveQueryKeldiyor()
    {
        if ($this->_activeQueryKeldiyor === null)
            $this->_activeQueryKeldiyor = new ActiveQueryKeldiyor();

        return $this->_activeQueryKeldiyor;
    }
    

    public function getActiveQueryN()
    {
        if ($this->_activeQueryN === null)
            $this->_activeQueryN = new ActiveQueryN();

        return $this->_activeQueryN;
    }
    

    public function getActiveQueryNurbek()
    {
        if ($this->_activeQueryNurbek === null)
            $this->_activeQueryNurbek = new ActiveQueryNurbek();

        return $this->_activeQueryNurbek;
    }
    

    public function getActiveQuerySukhrob()
    {
        if ($this->_activeQuerySukhrob === null)
            $this->_activeQuerySukhrob = new ActiveQuerySukhrob();

        return $this->_activeQuerySukhrob;
    }
    

    public function getActiveQueryUmid()
    {
        if ($this->_activeQueryUmid === null)
            $this->_activeQueryUmid = new ActiveQueryUmid();

        return $this->_activeQueryUmid;
    }
    

    public function getAmir2Test()
    {
        if ($this->_amir2Test === null)
            $this->_amir2Test = new Amir2Test();

        return $this->_amir2Test;
    }
    

    public function getAmirTest()
    {
        if ($this->_amirTest === null)
            $this->_amirTest = new AmirTest();

        return $this->_amirTest;
    }
    

    public function getAxrorTest()
    {
        if ($this->_axrorTest === null)
            $this->_axrorTest = new AxrorTest();

        return $this->_axrorTest;
    }
    

    public function getBahodirTest()
    {
        if ($this->_bahodirTest === null)
            $this->_bahodirTest = new BahodirTest();

        return $this->_bahodirTest;
    }
    

    public function getDemoAmmo()
    {
        if ($this->_demoAmmo === null)
            $this->_demoAmmo = new demoAmmo();

        return $this->_demoAmmo;
    }
    

    public function getDilmurodTests()
    {
        if ($this->_dilmurodTests === null)
            $this->_dilmurodTests = new DilmurodTests();

        return $this->_dilmurodTests;
    }
    

    public function getDilshod()
    {
        if ($this->_dilshod === null)
            $this->_dilshod = new Dilshod();

        return $this->_dilshod;
    }
    

    public function getFarangis()
    {
        if ($this->_farangis === null)
            $this->_farangis = new Farangis();

        return $this->_farangis;
    }
    

    public function getFeya()
    {
        if ($this->_feya === null)
            $this->_feya = new feya();

        return $this->_feya;
    }
    

    public function getGeoSsl()
    {
        if ($this->_geoSsl === null)
            $this->_geoSsl = new GeoSsl();

        return $this->_geoSsl;
    }
    

    public function getImageOptim()
    {
        if ($this->_imageOptim === null)
            $this->_imageOptim = new ImageOptim();

        return $this->_imageOptim;
    }
    

    public function getJamshidTest()
    {
        if ($this->_jamshidTest === null)
            $this->_jamshidTest = new JamshidTest();

        return $this->_jamshidTest;
    }
    

    public function getJavohir()
    {
        if ($this->_javohir === null)
            $this->_javohir = new Javohir();

        return $this->_javohir;
    }
    

    public function getJoins()
    {
        if ($this->_joins === null)
            $this->_joins = new Joins();

        return $this->_joins;
    }
    

    public function getMicro()
    {
        if ($this->_micro === null)
            $this->_micro = new Micro();

        return $this->_micro;
    }
    

    public function getMirshodTestSer()
    {
        if ($this->_mirshodTestSer === null)
            $this->_mirshodTestSer = new MirshodTestSer();

        return $this->_mirshodTestSer;
    }
    

    public function getMyEventSubscriber()
    {
        if ($this->_myEventSubscriber === null)
            $this->_myEventSubscriber = new MyEventSubscriber();

        return $this->_myEventSubscriber;
    }
    

    public function getMyTask()
    {
        if ($this->_myTask === null)
            $this->_myTask = new MyTask();

        return $this->_myTask;
    }
    

    public function getNestable2()
    {
        if ($this->_nestable2 === null)
            $this->_nestable2 = new Nestable2();

        return $this->_nestable2;
    }
    

    public function getNodirbektest()
    {
        if ($this->_nodirbektest === null)
            $this->_nodirbektest = new Nodirbektest();

        return $this->_nodirbektest;
    }
    

    public function getNurbekCollectionsTest()
    {
        if ($this->_nurbekCollectionsTest === null)
            $this->_nurbekCollectionsTest = new NurbekCollectionsTest();

        return $this->_nurbekCollectionsTest;
    }
    

    public function getNurbekTest()
    {
        if ($this->_nurbekTest === null)
            $this->_nurbekTest = new NurbekTest();

        return $this->_nurbekTest;
    }
    

    public function getOdilovTest()
    {
        if ($this->_odilovTest === null)
            $this->_odilovTest = new OdilovTest();

        return $this->_odilovTest;
    }
    

    public function getOtabekTest()
    {
        if ($this->_otabekTest === null)
            $this->_otabekTest = new OtabekTest();

        return $this->_otabekTest;
    }
    

    public function getRandomTest()
    {
        if ($this->_randomTest === null)
            $this->_randomTest = new randomTest();

        return $this->_randomTest;
    }
    

    public function getRegExcp()
    {
        if ($this->_regExcp === null)
            $this->_regExcp = new RegExcp();

        return $this->_regExcp;
    }
    

    public function getSerializer()
    {
        if ($this->_serializer === null)
            $this->_serializer = new Serializer();

        return $this->_serializer;
    }
    

    public function getService1()
    {
        if ($this->_service1 === null)
            $this->_service1 = new Service1();

        return $this->_service1;
    }
    

    public function getSocketIoNodirbek()
    {
        if ($this->_socketIoNodirbek === null)
            $this->_socketIoNodirbek = new SocketIoNodirbek();

        return $this->_socketIoNodirbek;
    }
    

    public function getSukhrobService()
    {
        if ($this->_sukhrobService === null)
            $this->_sukhrobService = new SukhrobService();

        return $this->_sukhrobService;
    }
    

    public function getTest()
    {
        if ($this->_test === null)
            $this->_test = new Test();

        return $this->_test;
    }
    

    public function getTestAxror()
    {
        if ($this->_testAxror === null)
            $this->_testAxror = new TestAxror();

        return $this->_testAxror;
    }
    

    public function getTestPirmuhammad()
    {
        if ($this->_testPirmuhammad === null)
            $this->_testPirmuhammad = new TestPirmuhammad();

        return $this->_testPirmuhammad;
    }
    

    public function getTestServiceSalohiddin()
    {
        if ($this->_testServiceSalohiddin === null)
            $this->_testServiceSalohiddin = new TestServiceSalohiddin();

        return $this->_testServiceSalohiddin;
    }
    

    public function getTestSherzod()
    {
        if ($this->_testSherzod === null)
            $this->_testSherzod = new TestSherzod();

        return $this->_testSherzod;
    }
    

    public function getUmidTest()
    {
        if ($this->_umidTest === null)
            $this->_umidTest = new UmidTest();

        return $this->_umidTest;
    }
    


}
