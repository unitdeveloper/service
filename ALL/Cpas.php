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

use zetsoft\service\cpas\Cpa;
use zetsoft\service\cpas\CpasFinance;
use zetsoft\service\cpas\CpasLead;
use zetsoft\service\cpas\CpasStats;
use zetsoft\service\cpas\CpasStatsXolmat;
use zetsoft\service\cpas\GenerateLanding;
use yii\base\Component;



/**
 *
* @property Cpa $cpa
* @property CpasFinance $cpasFinance
* @property CpasLead $cpasLead
* @property CpasStats $cpasStats
* @property CpasStatsXolmat $cpasStatsXolmat
* @property GenerateLanding $generateLanding

 */

class Cpas extends Component
{

    
    private $_cpa;
    private $_cpasFinance;
    private $_cpasLead;
    private $_cpasStats;
    private $_cpasStatsXolmat;
    private $_generateLanding;

    
    public function getCpa()
    {
        if ($this->_cpa === null)
            $this->_cpa = new Cpa();

        return $this->_cpa;
    }
    

    public function getCpasFinance()
    {
        if ($this->_cpasFinance === null)
            $this->_cpasFinance = new CpasFinance();

        return $this->_cpasFinance;
    }
    

    public function getCpasLead()
    {
        if ($this->_cpasLead === null)
            $this->_cpasLead = new CpasLead();

        return $this->_cpasLead;
    }
    

    public function getCpasStats()
    {
        if ($this->_cpasStats === null)
            $this->_cpasStats = new CpasStats();

        return $this->_cpasStats;
    }
    

    public function getCpasStatsXolmat()
    {
        if ($this->_cpasStatsXolmat === null)
            $this->_cpasStatsXolmat = new CpasStatsXolmat();

        return $this->_cpasStatsXolmat;
    }
    

    public function getGenerateLanding()
    {
        if ($this->_generateLanding === null)
            $this->_generateLanding = new GenerateLanding();

        return $this->_generateLanding;
    }
    


}
