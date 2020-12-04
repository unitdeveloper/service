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

use zetsoft\service\search\ActiveData;
use zetsoft\service\search\ArrayData;
use zetsoft\service\search\ElasticSearch;
use zetsoft\service\search\Manticore;
use zetsoft\service\search\Sorter;
use zetsoft\service\search\SphinxClient;
use zetsoft\service\search\SphinxService;
use zetsoft\service\search\TntSearchService;
use yii\base\Component;



/**
 *
* @property ActiveData $activeData
* @property ArrayData $arrayData
* @property ElasticSearch $elasticSearch
* @property Manticore $manticore
* @property Sorter $sorter
* @property SphinxClient $sphinxClient
* @property SphinxService $sphinxService
* @property TntSearchService $tntSearchService

 */

class Search extends Component
{

    
    private $_activeData;
    private $_arrayData;
    private $_elasticSearch;
    private $_manticore;
    private $_sorter;
    private $_sphinxClient;
    private $_sphinxService;
    private $_tntSearchService;

    
    public function getActiveData()
    {
        if ($this->_activeData === null)
            $this->_activeData = new ActiveData();

        return $this->_activeData;
    }
    

    public function getArrayData()
    {
        if ($this->_arrayData === null)
            $this->_arrayData = new ArrayData();

        return $this->_arrayData;
    }
    

    public function getElasticSearch()
    {
        if ($this->_elasticSearch === null)
            $this->_elasticSearch = new ElasticSearch();

        return $this->_elasticSearch;
    }
    

    public function getManticore()
    {
        if ($this->_manticore === null)
            $this->_manticore = new Manticore();

        return $this->_manticore;
    }
    

    public function getSorter()
    {
        if ($this->_sorter === null)
            $this->_sorter = new Sorter();

        return $this->_sorter;
    }
    

    public function getSphinxClient()
    {
        if ($this->_sphinxClient === null)
            $this->_sphinxClient = new SphinxClient();

        return $this->_sphinxClient;
    }
    

    public function getSphinxService()
    {
        if ($this->_sphinxService === null)
            $this->_sphinxService = new SphinxService();

        return $this->_sphinxService;
    }
    

    public function getTntSearchService()
    {
        if ($this->_tntSearchService === null)
            $this->_tntSearchService = new TntSearchService();

        return $this->_tntSearchService;
    }
    


}
