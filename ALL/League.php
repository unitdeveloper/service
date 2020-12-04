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

use zetsoft\service\league\BlogController;
use zetsoft\service\league\Booboo;
use zetsoft\service\league\Climate;
use zetsoft\service\league\Contain;
use zetsoft\service\league\CsvService;
use zetsoft\service\league\FakerService;
use zetsoft\service\league\FractalService;
use zetsoft\service\league\GlideService;
use zetsoft\service\league\PeriodService;
use zetsoft\service\league\Route;
use zetsoft\service\league\RouteSymfony;
use zetsoft\service\league\SomeController;
use yii\base\Component;



/**
 *
* @property BlogController $blogController
* @property Booboo $booboo
* @property Climate $climate
* @property Contain $contain
* @property CsvService $csvService
* @property FakerService $fakerService
* @property FractalService $fractalService
* @property GlideService $glideService
* @property PeriodService $periodService
* @property Route $route
* @property RouteSymfony $routeSymfony
* @property SomeController $someController

 */

class League extends Component
{

    
    private $_blogController;
    private $_booboo;
    private $_climate;
    private $_contain;
    private $_csvService;
    private $_fakerService;
    private $_fractalService;
    private $_glideService;
    private $_periodService;
    private $_route;
    private $_routeSymfony;
    private $_someController;

    
    public function getBlogController()
    {
        if ($this->_blogController === null)
            $this->_blogController = new BlogController();

        return $this->_blogController;
    }
    

    public function getBooboo()
    {
        if ($this->_booboo === null)
            $this->_booboo = new Booboo();

        return $this->_booboo;
    }
    

    public function getClimate()
    {
        if ($this->_climate === null)
            $this->_climate = new Climate();

        return $this->_climate;
    }
    

    public function getContain()
    {
        if ($this->_contain === null)
            $this->_contain = new Contain();

        return $this->_contain;
    }
    

    public function getCsvService()
    {
        if ($this->_csvService === null)
            $this->_csvService = new CsvService();

        return $this->_csvService;
    }
    

    public function getFakerService()
    {
        if ($this->_fakerService === null)
            $this->_fakerService = new FakerService();

        return $this->_fakerService;
    }
    

    public function getFractalService()
    {
        if ($this->_fractalService === null)
            $this->_fractalService = new FractalService();

        return $this->_fractalService;
    }
    

    public function getGlideService()
    {
        if ($this->_glideService === null)
            $this->_glideService = new GlideService();

        return $this->_glideService;
    }
    

    public function getPeriodService()
    {
        if ($this->_periodService === null)
            $this->_periodService = new PeriodService();

        return $this->_periodService;
    }
    

    public function getRoute()
    {
        if ($this->_route === null)
            $this->_route = new Route();

        return $this->_route;
    }
    

    public function getRouteSymfony()
    {
        if ($this->_routeSymfony === null)
            $this->_routeSymfony = new RouteSymfony();

        return $this->_routeSymfony;
    }
    

    public function getSomeController()
    {
        if ($this->_someController === null)
            $this->_someController = new SomeController();

        return $this->_someController;
    }
    


}
