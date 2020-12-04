<?php


namespace zetsoft\service\soaps;

require Root . '/vendori/soaps/vendor/autoload.php';

/*
 * @package Wsdl2phpgenerator/Wsdl2phpgeneratort
 * @author NodirbekOmonov
 * https://github.com/wsdl2phpgenerator/wsdl2phpgenerator/blob/HEAD/docs/usage-and-options.md
 */

use zetsoft\system\kernels\ZFrame;
use Wsdl2PhpGenerator\Generator;
use Wsdl2PhpGenerator\Config;



class Wsdl2phpgenerator extends ZFrame
{
    public function example()
    {
        $generator = new Generator();
        $generator->generate(
            new Config(array(
                'inputFile' => 'http://www.webservicex.net/CurrencyConvertor.asmx?WSDL',
                'outputDir' => '/tmp/CurrencyConverter'
            ))
        );

        require '/tmp/CurrencyConverter/autoload.php';

        // A class will generated representing the service.
        // It is named after the element in the WSDL and has a method for each operation.
        $service = new \CurrencyConvertor();
        $request = new \ConversionRate(\Currency::USD, \Currency::EUR);
        $response = $service->ConversionRate($request);

        echo $response->getConversionRateResult();
    }
}
