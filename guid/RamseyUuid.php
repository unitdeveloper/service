<?php

namespace zetsoft\service\guid;

use Ramsey\Uuid\Uuid;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendors/string/ALL/vendor/autoload.php';

/**
 * @author NurbekMakhmudov
 * @todo PHP library for generating and working with universally unique identifiers (UUIDs).
 * https://packagist.org/packages/ramsey/uuid
 * https://github.com/ramsey/uuid
 */
class RamseyUuid extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-10

    /**
     * @var $uuid
     */
    private $uuid;

    /**
     * initialization
     */
    public function init()
    {
        parent::init();
        $this->uuid = Uuid::uuid4();  // default version. This generates a Version 4: Random UUID.
    }

    /**
     * @return mixed
     * @author NurbekMakhmudov
     * creating a Random UUID.
     */
    public function create()
    {
        return $this->uuid->toString();
    }

    /**
     * @param $version
     * @author NurbekMakhmudov
     * https://uuid.ramsey.dev/en/latest/quickstart.html
     * @todo Select the version to get different results
     *   Uuid::uuid1()  This generates a Version 1: Time-based UUID.
     *   Uuid::uuid2()  This generates a Version 2: DCE Security UUID.
     *   Uuid::uuid3()  This generates a Version 3: Name-based (MD5) UUID.
     *   Uuid::uuid4()  This generates a Version 4: Random UUID.
     *   Uuid::uuid5()  This generates a Version 5: Name-based (SHA-1) UUID.
     *   Uuid::uuid6()  This generates a Version 6: Ordered-Time UUID.
     */
    public function createWithVersion($version)
    {
        if ($version === 1)
            $this->uuid = Uuid::uuid1();

        if ($version === 2)
            $this->uuid = Uuid::uuid2(Uuid::DCE_DOMAIN_PERSON);

        if ($version === 3)
            $this->uuid = Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://www.sample.net');

        if ($version === 4)
            $this->uuid = Uuid::uuid4();

        if ($version === 5)
            $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://www.sample.net');

        if ($version === 6)
            $this->uuid = Uuid::uuid6();

        return $this->uuid->toString();
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->uuid->getFields()->getVersion();
    }

    //end|NurbekMakhmudov|2020-10-10

    // pay OK
}
