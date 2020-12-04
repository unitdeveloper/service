<?php

/**
 * @author Muminiv Umid
 * @license AlisherXayrillayev
 *
 * Class NikicPhpParser
 * @package zetsoft\service\parser
 * https://packagist.org/packages/nikic/php-parser
 */

namespace zetsoft\service\parser;

require Root . '/vendori/parser/excel/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;


class NikicPhpParser extends ZFrame
{

    //start|AlisherXayrillayev|2020-10-12



    #region Example

    public function example()
    {
        $this->parseFromPHP();
    }

    public function parseFromPHP()
    {
        $sourcePHP = __DIR__ . '/sample/source.php';
        $resultPHP = __DIR__ . '/results/resultSource_NikicPhpParser.php';

        $code = file_get_contents($sourcePHP);

        $parseCode = $this->parsePHP($code, ParserFactory::PREFER_PHP7);

        $dumper = new NodeDumper();
        file_put_contents($resultPHP, $dumper->dump($parseCode));
    }

    /**
     * @param $code
     * @param $parcerFactory type ParserFactory
     * @return mixed|\PhpParser\Node[]|void|null
     */
    public function parsePHP($code, $parserFactory) {
        $parser = (new ParserFactory())->create($parserFactory);
        $parseCode = null;

        try {
            $parseCode = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }
        return $parseCode;
    }


    #endregion


    //end|AlisherXayrillayev|2020-10-12


    #region Old Code
    public function test()
    {
        $this->parse();
    }

    public function parse()
    {
        $code = <<<'CODE'
        <?php
        
        function test($foo)
        {
            var_dump($foo);
        }
        ?>
        CODE;

                $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
                try {
                    $ast = $parser->parse($code);
                } catch (Error $error) {
                    echo "Parse error: {$error->getMessage()}\n";
                    return;
                }

                $dumper = new NodeDumper();
                echo $dumper->dump($ast) . "\n";

    }

    #endregion

}