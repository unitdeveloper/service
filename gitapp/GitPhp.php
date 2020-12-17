<?php

namespace zetsoft\service\gitapp;


require Root . '/vendors/gitapp/vendor/autoload.php';

use Cz\Git\GitRepository;
use zetsoft\system\kernels\ZFrame;

/*
 * class GitPhp
 * @package zetsoft/service/gitapp
 * @author SukhrobNuraliev
 * https://packagist.org/packages/czproject/git-php
 */

class GitPhp extends ZFrame
{

    #region example
// ishladi
    public function example()
    {
        // create repo object
        $repo = GitRepository::init(__DIR__ . '/example');

        // create a new file in repo
        $filename = $repo->getRepositoryPath() . '/readme.txt';
        file_put_contents($filename, "This is test text");

        // commit
        $repo->addFile($filename);
        $repo->commit('first commit ');

        echo "success";
    }

    #endregion
}
