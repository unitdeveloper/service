<?php
/**
 * Class    OfficeToPdf
 * @package zetsoft\service\office
 *
 *
 * @author UzakbaevAxmet
 * @author DilshodKhudoyarov
 *
 * Class formatlarni boshqa convert qiladi
 */

namespace zetsoft\service\office;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Officetopdf extends ZFrame
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

    public $oldPath;


    #endregion

    #region Core

    public function cmdline()
    {
        $cmd = 'officetopdf.exe';

        if ($this->useWordForConvert)
            $cmd .= ' ';

        if (!empty($this->inputFile))
            $cmd .= $this->inputFile;

        if (!empty($this->outputFile))
            $cmd .=' ' . $this->outputFile;
        return $cmd;
    }

    public function before()
    {
        $this->oldPath = getcwd();
        chdir(Root .'/scripts/convert/');
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

    #region DocPdf
    public function docPdf($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;

        $result = Az::$app->office->officetopdf->converter();

        return($result);
    }
    #endregion

    #region docRtf
    public function docRtf($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;

        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }

    #region docxOdd
    public function docxOdd($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;

        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #endregion

    #region docXlsx
    public function docXlsx($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;

        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #endregion

    #region docXml
    public function docXml($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;

        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #endregion

    #region docXps
    public function docXps($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;


        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #endregion

    #region docTxt
    public function docTxt($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;


        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #endregion

    #region docxHtml
    // this function converts pdf file to txt format
    public function docxHtml($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;


        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #region xlsCsv
    public function xlsCsv($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->officetopdf->useWordForConvert = true;


        $result = Az::$app->office->officetopdf->converter();
        vd($result);
    }
    #endregion

    #region docxPpt
    public function docxPpt($file_path)
    {
        Az::$app->office->officetopdf->inputFile = $file_path;
        Az::$app->office->officetopdf->outputFile = 'D:/';
        Az::$app->office->officetopdf->useWordForConvert = false;

        $result = Az::$app->office->officetopdf->converter();

        return($result);
    }
    #endregion


}
