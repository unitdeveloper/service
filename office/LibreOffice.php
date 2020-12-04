<?php
/**
 * Class    LibreOffice
 * @package zetsoft\service\office
 *
 * @author DilshodKhudoyarov
 *
 * https://ru.libreoffice.org/
 * Class file formatlarni boshqa formatga convert qiladi
 */

namespace zetsoft\service\office;


require Root . '/vendori/fileapp/office/vendor/autoload.php';

use zetsoft\service\process\SymfonyProcess;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function Safe\system;

// This service uses LibreOffice program to convert files into any types
// Give a path of your file($file_path) to convert it into another type
class LibreOffice extends ZFrame
{

    #region Vars

    public $deleteAfterConvert = false;

    public $openAfterConvert = false;

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
    public $format = self::format['pdf'];

    public $oldPath;


    #endregion

    #region Const

    public const cmdline = [
        'useWordForConvert' => '--headless --convert-to',
        'outdir' => '--outdir',
        'outputFile' => '--outputFile',
        'inputFile' => '--inputfile',
    ];

    public const format = [
        'pdf' => 'pdf ',
        'txt' => 'txt',
        'rtf' => 'rtf',
        'jpg' => 'jpg',
        'png' => 'png',
        'docx' => 'docx',
        'xml' => 'xml',
        'html' => 'html'
    ];

    #endregion


    #region Core


    public function cmdline()
    {
        $cmd = 'soffice';

        if ($this->useWordForConvert)
            $cmd .= ' ' . self::cmdline['useWordForConvert'];

        if (!empty($this->format))
            $cmd .= ' ' . $this->format;

        if (!empty($this->inputFile))
            $cmd .=  ' ' . $this->inputFile;

        if (!empty($this->outputFile))
            $cmd .= ' ' . self::cmdline['outdir'] . ' ' . $this->outputFile;

        return $cmd;
    }

    public function before()
    {
        $this->oldPath = getcwd();
        chdir('D:\Develop\Projects\ALL\execut\doc\LibreOffice\6_45\App\libreoffice\program');
    }

    public function after()
    {
        chdir($this->oldPath);

        if ($this->openAfterConvert)
            shell_exec($this->outputFile);
    }
    #endregion

    #region converters

    public function converter()
    {
        $this->before();

        $cmd = $this->cmdline();
        $output = shell_exec($cmd);

    //    $output = system($cmd);

     /*   if (file_exists($this->outputFile))
        exec($this->outputFile);*/
      //  $output = exec($cmd);



        $this->after();
        return $output;
    }

    #endregion

    #region DocPdfTest
    public function docPdf($file_path, $directory)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = $directory;
        Az::$app->office->libreOffice->useWordForConvert = true;

        $result = Az::$app->office->libreOffice->converter();
        return $result;
    }
    #endregion

    #region docTxtTest
    public function docTxt($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['txt'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion

    #region docHtml
    public function docHtml($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['html'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion

    #region docXml
    public function docXml($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['xml'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion

    #region xmlDoc
    public function xmlDoc($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['docx'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion

    #region txtDoc
    public function txtDoc($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['docx'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion

    #region docJpg
    public function docJpg($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['jpg'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion

    #region docJpg
    public function docPng($file_path)
    {
        Az::$app->office->libreOffice->inputFile = $file_path;
        Az::$app->office->libreOffice->outputFile = Root . '\upload\uploaz\eyuf';
        Az::$app->office->libreOffice->useWordForConvert = true;
        Az::$app->office->libreOffice->format= self::format['png'];

        $result = Az::$app->office->libreOffice->converter();
        vd($result);
    }
    #endregion
}
