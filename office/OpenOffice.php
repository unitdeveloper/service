<?php
/**
 * Class    OpenOffice
 * @package zetsoft\service\office
 *
 * @author UzakbaevAxmet & Umid Mo'monov
 *
 */
namespace zetsoft\service\office;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\Az;
// This service uses OpenOffice program to convert files into any types

class OpenOffice extends ZFrame
{

    #region Vars

    public $deleteAfterConvert = false;

    public $openAfterConvert = true;

    /**
     * @var bool
     *
     *
     */
    public $useWordForConvert = false;


    /**
     * @var string
     *
     *
     * --outputFile
     */
    public $outputFile = '';


    /**
     * @var string
     *
     * --inputfile
     */
    public $inputFile = '';


    /**
     * @var string
     *
     *
     * Available from
     *
     *
     * See current List Below.
     */

    public $oldPath;


    #endregion

    #region Const

    public const cmdline = [
        'useWordForConvert' => 'DocumentConverter.py',
    ];

    /**
     *D:\open\program\soffice.exe
     *
     */
    #endregion
    #region run
    // in first to work in this converter you have to include this run method
    public function run(){
        chdir('C:\Program Files (x86)\OpenOffice 4\program');
          $port = exec('soffice -headless -nologo -norestore -accept=socket,host=localhost,port=2002;urp;StarOffice.ServiceManager', $outptuing, $status);
          $old_path = getcwd();
    }
    #endregion


    #region Core
    public function cmdline()
    {
        $cmd = 'python';

        if ($this->useWordForConvert)
            $cmd .= ' ' . self::cmdline['useWordForConvert'];

        if (!empty($this->inputFile))
            $cmd .= ' ' . $this->inputFile;

        if (!empty($this->outputFile))
            $cmd .= ' ' . $this->outputFile;

        return $cmd;
    }

    public function before()
    {
        $this->oldPath = getcwd();
        chdir('C:\Program Files (x86)\OpenOffice 4\program');
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
        $this->docPdfTest();
        $this->docRtfTest();
        $this->docTxtTest();
        $this->pdfTxtTest();
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

       $this->inputFile = 'D:\Develop\Projects\ALL\asrorz\zetsoft\upload\excelz\eyuf\contractSh1445.2020-09-09-20-50-49.docx';


        $this->outputFile = 'D:\Develop\Projects\ALL\asrorz\zetsoft\upload\uploaz\eyuf\new_pdf.pdf';


        $this->useWordForConvert = true;


        //$result = Az::$app->office->docto->converter();

        $result = $this->converter();


        vd($result);
    }
/*
    public function docPdf($file_path)
    {
        Az::$app->office->docto->inputFile = $file_path;
        Az::$app->office->docto->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->docto->useWordForConvert = true;

        $result = Az::$app->office->openOffice->converter();
        vd($result);
    }*/
    #endregion

    #region DocPdfTest
    public function docPdf($file_path)
    {
        Az::$app->office->openOffice->inputFile = $file_path;
        Az::$app->office->openOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->openOffice->useWordForConvert = true;

        $result = Az::$app->office->openOffice->converter();
        
        return $result;
    }
    #endregion

    #region docRtfTest
    public function docRtfTest()
    {
        Az::$app->office->docto->inputFile = 'D:/test.docx';
        Az::$app->office->docto->outputFile = 'D:\reg.rtf';
        Az::$app->office->docto->useWordForConvert = true;

        $result = Az::$app->office->docto->converter();
        vd($result);
    }

    #region docTxtTest
    public function docTxtTest()
    {
        Az::$app->office->docto->inputFile = 'D:/test.docx';
        Az::$app->office->docto->outputFile = 'D:\reg.txt';
        Az::$app->office->docto->useWordForConvert = true;

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region docHtmlTest
    public function docHtmlTest()
    {
        Az::$app->office->docto->inputFile = 'D:/test.docx';
        Az::$app->office->docto->outputFile = 'D:/converted_file.html';
        Az::$app->office->docto->useWordForConvert = true;

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region docOdtTest
    public function docOdtTest()
    {
        Az::$app->office->docto->inputFile = 'D:/test.docx';
        Az::$app->office->docto->outputFile = 'D:/docodt.odt';
        Az::$app->office->docto->useWordForConvert = true;

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion

    #region OdtDocTest
    public function odtDocTest()
    {
        Az::$app->office->docto->inputFile = 'D:/test.docx';
        Az::$app->office->docto->outputFile = 'D:/converted_file.xps';
        Az::$app->office->docto->useWordForConvert = true;

        $result = Az::$app->office->docto->converter();
        vd($result);
    }
    #endregion
    }
