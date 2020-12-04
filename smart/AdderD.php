<?php


namespace zetsoft\service\smart;

use Symfony\Component\Filesystem\Filesystem;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

/**
 * Class    Adder
 * @package zetsoft\service\smart
 * @author Daho
 */
class AdderD extends ZFrame
{


    #region Clone
    /**
     *
     * Function  clone
     * @author Daho
     */
    public function clone(){
        Az::debug('Cloning app files...');
        $this->cloneFolders();
        $this->createAppConf();
        $this->createAppIndex();
        Az::debug('Creating Nginx configurations...');
        $this->nginxConf();
        Az::debug('Add domain names to host...');
        $this->updateHosts();
        Az::debug('Creating env for the new app...');
        $this->createEnv();
        Az::debug('Creating connectin file to the new app...');
        $this->createConnectionFile();
        Az::debug('Cloning DB...');
        $this->createDb();
        Az::debug('Check Nginx and restart...');
        $this->restartNginx();
        Az::debug('The new app creating successfully!');

    }

    #endregion

    #region File system
    public function cloneFolders(){
        $theme_path = Root . '/webhtm/thm/';
        $appPath = Root . '/webhtm/apps/';
        $filesys = new Filesystem();

        $filesys->mirror($theme_path . $this->theme, $appPath . $this->appName);

    }
    

    #endregion

}
