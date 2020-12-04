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

use zetsoft\service\https\Amphp;
use zetsoft\service\https\GooglePlacesApi;
use zetsoft\service\https\Guzzle;
use zetsoft\service\https\GuzzleTwo;
use zetsoft\service\https\Httpful;
use zetsoft\service\https\MashapeHttp;
use zetsoft\service\https\Utility;
use yii\base\Component;



/**
 *
* @property Amphp $amphp
* @property GooglePlacesApi $googlePlacesApi
* @property Guzzle $guzzle
* @property GuzzleTwo $guzzleTwo
* @property Httpful $httpful
* @property MashapeHttp $mashapeHttp
* @property Utility $utility

 */

class Https extends Component
{

    
    private $_amphp;
    private $_googlePlacesApi;
    private $_guzzle;
    private $_guzzleTwo;
    private $_httpful;
    private $_mashapeHttp;
    private $_utility;

    
    public function getAmphp()
    {
        if ($this->_amphp === null)
            $this->_amphp = new Amphp();

        return $this->_amphp;
    }
    

    public function getGooglePlacesApi()
    {
        if ($this->_googlePlacesApi === null)
            $this->_googlePlacesApi = new GooglePlacesApi();

        return $this->_googlePlacesApi;
    }
    

    public function getGuzzle()
    {
        if ($this->_guzzle === null)
            $this->_guzzle = new Guzzle();

        return $this->_guzzle;
    }
    

    public function getGuzzleTwo()
    {
        if ($this->_guzzleTwo === null)
            $this->_guzzleTwo = new GuzzleTwo();

        return $this->_guzzleTwo;
    }
    

    public function getHttpful()
    {
        if ($this->_httpful === null)
            $this->_httpful = new Httpful();

        return $this->_httpful;
    }
    

    public function getMashapeHttp()
    {
        if ($this->_mashapeHttp === null)
            $this->_mashapeHttp = new MashapeHttp();

        return $this->_mashapeHttp;
    }
    

    public function getUtility()
    {
        if ($this->_utility === null)
            $this->_utility = new Utility();

        return $this->_utility;
    }
    


}
