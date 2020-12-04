<?php


namespace zetsoft\service\soaps;

require Root . '/vendori/soaps/vendor/autoload.php';

/*
 * @package phpro/soap-client
 * @author SukhrobNuraliev
 * https://packagist.org/packages/phpro/soap-client
 */

use zetsoft\system\kernels\ZFrame;
use WsdlToPhp\WsSecurity\WsSecurity as WsSec;


class Wssecurity extends ZFrame
{
    public function example()
    {
        /**
         * @var \SoapHeader
         */
        $soapHeader = WsSec::createWsSecuritySoapHeader('login', 'password', true);
        /**
         * Send the request
         */
        $soapClient = new \SoapClient('wsdl_url');
        $soapClient->__setSoapHeaders($soapHeader);
        $soapClient->__soapCall('echoVoid', []);
    }
}
