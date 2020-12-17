<?php


namespace zetsoft\service\guid;

use PascalDeVink\ShortUuid\ShortUuid;
use Ramsey\Uuid\Uuid;
use zetsoft\system\kernels\ZFrame;

require Root . '/vendors/string/ALL/vendor/autoload.php';

/**
 * @author NurbekMakhmudov
 * @todo Shortuuid is a simple php library that generates concise, unambiguous, URL-safe UUIDs.
 * https://github.com/pascaldevink/shortuuid
 * https://packagist.org/packages/pascaldevink/shortuuid
 */
class PascaldevinkShortUUID extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-11

    /**
     * @var $shortUuid
     */
    private $shortUuid;

    /**
     * initialization
     */
    public function init()
    {
        parent::init();
        $this->shortUuid = new ShortUuid();
    }

    /**
     * @param $uuidForEncode
     * @return string
     * @author NurbekMakhmudov
     * @todo generated uuids to base57 using lowercase and uppercase letters and digits
     */
    public function encodeUuid($uuidForEncode)
    {
        if (!Uuid::isValid($uuidForEncode))
            return "The invalid standard UUID";

        return $this->shortUuid->encode(Uuid::fromString($uuidForEncode));
    }

    /**
     * @param $uuidForDecode
     * @return string
     * @author NurbekMakhmudov
     * @todo decode uuid from  base57
     */
    public function decodeUuid($uuidForDecode)
    {
        return $this->shortUuid->decode($uuidForDecode);
    }

    //end|NurbekMakhmudov|2020-10-11

    // pay OK

}
