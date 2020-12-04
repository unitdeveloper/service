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

use zetsoft\service\parser\HtmlCompress;
use zetsoft\service\parser\HtmlPurifier;
use zetsoft\service\parser\JaegerQuerylist;
use zetsoft\service\parser\NathanmacParser;
use zetsoft\service\parser\NikicPhpParser;
use zetsoft\service\parser\PhpDiff;
use zetsoft\service\parser\PhpExcelImport;
use zetsoft\service\parser\PhpHtmlParser;
use zetsoft\service\parser\PhpSpreadsheet;
use zetsoft\service\parser\Phpwee;
use zetsoft\service\parser\RodenastyleStreamParser;
use zetsoft\service\parser\SebastianDiff;
use zetsoft\service\parser\ShuchkinSimplexlsxExcelParser;
use yii\base\Component;



/**
 *
* @property HtmlCompress $htmlCompress
* @property HtmlPurifier $htmlPurifier
* @property JaegerQuerylist $jaegerQuerylist
* @property NathanmacParser $nathanmacParser
* @property NikicPhpParser $nikicPhpParser
* @property PhpDiff $phpDiff
* @property PhpExcelImport $phpExcelImport
* @property PhpHtmlParser $phpHtmlParser
* @property PhpSpreadsheet $phpSpreadsheet
* @property Phpwee $phpwee
* @property RodenastyleStreamParser $rodenastyleStreamParser
* @property SebastianDiff $sebastianDiff
* @property ShuchkinSimplexlsxExcelParser $shuchkinSimplexlsxExcelParser

 */

class Parser extends Component
{

    
    private $_htmlCompress;
    private $_htmlPurifier;
    private $_jaegerQuerylist;
    private $_nathanmacParser;
    private $_nikicPhpParser;
    private $_phpDiff;
    private $_phpExcelImport;
    private $_phpHtmlParser;
    private $_phpSpreadsheet;
    private $_phpwee;
    private $_rodenastyleStreamParser;
    private $_sebastianDiff;
    private $_shuchkinSimplexlsxExcelParser;

    
    public function getHtmlCompress()
    {
        if ($this->_htmlCompress === null)
            $this->_htmlCompress = new HtmlCompress();

        return $this->_htmlCompress;
    }
    

    public function getHtmlPurifier()
    {
        if ($this->_htmlPurifier === null)
            $this->_htmlPurifier = new HtmlPurifier();

        return $this->_htmlPurifier;
    }
    

    public function getJaegerQuerylist()
    {
        if ($this->_jaegerQuerylist === null)
            $this->_jaegerQuerylist = new JaegerQuerylist();

        return $this->_jaegerQuerylist;
    }
    

    public function getNathanmacParser()
    {
        if ($this->_nathanmacParser === null)
            $this->_nathanmacParser = new NathanmacParser();

        return $this->_nathanmacParser;
    }
    

    public function getNikicPhpParser()
    {
        if ($this->_nikicPhpParser === null)
            $this->_nikicPhpParser = new NikicPhpParser();

        return $this->_nikicPhpParser;
    }
    

    public function getPhpDiff()
    {
        if ($this->_phpDiff === null)
            $this->_phpDiff = new PhpDiff();

        return $this->_phpDiff;
    }
    

    public function getPhpExcelImport()
    {
        if ($this->_phpExcelImport === null)
            $this->_phpExcelImport = new PhpExcelImport();

        return $this->_phpExcelImport;
    }
    

    public function getPhpHtmlParser()
    {
        if ($this->_phpHtmlParser === null)
            $this->_phpHtmlParser = new PhpHtmlParser();

        return $this->_phpHtmlParser;
    }
    

    public function getPhpSpreadsheet()
    {
        if ($this->_phpSpreadsheet === null)
            $this->_phpSpreadsheet = new PhpSpreadsheet();

        return $this->_phpSpreadsheet;
    }
    

    public function getPhpwee()
    {
        if ($this->_phpwee === null)
            $this->_phpwee = new Phpwee();

        return $this->_phpwee;
    }
    

    public function getRodenastyleStreamParser()
    {
        if ($this->_rodenastyleStreamParser === null)
            $this->_rodenastyleStreamParser = new RodenastyleStreamParser();

        return $this->_rodenastyleStreamParser;
    }
    

    public function getSebastianDiff()
    {
        if ($this->_sebastianDiff === null)
            $this->_sebastianDiff = new SebastianDiff();

        return $this->_sebastianDiff;
    }
    

    public function getShuchkinSimplexlsxExcelParser()
    {
        if ($this->_shuchkinSimplexlsxExcelParser === null)
            $this->_shuchkinSimplexlsxExcelParser = new ShuchkinSimplexlsxExcelParser();

        return $this->_shuchkinSimplexlsxExcelParser;
    }
    


}
