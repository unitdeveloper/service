<?php

namespace zetsoft\service\parser;

require Root . '/vendors/parser/excel/vendor/autoload.php';

use zetsoft\system\kernels\ZFrame;
use Nathanmac\Utilities\Parser\Parser;

/**
 * Class NathanmacParser
 * @package zetsoft\service\parser\nathanmac
 * @author NurbekMakhmudov
 * https://github.com/nathanmac/Parser
 * https://packagist.org/packages/nathanmac/parser
 * @todo Simple PHP Parser Library for API Development, parse a post http payload into a php array.
 */
class NathanmacParser extends ZFrame
{
    //start|NurbekMakhmudov|2020-10-11

    /**
     * @var $parser
     */
    private $parser;

    /**
     * initialization
     */
    public function init()
    {
        parent::init();
        $this->parser = new Parser();
    }

    #region parse functions

    /**
     * @param $jsonSource
     * @return mixed
     * @author NurbekMakhmudov
     * @todo Parse json to php array
     */
    public function parseJson($jsonSource)
    {
        $res = $this->parser->json($jsonSource);
        return $res;
    }

    /**
     * @param $queryStringSource
     * @return mixed
     * @author NurbekMakhmudov
     * @todo Parse string to php array
     */
    public function parseQueryString($queryStringSource)
    {
        $res = $this->parser->querystr($queryStringSource);
        return $res;
    }

    /**
     * @param $xmlSource
     * @return mixed
     * @author NurbekMakhmudov
     * @todo Parse XML to php array
     */
    public function parseXml($xmlSource)
    {
        $res = $this->parser->xml($xmlSource);
        return $res;
    }

    /**
     * @param $yamlSource
     * @return mixed
     * @author NurbekMakhmudov
     * @todo Parse YAML to php array
     */
    public function parseYaml($yamlSource)
    {
        $res = $this->parser->yaml($yamlSource);
        return $res;
    }


    #endregion


    #region examples

    public function jsonParserExample()
    {
        $jsonSource = '{
                "message": {
                    "author": "Nurbek Makhmudov",
                    "subject": "Parse json to array",
                    "body": "Hello, contact me if you dont know how to use it"
                }
            }';

       $res =  $this->parseJson($jsonSource);
       print_r($res);
    }

    public function xmlParserExample()
    {
        $xmlSource = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                        <xml>
                            <message>
                                <author>Nurbek Makhmudov</author>
					            <subject>Parse XML to array</subject>
					            <body>Hello, contact me if you dont know how to use it</body>
                            </message>
                        </xml>";

       $res =  $this->parseXml($xmlSource);
       print_r($res);
    }

    public function queryStringParserExample()
    {
        $queryStringSource = 'author=Nurbek Makhmudov&subject=Parse Query String to array&body=Hello, contact me if you dont know how to use it';
        $res =  $this->parseQueryString($queryStringSource);
        print_r($res);
    }

    public function yamlParserExample()
    {
        $yamlSource = '
				---
				message:
				    author: "Nurbek Makhmudov"
				    subject: "Parse YAML to array"
				    body: "Hello, contact me if you dont know how to use it"
				';
        $res =  $this->parseYaml($yamlSource);
        print_r($res);
    }

    #endregion


    //end|NurbekMakhmudov|2020-10-11

    // pay  OK

}
