<?php

namespace zetsoft\service\optima;

require Root . '/vendori/optima/html/vendor/autoload.php'; //Exception 'Error' with message 'Class 'Middlewares\Utils\Factory' not found'

/**
 * Author:
 * @licence: AlisherXayrillayev
 */

/**
 * Class MiddlewaresMinifier
 * @package zetsoft\service\parser
 * https://packagist.org/packages/middlewares/minifier
 *
 *
 *
 * Not working package
 */


use Middlewares\Minifier;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use zetsoft\system\kernels\ZFrame;

class MiddlewaresMinifier extends ZFrame
{


    //start|AlisherXayrillayev|2020-10-12

    //error Exception 'Error' with message 'Class 'Middlewares\Utils\Factory' not found'

    #region Example
    public function example(){
        $this->exampleOne();
    }

    public function exampleOne(){
        $compressor = Factory::construct();
        $mimeType = 'text/html';

        $minifier = new Minifier($compressor, $mimeType);
//        vdd($minifier);
        Dispatcher::run([
            Minifier::html(),
            Minifier::css(),
            Minifier::js(),
        ]);
    }

    #endregion

    //start|AlisherXayrillayev|2020-10-12

    #region Old Code
    public function minifierProvider(): array
    {

        $fileSource = __DIR__ . '/sample/demo.html';
        $fileResult = __DIR__ . '/results/htmlResult.html';
        $data = [
                'text/html',
                file_get_contents($fileSource),
                trim(file_get_contents($fileResult)),
        ];

        return $data;
    }

    /**
     * @dataProvider minifierProvider
     */
    public function testMinifier(string $mime, string $content, string $expected)
    {
        $response = Dispatcher::run([
            Minifier::html(),
            function () use ($mime, $content) {
                $response = Factory::createResponse();
                $response->getBody()->write($content);

                return $response->withHeader('Content-Type', $mime);
            },
        ]);

        $this->assertEquals($expected, (string)$response->getBody());
    }
    #endregion
}

