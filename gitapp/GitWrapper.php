<?php

namespace zetsoft\service\gitapp;


require Root.'/vendors/gitapp/vendor/autoload.php';


use GitWrapper\GitWrapper as Wrapper;
use zetsoft\system\kernels\ZFrame;

/*
 * class GitWrapper
 * @package zetsoft/service/gitapp
 * @author SukhrobNuraliev
 * https://packagist.org/packages/wyrihaximus/html-compress
 */

class GitWrapper extends ZFrame
{

   #region example
//ishladi
    public function example()
    {
        $gitWrapper = new Wrapper();

            // Clone a repo into, get a working copy object
            $git = $gitWrapper->cloneRepository('https://github.com/SukhrobNuraliev/Netflix-Clone.git', __DIR__ . '/example/Netflix-Clone');

            // Create a file in the working copy
            touch(__DIR__ . '/example/Netflix-Clone');
            echo "Success";
    }

    #endregion
}
