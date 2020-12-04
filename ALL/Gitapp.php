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

use zetsoft\service\gitapp\Execs;
use zetsoft\service\gitapp\Gitelephant;
use zetsoft\service\gitapp\Gitlib;
use zetsoft\service\gitapp\GitPhp;
use zetsoft\service\gitapp\GitWrapper;
use zetsoft\service\gitapp\PhpGitHooks;
use zetsoft\service\gitapp\Phploy;
use yii\base\Component;



/**
 *
* @property Execs $execs
* @property Gitelephant $gitelephant
* @property Gitlib $gitlib
* @property GitPhp $gitPhp
* @property GitWrapper $gitWrapper
* @property PhpGitHooks $phpGitHooks
* @property Phploy $phploy

 */

class Gitapp extends Component
{

    
    private $_execs;
    private $_gitelephant;
    private $_gitlib;
    private $_gitPhp;
    private $_gitWrapper;
    private $_phpGitHooks;
    private $_phploy;

    
    public function getExecs()
    {
        if ($this->_execs === null)
            $this->_execs = new Execs();

        return $this->_execs;
    }
    

    public function getGitelephant()
    {
        if ($this->_gitelephant === null)
            $this->_gitelephant = new Gitelephant();

        return $this->_gitelephant;
    }
    

    public function getGitlib()
    {
        if ($this->_gitlib === null)
            $this->_gitlib = new Gitlib();

        return $this->_gitlib;
    }
    

    public function getGitPhp()
    {
        if ($this->_gitPhp === null)
            $this->_gitPhp = new GitPhp();

        return $this->_gitPhp;
    }
    

    public function getGitWrapper()
    {
        if ($this->_gitWrapper === null)
            $this->_gitWrapper = new GitWrapper();

        return $this->_gitWrapper;
    }
    

    public function getPhpGitHooks()
    {
        if ($this->_phpGitHooks === null)
            $this->_phpGitHooks = new PhpGitHooks();

        return $this->_phpGitHooks;
    }
    

    public function getPhploy()
    {
        if ($this->_phploy === null)
            $this->_phploy = new Phploy();

        return $this->_phploy;
    }
    


}
