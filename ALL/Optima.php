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

use zetsoft\service\optima\IvoglentYii2Minify;
use zetsoft\service\optima\MarcocesaratoMinifier;
use zetsoft\service\optima\TinyHtmlMinifier;
use zetsoft\service\optima\VokuHtmlMin;
use zetsoft\service\optima\WyrihaximusHtmlCompress;
use yii\base\Component;



/**
 *
* @property IvoglentYii2Minify $ivoglentYii2Minify
* @property MarcocesaratoMinifier $marcocesaratoMinifier
* @property TinyHtmlMinifier $tinyHtmlMinifier
* @property VokuHtmlMin $vokuHtmlMin
* @property WyrihaximusHtmlCompress $wyrihaximusHtmlCompress

 */

class Optima extends Component
{

    
    private $_ivoglentYii2Minify;
    private $_marcocesaratoMinifier;
    private $_tinyHtmlMinifier;
    private $_vokuHtmlMin;
    private $_wyrihaximusHtmlCompress;

    
    public function getIvoglentYii2Minify()
    {
        if ($this->_ivoglentYii2Minify === null)
            $this->_ivoglentYii2Minify = new IvoglentYii2Minify();

        return $this->_ivoglentYii2Minify;
    }
    

    public function getMarcocesaratoMinifier()
    {
        if ($this->_marcocesaratoMinifier === null)
            $this->_marcocesaratoMinifier = new MarcocesaratoMinifier();

        return $this->_marcocesaratoMinifier;
    }
    

    public function getTinyHtmlMinifier()
    {
        if ($this->_tinyHtmlMinifier === null)
            $this->_tinyHtmlMinifier = new TinyHtmlMinifier();

        return $this->_tinyHtmlMinifier;
    }
    

    public function getVokuHtmlMin()
    {
        if ($this->_vokuHtmlMin === null)
            $this->_vokuHtmlMin = new VokuHtmlMin();

        return $this->_vokuHtmlMin;
    }
    

    public function getWyrihaximusHtmlCompress()
    {
        if ($this->_wyrihaximusHtmlCompress === null)
            $this->_wyrihaximusHtmlCompress = new WyrihaximusHtmlCompress();

        return $this->_wyrihaximusHtmlCompress;
    }
    


}
