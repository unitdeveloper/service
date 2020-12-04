<?php
/**
 * Class    CsvService
 * @package zetsoft\service\league
 *
 * @author DilshodKhudoyarov
 *
 * https://csv.thephpleague.com/9.0/
 * Csv bu CSV yozuvlarini yozish, tanlash va konvertatsiya qilish kabi CSV hujjatlarni
 * yuklashni engillashtiradigan oddiy kutubxona.
 */

namespace zetsoft\service\league;
use zetsoft\system\kernels\ZFrame;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\CharsetConverter;
use League\Csv\XMLConverter;

class CsvService extends ZFrame
{

    public function test_case() {
        $this->csv_readerTest();
        $this->encoding_csvTest();
        $this->csv_to_xmlTest();
    }

    public function csv_readerTest() {
        $file_path ='Give a file path!';
        $result=$this->csv_reader($file_path);
        vd($result);
    }
    //Accessing some records from a given CSV documents
    public function csv_reader($file_path) {
        // $file_path = path/to/your/csv/file.csv -> example
        $csv = Reader::createFromPath($file_path, 'r');
        $csv->setHeaderOffset(0); //set the CSV header offset

//get 25 records starting from the 11th row
        $stmt = (new Statement())
            ->offset(10)
            ->limit(25)
        ;

        $records = $stmt->process($csv);
        foreach ($records as $record) {
            //do something here
        }
    }

    public function encoding_csvTest() {
        $file_path ='Give a file path!';
        $result=$this->encoding_csv($file_path);
        vd($result);
    }

    //Encoding a CSV document into a given charset
    public function encoding_csv($file_path) {
        $csv = Reader::createFromPath($file_path, 'r');
        $csv->setHeaderOffset(0);

        $input_bom = $csv->getInputBOM();

        if ($input_bom === Reader::BOM_UTF16_LE || $input_bom === Reader::BOM_UTF16_BE) {
            CharsetConverter::addTo($csv, 'utf-16', 'utf-8');
        }

        foreach ($csv as $record) {
            //all fields from the record are converted into UTF-8 charset
        }
    }

    public function csv_to_xmlTest() {
        $file_path ='Give a file path!';
        $records = 'Give the records';
        $result=$this->csv_to_xml($file_path, $records);
        vd($result);
    }

    //Converting a CSV document into a XML document
    public function csv_to_xml($file_path, $records) {
        $csv = Reader::createFromPath($file_path, 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name')
        ;

        $dom = $converter->convert($records);
        $dom->formatOutput = true;
        $dom->encoding = 'iso-8859-15';

        echo '<pre>', PHP_EOL;
        echo htmlentities($dom->saveXML());
    }

}