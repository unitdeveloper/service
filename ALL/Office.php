<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\ALL;

use zetsoft\service\office\Docto;
use zetsoft\service\office\Excel;
use zetsoft\service\office\Import;
use zetsoft\service\office\Json;
use zetsoft\service\office\JupiternDocs;
use zetsoft\service\office\JuroshPdfMerge;
use zetsoft\service\office\LibreOffice;
use zetsoft\service\office\MergeFiles;
use zetsoft\service\office\Mpdf;
use zetsoft\service\office\OfficeConvert;
use zetsoft\service\office\Officetopdf;
use zetsoft\service\office\OpenOffice;
use zetsoft\service\office\Pandoc;
use zetsoft\service\office\PhpExcelWriter;
use zetsoft\service\office\PhpWord;
use zetsoft\service\office\PhpWordOb;
use zetsoft\service\office\RguedesPdfMerger;
use zetsoft\service\office\SimpleExcelPhp;
use zetsoft\service\office\Tbszip;
use zetsoft\service\office\Tcpdf;
use zetsoft\service\office\Wordpdf;
use zetsoft\service\office\WordpdfNorm;
use zetsoft\service\office\ZipArchive;
use yii\base\Component;



/**
 *
* @property Docto $docto
* @property Excel $excel
* @property Import $import
* @property Json $json
* @property JupiternDocs $jupiternDocs
* @property JuroshPdfMerge $juroshPdfMerge
* @property LibreOffice $libreOffice
* @property MergeFiles $mergeFiles
* @property Mpdf $mpdf
* @property OfficeConvert $officeConvert
* @property Officetopdf $officetopdf
* @property OpenOffice $openOffice
* @property Pandoc $pandoc
* @property PhpExcelWriter $phpExcelWriter
* @property PhpWord $phpWord
* @property PhpWordOb $phpWordOb
* @property RguedesPdfMerger $rguedesPdfMerger
* @property SimpleExcelPhp $simpleExcelPhp
* @property Tbszip $tbszip
* @property Tcpdf $tcpdf
* @property Wordpdf $wordpdf
* @property WordpdfNorm $wordpdfNorm
* @property ZipArchive $zipArchive

 */

class Office extends Component
{

    
    private $_docto;
    private $_excel;
    private $_import;
    private $_json;
    private $_jupiternDocs;
    private $_juroshPdfMerge;
    private $_libreOffice;
    private $_mergeFiles;
    private $_mpdf;
    private $_officeConvert;
    private $_officetopdf;
    private $_openOffice;
    private $_pandoc;
    private $_phpExcelWriter;
    private $_phpWord;
    private $_phpWordOb;
    private $_rguedesPdfMerger;
    private $_simpleExcelPhp;
    private $_tbszip;
    private $_tcpdf;
    private $_wordpdf;
    private $_wordpdfNorm;
    private $_zipArchive;

    
    public function getDocto()
    {
        if ($this->_docto === null)
            $this->_docto = new Docto();

        return $this->_docto;
    }
    

    public function getExcel()
    {
        if ($this->_excel === null)
            $this->_excel = new Excel();

        return $this->_excel;
    }
    

    public function getImport()
    {
        if ($this->_import === null)
            $this->_import = new Import();

        return $this->_import;
    }
    

    public function getJson()
    {
        if ($this->_json === null)
            $this->_json = new Json();

        return $this->_json;
    }
    

    public function getJupiternDocs()
    {
        if ($this->_jupiternDocs === null)
            $this->_jupiternDocs = new JupiternDocs();

        return $this->_jupiternDocs;
    }
    

    public function getJuroshPdfMerge()
    {
        if ($this->_juroshPdfMerge === null)
            $this->_juroshPdfMerge = new JuroshPdfMerge();

        return $this->_juroshPdfMerge;
    }
    

    public function getLibreOffice()
    {
        if ($this->_libreOffice === null)
            $this->_libreOffice = new LibreOffice();

        return $this->_libreOffice;
    }
    

    public function getMergeFiles()
    {
        if ($this->_mergeFiles === null)
            $this->_mergeFiles = new MergeFiles();

        return $this->_mergeFiles;
    }
    

    public function getMpdf()
    {
        if ($this->_mpdf === null)
            $this->_mpdf = new Mpdf();

        return $this->_mpdf;
    }
    

    public function getOfficeConvert()
    {
        if ($this->_officeConvert === null)
            $this->_officeConvert = new OfficeConvert();

        return $this->_officeConvert;
    }
    

    public function getOfficetopdf()
    {
        if ($this->_officetopdf === null)
            $this->_officetopdf = new Officetopdf();

        return $this->_officetopdf;
    }
    

    public function getOpenOffice()
    {
        if ($this->_openOffice === null)
            $this->_openOffice = new OpenOffice();

        return $this->_openOffice;
    }
    

    public function getPandoc()
    {
        if ($this->_pandoc === null)
            $this->_pandoc = new Pandoc();

        return $this->_pandoc;
    }
    

    public function getPhpExcelWriter()
    {
        if ($this->_phpExcelWriter === null)
            $this->_phpExcelWriter = new PhpExcelWriter();

        return $this->_phpExcelWriter;
    }
    

    public function getPhpWord()
    {
        if ($this->_phpWord === null)
            $this->_phpWord = new PhpWord();

        return $this->_phpWord;
    }
    

    public function getPhpWordOb()
    {
        if ($this->_phpWordOb === null)
            $this->_phpWordOb = new PhpWordOb();

        return $this->_phpWordOb;
    }
    

    public function getRguedesPdfMerger()
    {
        if ($this->_rguedesPdfMerger === null)
            $this->_rguedesPdfMerger = new RguedesPdfMerger();

        return $this->_rguedesPdfMerger;
    }
    

    public function getSimpleExcelPhp()
    {
        if ($this->_simpleExcelPhp === null)
            $this->_simpleExcelPhp = new SimpleExcelPhp();

        return $this->_simpleExcelPhp;
    }
    

    public function getTbszip()
    {
        if ($this->_tbszip === null)
            $this->_tbszip = new Tbszip();

        return $this->_tbszip;
    }
    

    public function getTcpdf()
    {
        if ($this->_tcpdf === null)
            $this->_tcpdf = new Tcpdf();

        return $this->_tcpdf;
    }
    

    public function getWordpdf()
    {
        if ($this->_wordpdf === null)
            $this->_wordpdf = new Wordpdf();

        return $this->_wordpdf;
    }
    

    public function getWordpdfNorm()
    {
        if ($this->_wordpdfNorm === null)
            $this->_wordpdfNorm = new WordpdfNorm();

        return $this->_wordpdfNorm;
    }
    

    public function getZipArchive()
    {
        if ($this->_zipArchive === null)
            $this->_zipArchive = new ZipArchive();

        return $this->_zipArchive;
    }
    


}
