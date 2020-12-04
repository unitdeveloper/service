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

use zetsoft\service\menu\ALL;
use zetsoft\service\menu\ALLNew;
use zetsoft\service\menu\Csvs;
use zetsoft\service\menu\Davlat;
use zetsoft\service\menu\Eyuf;
use zetsoft\service\menu\Gits;
use zetsoft\service\menu\Json;
use zetsoft\service\menu\Libra;
use zetsoft\service\menu\Mycompany;
use zetsoft\service\menu\Nestable;
use zetsoft\service\menu\OrderNorms;
use zetsoft\service\menu\Orders;
use yii\base\Component;



/**
 *
* @property ALL $aLL
* @property ALLNew $aLLNew
* @property Csvs $csvs
* @property Davlat $davlat
* @property Eyuf $eyuf
* @property Gits $gits
* @property Json $json
* @property Libra $libra
* @property Mycompany $mycompany
* @property Nestable $nestable
* @property OrderNorms $orderNorms
* @property Orders $orders

 */

class Menu extends Component
{

    
    private $_aLL;
    private $_aLLNew;
    private $_csvs;
    private $_davlat;
    private $_eyuf;
    private $_gits;
    private $_json;
    private $_libra;
    private $_mycompany;
    private $_nestable;
    private $_orderNorms;
    private $_orders;

    
    public function getALL()
    {
        if ($this->_aLL === null)
            $this->_aLL = new ALL();

        return $this->_aLL;
    }
    

    public function getALLNew()
    {
        if ($this->_aLLNew === null)
            $this->_aLLNew = new ALLNew();

        return $this->_aLLNew;
    }
    

    public function getCsvs()
    {
        if ($this->_csvs === null)
            $this->_csvs = new Csvs();

        return $this->_csvs;
    }
    

    public function getDavlat()
    {
        if ($this->_davlat === null)
            $this->_davlat = new Davlat();

        return $this->_davlat;
    }
    

    public function getEyuf()
    {
        if ($this->_eyuf === null)
            $this->_eyuf = new Eyuf();

        return $this->_eyuf;
    }
    

    public function getGits()
    {
        if ($this->_gits === null)
            $this->_gits = new Gits();

        return $this->_gits;
    }
    

    public function getJson()
    {
        if ($this->_json === null)
            $this->_json = new Json();

        return $this->_json;
    }
    

    public function getLibra()
    {
        if ($this->_libra === null)
            $this->_libra = new Libra();

        return $this->_libra;
    }
    

    public function getMycompany()
    {
        if ($this->_mycompany === null)
            $this->_mycompany = new Mycompany();

        return $this->_mycompany;
    }
    

    public function getNestable()
    {
        if ($this->_nestable === null)
            $this->_nestable = new Nestable();

        return $this->_nestable;
    }
    

    public function getOrderNorms()
    {
        if ($this->_orderNorms === null)
            $this->_orderNorms = new OrderNorms();

        return $this->_orderNorms;
    }
    

    public function getOrders()
    {
        if ($this->_orders === null)
            $this->_orders = new Orders();

        return $this->_orders;
    }
    


}
