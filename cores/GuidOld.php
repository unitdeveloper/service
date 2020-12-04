<?php

/**
 * @author NurbekMakhmudov
 * @todo A Simple GUID generator Package for PHP.
 * https://packagist.org/packages/sudiptpa/guid
 */

namespace zetsoft\service\cores;

use Sujip\Guid\Guid as GuidGenerator;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendori/string/ALl/vendor/autoload.php';

class Guid extends ZFrame
{
    private $guid;

    public function init()
    {
        parent::init();
        $this->guid = new GuidGenerator();
    }

    public function create()
    {
        //return strtolower($this->guid->create());
        return $this->guid->create();
    }
}
