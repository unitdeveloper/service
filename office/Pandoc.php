<?php
/**
 * Class    Pandoc
 * @package zetsoft\service\office
 *
 * @author UzakbaevAxmet
 * @author DilshodKhudoyarov
 * Class file formatlarni boshqa formatga convert qiladi
 */

namespace zetsoft\service\office;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

// This service uses Pandoc program to convert files into any types
// Give a path of your file($path) to convert it into another type

/**
 * Class    Pandoc
 * @package zetsoft\service\office
 * 
 * https://github.com/jgm/pandoc/
 * https://pandoc.org/demos.html
 */
class Pandoc extends ZFrame
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

    #region Const

    public const cmdline = [
    //    'outputFile' => '-o',
    ];


    /**
     *
     * https://docs.microsoft.com/en-us/dotnet/api/microsoft.office.interop.word.wdsaveformat?view=word-pia
     */

    #endregion


    #region Core


    public function cmdline()
    {
        $cmd = 'pandoc';

        if (!empty($this->inputFile))
            $cmd .= ' ' . $this->inputFile;

        if (!empty($this->outputFile))
            $cmd .= ' --pdf-engine=D:\miktext\texmfs\install\miktex\bin\x64\xelatex -o ' . $this->outputFile;

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
         $this->docxTxt('d:/ZoirTest-Copy.docx');
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
    public function docPdf($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region txtHtml
    public function txtHtml($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region txtTex
    public function txtTex($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region texTxt
    public function texTxt($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region txtDocx
    public function txtDocx($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region docxTxt
    public function docxTxt($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region txtPdf
    public function txtPdf($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion

    #region txtRtf
    public function txtRtf($file_path)
    {
        Az::$app->office->pandoc->inputFile = $file_path;
        Az::$app->office->pandoc->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->pandoc->useWordForConvert = true;

        $result = Az::$app->office->pandoc->converter();

        //tegilmasin

        return($result);
    }
    #endregion
}
