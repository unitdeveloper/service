<?php


namespace zetsoft\service\parser;

use zetsoft\system\kernels\ZFrame;

require Root . '/vendors/parser/excel/vendor/autoload.php';

/**
 * @author AlisherXayrillayev
 *
 * Class RodenastyleStreamParser
 * @package zetsoft\service\parser
 *
 * https://github.com/sergiorodenas/stream-parser
 * https://packagist.org/packages/rodenastyle/stream-parser
 */

use Rodenastyle\StreamParser\StreamParser;
use Tightenco\Collect\Support\Collection;

class RodenastyleStreamParser extends ZFrame
{

    public $result = array();

    public function init()
    {
        parent::init();
    }
    #region Example

    //xml example

    public function exampleXML()
    {
        $this->parseFromXML();
    }

    public function parseFromXML()
    {
        $sourceXML = __DIR__ . '/sample/source.xml';
        $resultPath = __DIR__ . '/results/resultSourceXML_RodenastyleStreamParser.txt';

        $result = $this->parseXML($sourceXML);
        vdd($result);
//        file_put_contents($resultPath, $result);
    }

    /**
     * @param $xml (string) xml file path
     * @return array
     */
    public function parseXML($xml){
        $this->result = array();
        StreamParser::xml($xml)->each(function(Collection $book){
            array_push($this->result, $book);
        });
        return $this->result;
    }

    //end example



    //json example

    public function exampleJSON()
    {
        $this->parseFromJSON();
    }

    public function parseFromJSON()
    {
        $sourceJSON = __DIR__ . '/sample/demo.json';
        $resultPath = __DIR__ . '/results/resultDemoJSON_RodenastyleStreamParser.txt';

        $result = $this->parseJSON($sourceJSON);
        vdd($result);
//        file_put_contents($resultPath, $result);
    }

    /**
     * @param $json (string) json file path
     * @return array
     * error PHP Compile Error 'yii\base\ErrorException' with message 'Declaration of Rodenastyle\StreamParser\Services\JsonCollectionParser::parse($filePath, $itemCallback, $assoc = true) must be compatible with JsonCollectionParser\Parser::parse($input, $itemCallback, bool $assoc = true): void'
     */
    public function parseJSON($json){
        $this->result = array();
        StreamParser::json($json)->each(function(Collection $book){
            var_dump($book->get('comments')->count());
        });
        return $this->result;
    }

    //end json example



    //csv example

    public function exampleCSV()
    {
        $this->parseFromCSV();
    }

    public function parseFromCSV()
    {
        $sourceTEXT = __DIR__ . '/sample/source.csv';
        $resultPath = __DIR__ . '/results/resultSourceCSV_RodenastyleStreamParser.txt';

        $result = $this->parseCSV($sourceTEXT);
        vdd($result);
//        file_put_contents($resultPath, $result);
    }

    /**
     * @param $csv (string) csv file path
     * @return array
     */
    public function parseCSV($csv){
        $this->result = array();
        StreamParser::csv($csv)->each(function(Collection $book){
            array_push($this->result, $book);
        });
        return $this->result;
    }

    //end csv example


    #endregion

}
