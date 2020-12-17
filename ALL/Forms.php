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

use zetsoft\service\forms\Active;
use zetsoft\service\forms\AjaxData;
use zetsoft\service\forms\Ajaxer;
use zetsoft\service\forms\DataTable;
use zetsoft\service\forms\Detail;
use zetsoft\service\forms\Dynas;
use zetsoft\service\forms\Export;
use zetsoft\service\forms\Former;
use zetsoft\service\forms\Import;
use zetsoft\service\forms\Modelz;
use zetsoft\service\forms\Multi;
use zetsoft\service\forms\Tabular;
use zetsoft\service\forms\WiData;
use zetsoft\service\forms\ZPjax;
use yii\base\Component;



/**
 *
* @property Active $active
* @property AjaxData $ajaxData
* @property Ajaxer $ajaxer
* @property DataTable $dataTable
* @property Detail $detail
* @property Dynas $dynas
* @property Export $export
* @property Former $former
* @property Import $import
* @property Modelz $modelz
* @property Multi $multi
* @property Tabular $tabular
* @property WiData $wiData
* @property ZPjax $zPjax

 */

class Forms extends Component
{

    
    private $_active;
    private $_ajaxData;
    private $_ajaxer;
    private $_dataTable;
    private $_detail;
    private $_dynas;
    private $_export;
    private $_former;
    private $_import;
    private $_modelz;
    private $_multi;
    private $_tabular;
    private $_wiData;
    private $_zPjax;

    
    public function getActive()
    {
        if ($this->_active === null)
            $this->_active = new Active();

        return $this->_active;
    }
    

    public function getAjaxData()
    {
        if ($this->_ajaxData === null)
            $this->_ajaxData = new AjaxData();

        return $this->_ajaxData;
    }
    

    public function getAjaxer()
    {
        if ($this->_ajaxer === null)
            $this->_ajaxer = new Ajaxer();

        return $this->_ajaxer;
    }
    

    public function getDataTable()
    {
        if ($this->_dataTable === null)
            $this->_dataTable = new DataTable();

        return $this->_dataTable;
    }
    

    public function getDetail()
    {
        if ($this->_detail === null)
            $this->_detail = new Detail();

        return $this->_detail;
    }
    

    public function getDynas()
    {
        if ($this->_dynas === null)
            $this->_dynas = new Dynas();

        return $this->_dynas;
    }
    

    public function getExport()
    {
        if ($this->_export === null)
            $this->_export = new Export();

        return $this->_export;
    }
    

    public function getFormer()
    {
        if ($this->_former === null)
            $this->_former = new Former();

        return $this->_former;
    }
    

    public function getImport()
    {
        if ($this->_import === null)
            $this->_import = new Import();

        return $this->_import;
    }
    

    public function getModelz()
    {
        if ($this->_modelz === null)
            $this->_modelz = new Modelz();

        return $this->_modelz;
    }
    

    public function getMulti()
    {
        if ($this->_multi === null)
            $this->_multi = new Multi();

        return $this->_multi;
    }
    

    public function getTabular()
    {
        if ($this->_tabular === null)
            $this->_tabular = new Tabular();

        return $this->_tabular;
    }
    

    public function getWiData()
    {
        if ($this->_wiData === null)
            $this->_wiData = new WiData();

        return $this->_wiData;
    }
    

    public function getZPjax()
    {
        if ($this->_zPjax === null)
            $this->_zPjax = new ZPjax();

        return $this->_zPjax;
    }
    


}
