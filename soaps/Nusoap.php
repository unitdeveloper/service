<?php


namespace zetsoft\service\soaps;

require Root . '/vendors/soaps/vendor/autoload.php';

/*
 * @package econea/nusoap
 * @author SukhrobNuraliev
 * https://packagist.org/packages/econea/nusoap
 */

use zetsoft\system\kernels\ZFrame;


class Nusoap extends ZFrame
{
    public function example()
    {
        // Config
        $client = new nusoap_client('example.com/api/v1', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = FALSE;

        // Calls
        $result = $client->call($action, $data);
    }
}
