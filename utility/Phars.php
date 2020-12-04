<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;

use Addvilz\Pharaoh\Builder;
use Herrera\Box\Box;
use Herrera\Box\StubGenerator;
use Phar;
use Symfony\Component\Finder\Finder;
use zetsoft\system\kernels\ZFrame;

require Root . '/ventest/phar/vendor/autoload.php';

class Phars extends ZFrame
{
    public $targetPath;
    public $pharName;
    public $desPath;
    public $rootPath;
    
    #region
    public function run()
    {
        $src = (new Finder())
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in($this->targetPath);

        $builder = (new Builder($this->pharName . '.phar', $this->desPath, $this->rootPath))
            ->addFinder($src)
            ->addFile($this->targetPath)
            ->build();
    }

}
