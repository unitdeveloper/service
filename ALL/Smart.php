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

use zetsoft\service\smart\Adder;
use zetsoft\service\smart\AdderD;
use zetsoft\service\smart\Adder_U;
use zetsoft\service\smart\Cruds;
use zetsoft\service\smart\Dyna;
use zetsoft\service\smart\Fake;
use zetsoft\service\smart\Insert;
use zetsoft\service\smart\Migra;
use zetsoft\service\smart\Model;
use zetsoft\service\smart\Name;
use zetsoft\service\smart\Norms;
use zetsoft\service\smart\Order;
use zetsoft\service\smart\Puters;
use zetsoft\service\smart\Service;
use zetsoft\service\smart\Sorting;
use zetsoft\service\smart\Stats;
use zetsoft\service\smart\Tester;
use zetsoft\service\smart\Visuals;
use zetsoft\service\smart\VisualsApp;
use zetsoft\service\smart\VisualsDb;
use zetsoft\service\smart\Widget;
use zetsoft\service\smart\WidgetA;
use yii\base\Component;



/**
 *
* @property Adder $adder
* @property AdderD $adderD
* @property Adder_U $adder_U
* @property Cruds $cruds
* @property Dyna $dyna
* @property Fake $fake
* @property Insert $insert
* @property Migra $migra
* @property Model $model
* @property Name $name
* @property Norms $norms
* @property Order $order
* @property Puters $puters
* @property Service $service
* @property Sorting $sorting
* @property Stats $stats
* @property Tester $tester
* @property Visuals $visuals
* @property VisualsApp $visualsApp
* @property VisualsDb $visualsDb
* @property Widget $widget
* @property WidgetA $widgetA

 */

class Smart extends Component
{

    
    private $_adder;
    private $_adderD;
    private $_adder_U;
    private $_cruds;
    private $_dyna;
    private $_fake;
    private $_insert;
    private $_migra;
    private $_model;
    private $_name;
    private $_norms;
    private $_order;
    private $_puters;
    private $_service;
    private $_sorting;
    private $_stats;
    private $_tester;
    private $_visuals;
    private $_visualsApp;
    private $_visualsDb;
    private $_widget;
    private $_widgetA;

    
    public function getAdder()
    {
        if ($this->_adder === null)
            $this->_adder = new Adder();

        return $this->_adder;
    }
    

    public function getAdderD()
    {
        if ($this->_adderD === null)
            $this->_adderD = new AdderD();

        return $this->_adderD;
    }
    

    public function getAdder_U()
    {
        if ($this->_adder_U === null)
            $this->_adder_U = new Adder_U();

        return $this->_adder_U;
    }
    

    public function getCruds()
    {
        if ($this->_cruds === null)
            $this->_cruds = new Cruds();

        return $this->_cruds;
    }
    

    public function getDyna()
    {
        if ($this->_dyna === null)
            $this->_dyna = new Dyna();

        return $this->_dyna;
    }
    

    public function getFake()
    {
        if ($this->_fake === null)
            $this->_fake = new Fake();

        return $this->_fake;
    }
    

    public function getInsert()
    {
        if ($this->_insert === null)
            $this->_insert = new Insert();

        return $this->_insert;
    }
    

    public function getMigra()
    {
        if ($this->_migra === null)
            $this->_migra = new Migra();

        return $this->_migra;
    }
    

    public function getModel()
    {
        if ($this->_model === null)
            $this->_model = new Model();

        return $this->_model;
    }
    

    public function getName()
    {
        if ($this->_name === null)
            $this->_name = new Name();

        return $this->_name;
    }
    

    public function getNorms()
    {
        if ($this->_norms === null)
            $this->_norms = new Norms();

        return $this->_norms;
    }
    

    public function getOrder()
    {
        if ($this->_order === null)
            $this->_order = new Order();

        return $this->_order;
    }
    

    public function getPuters()
    {
        if ($this->_puters === null)
            $this->_puters = new Puters();

        return $this->_puters;
    }
    

    public function getService()
    {
        if ($this->_service === null)
            $this->_service = new Service();

        return $this->_service;
    }
    

    public function getSorting()
    {
        if ($this->_sorting === null)
            $this->_sorting = new Sorting();

        return $this->_sorting;
    }
    

    public function getStats()
    {
        if ($this->_stats === null)
            $this->_stats = new Stats();

        return $this->_stats;
    }
    

    public function getTester()
    {
        if ($this->_tester === null)
            $this->_tester = new Tester();

        return $this->_tester;
    }
    

    public function getVisuals()
    {
        if ($this->_visuals === null)
            $this->_visuals = new Visuals();

        return $this->_visuals;
    }
    

    public function getVisualsApp()
    {
        if ($this->_visualsApp === null)
            $this->_visualsApp = new VisualsApp();

        return $this->_visualsApp;
    }
    

    public function getVisualsDb()
    {
        if ($this->_visualsDb === null)
            $this->_visualsDb = new VisualsDb();

        return $this->_visualsDb;
    }
    

    public function getWidget()
    {
        if ($this->_widget === null)
            $this->_widget = new Widget();

        return $this->_widget;
    }
    

    public function getWidgetA()
    {
        if ($this->_widgetA === null)
            $this->_widgetA = new WidgetA();

        return $this->_widgetA;
    }
    


}
