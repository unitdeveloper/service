<?

/**
 *
 * @author Xolmat Ravshanov
 */



namespace zetsoft\service\cpas;


use Faker\Provider\en_US\Text;
use GuzzleHttp\Client;
use zetsoft\system\kernels\ZFrame;

class CpasFinance extends ZFrame
{


    #region vars
    public $client;
    public $id;
    public $status_cpa;
    #endregion

    #region Cores
    public function init()
    {
        parent::init();
        $this->client = new Client();

    }
    #endregion



}

