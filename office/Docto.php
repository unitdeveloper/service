<?php
/**
 * Class    Docto
 * @package zetsoft\service\office
 *
 * https://github.com/tobya/DocTo
 * @author UzakbaevAxmet
 * @author DilshodKhudoyarov
 * Class file formatlarni boshqa formatga convert qiladi
 */

namespace zetsoft\service\office;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function zetsoft\apisys\edit\returnn;

// This service uses Docto program to convert files into any types
// Give a path of your file($file_path) to convert it into another type

class Docto extends ZFrame
{

    #region Vars


    public $deleteAfterConvert = false;

    public $openAfterConvert = true;

    /**
     * @var bool
     * -WD Use Word for Conversion (Default)
     * --word
     */
    public $useWordForConvert = false;


    /**
     * @var string
     *
     * -O  Output File or Directory to place converted Docs
     * --outputFile
     */
    public $outputFile = '';


    /**
     * @var string
     *  -F  Input File or Directory
     * --inputfile
     */
    public $inputFile = '';


    /**
     * @var string
     *
     *   -T  Format(Type) to convert file to, either integer or wdSaveFormat constant.
     * Available from
     * https://docs.microsoft.com/en-us/dotnet/api/microsoft.office.interop.word.wdsaveformat
     * or https://docs.microsoft.com/en-us/dotnet/api/microsoft.office.interop.excel.xlfileformat
     * See current List Below.
     */
    public $format = self::format['wdFormatPDF'];

    public $oldPath;


    #endregion

    #region Const

    public const cmdline = [
        'useWordForConvert' => '--word',
        'outputFile' => '--outputFile',
        'inputFile' => '--inputfile',
        'format' => '--format',
        'excel' => '--excel'
    ];


    public const cmdlineShort = [
        'useWordForConvert' => '-WD',
        'outputFile' => '-O',
        'inputFile' => '-F',
        'format' => '-T',
        'excel' => '-XL',
    ];


    /**
     *
     * https://docs.microsoft.com/en-us/dotnet/api/microsoft.office.interop.word.wdsaveformat?view=word-pia
     */

    public const format = [
        'wdFormatDocument97' => 'wdFormatDocument97',
        'wdFormatDocument' => 'wdFormatDocument',
        'wdFormatDocumentDefault' => 'wdFormatDocumentDefault',
        'wdFormatDOSText' => 'wdFormatDOSText',
        'wdFormatDOSTextLineBreaks' => 'wdFormatDOSTextLineBreaks',
        'wdFormatEncodedText' => 'wdFormatEncodedText',
        'wdFormatFilteredHTML' => 'wdFormatFilteredHTML',
        'wdFormatFlatXML' => 'wdFormatFlatXML',
        'wdFormatFlatXMLMacroEnabled' => 'wdFormatFlatXMLMacroEnabled',
        'wdFormatXML' => 'wdFormatXML',
        'wdFormatPDF' => 'wdFormatPDF',
        'wdFormatHTML' => 'wdFormatHTML',
        'wdFormatRTF' => 'wdFormatRTF',
        'wdFormatText' => 'wdFormatText',
        'wdFormatXMLDocument' => 'wdFormatXMLDocument',
        'wdFormatStrictOpenXMLDocument' => 'wdFormatStrictOpenXMLDocument',
        'wdFormatXPS' => 'wdFormatXPS',
        'wdFormatWebArchive' => 'wdFormatWebArchive',
        'wdFormatUnicodeText' => 'wdFormatUnicodeText',
        'wdFormatTextLineBreaks' => 'wdFormatTextLineBreaks',
        'wdFormatXMLTemplate' => 'wdFormatXMLTemplate',
        'wdFormatXMLTemplateMacroEnabled' => 'wdFormatXMLTemplateMacroEnabled',
        'xlOpenXMLWorkbook' => 'xlOpenXMLWorkbook',
    ];

    #endregion


    #region Core


    public function cmdline()
    {
        $cmd = 'docto';

        if ($this->useWordForConvert)
            $cmd .= ' ' . self::cmdline['useWordForConvert'];

        if (!empty($this->inputFile))
            $cmd .= ' ' . self::cmdline['inputFile'] . ' ' . $this->inputFile;

        if (!empty($this->outputFile))
            $cmd .= ' ' . self::cmdline['outputFile'] . ' ' . $this->outputFile;

        if (!empty($this->format))
            $cmd .= ' ' . self::cmdline['format'] . ' ' . $this->format;

        return $cmd;
    }

    public function before()
    {
        $this->oldPath = getcwd();
        chdir(Root . '/scripts/convert/');
    }

    public function after()
    {
        chdir($this->oldPath);

        if ($this->openAfterConvert)
            shell_exec($this->outputFile);
    }


    #endregion


    #region Test

    public function test_case()
    {

    }

    #endregion

    #region example

    public function  exampleFile()
    {
        
    }
    #endregion


    #region converters
    public function converter()
    {
        $this->before();

        $cmd = $this->cmdline();
        $output = shell_exec($cmd);

        $this->after();

        return $output;
    }
    #endregion

    #region DocPdfTest

    public function docPdfTest()
    {

        $merged_file = 'D:\Develop\Projects\ALL\asrorz\zetsoft\upload\excelz\eyuf\mergedfile-JupiternDocs-9300.docx';
        
       // $file = Az::$app->office->docto->docPdf($merged_file);
       // $file = Az::$app->office->openOffice->docPdf($merged_file);
        $file = Az::$app->office->officetopdf->docPdf($merged_file);
  // $file = Az::$app->office->pandoc->docPdf($merged_file);
        vdd($file);
    }

    public function docPdf1($file_path)
    {

        $fileName = bname($file_path);
        
        $fileName = strtr($fileName, [
             '.docx' => '.pdf',
         ]);

        Az::$app->office->docto->inputFile = $file_path;
        
        Az::$app->office->docto->outputFile = Root . "/upload/uploaz/eyuf/$fileName";

//        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';

        Az::$app->office->docto->useWordForConvert = false;

        $result = Az::$app->office->docto->converter();
        //tegilmasin
        return ($result);
    }



    public function docPdf($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '/upload/uploaz/eyuf';
        
//        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';

        Az::$app->office->docto->useWordForConvert = false;

        $result = Az::$app->office->docto->converter();
        //tegilmasin
        return ($result);
    }
    #endregion

    #region docRtfTest
    public function docRtf($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatRTF'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }

    #region docTxtTest
    public function docTxt($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatText'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region docHtmlTest
    public function docHtml($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatHTML'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region docXmlTest
    public function docXml($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatXMLDocument'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region docXpsTest
    public function docXps($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatXPS'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region docOddTest
    public function docOddTest($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatStrictOpenXMLDocument'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region pdfTxtTest
    // this function converts pdf file to txt format
    public function pdfTxt($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatText'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }

    #region PdfDocxTest
    public function pdfDocx($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;
        Az::$app->office->docto->format = self::format['wdFormatDocumentDefault'];

        $result = Az::$app->office->docto->converter();
        vd($result);
    }



}
