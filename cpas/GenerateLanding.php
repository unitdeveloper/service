<?

/**
 *
 * @author Xolmat Ravshanov
 */


namespace zetsoft\service\cpas;


use GuzzleHttp\Client;
use zetsoft\models\cpas\CpasStream;
use zetsoft\system\kernels\ZFrame;

class GenerateLanding extends ZFrame
{
    public function run(int $stream_id)
    {
        $stream = CpasStream::findOne($stream_id);
        if ($stream === null){
            return false;
        }

    }
}

