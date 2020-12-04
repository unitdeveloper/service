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

use zetsoft\service\reacts\ChildProcess;
use zetsoft\service\reacts\FileSystem;
use zetsoft\service\reacts\Loop;
use zetsoft\service\reacts\NetteFinder;
use zetsoft\service\reacts\ReactPhp;
use zetsoft\service\reacts\saneInterface;
use zetsoft\service\reacts\SpatieArrayFunctions;
use zetsoft\service\reacts\SpatieUrl;
use yii\base\Component;



/**
 *
* @property ChildProcess $childProcess
* @property FileSystem $fileSystem
* @property Loop $loop
* @property NetteFinder $netteFinder
* @property ReactPhp $reactPhp
* @property saneInterface $saneInterface
* @property SpatieArrayFunctions $spatieArrayFunctions
* @property SpatieUrl $spatieUrl

 */

class Reacts extends Component
{

    
    private $_childProcess;
    private $_fileSystem;
    private $_loop;
    private $_netteFinder;
    private $_reactPhp;
    private $_saneInterface;
    private $_spatieArrayFunctions;
    private $_spatieUrl;

    
    public function getChildProcess()
    {
        if ($this->_childProcess === null)
            $this->_childProcess = new ChildProcess();

        return $this->_childProcess;
    }
    

    public function getFileSystem()
    {
        if ($this->_fileSystem === null)
            $this->_fileSystem = new FileSystem();

        return $this->_fileSystem;
    }
    

    public function getLoop()
    {
        if ($this->_loop === null)
            $this->_loop = new Loop();

        return $this->_loop;
    }
    

    public function getNetteFinder()
    {
        if ($this->_netteFinder === null)
            $this->_netteFinder = new NetteFinder();

        return $this->_netteFinder;
    }
    

    public function getReactPhp()
    {
        if ($this->_reactPhp === null)
            $this->_reactPhp = new ReactPhp();

        return $this->_reactPhp;
    }
    

    public function getSaneInterface()
    {
        if ($this->_saneInterface === null)
            $this->_saneInterface = new saneInterface();

        return $this->_saneInterface;
    }
    

    public function getSpatieArrayFunctions()
    {
        if ($this->_spatieArrayFunctions === null)
            $this->_spatieArrayFunctions = new SpatieArrayFunctions();

        return $this->_spatieArrayFunctions;
    }
    

    public function getSpatieUrl()
    {
        if ($this->_spatieUrl === null)
            $this->_spatieUrl = new SpatieUrl();

        return $this->_spatieUrl;
    }
    


}
