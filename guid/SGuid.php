<?php

/**
 * @author NurbekMakhmudov
 * @todo A Simple GUID generator Package for PHP.
 * https://packagist.org/packages/sudiptpa/guid
 * https://github.com/sudiptpa/guid
 */

namespace zetsoft\service\guid;

use Sujip\Guid\Guid;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendori/string/ALL/vendor/autoload.php';

/**
 * Class SGuid
 * This package is useful for creating globally unique identifiers (GUID).
 * @package zetsoft\service\guid
 * @author NurbekMakhmudov
 * https://github.com/sudiptpa/guid
 */
class SGuid extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-10

    /**
     * @var $guid
     */
    private $guid;

    /**
     *  initialization
     */
    public function init()
    {
        parent::init();
        $this->guid = new Guid();
    }

    /**
     * @return mixed
     *  @author NurbekMakhmudov
     *  creating a convenient installation experience for you.
     */
    public function create()
    {
         return $this->guid->create();
    }

    /**
     *  @return mixed
     *  @author NurbekMakhmudov
     *  creating a lowercase convenient installation experience for you.
     */
    public function createLowercase()
    {
         return strtolower($this->guid->create());
    }

    //end|NurbekMakhmudov|2020-10-10

    // pay OK
}