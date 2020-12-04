<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 29.06.2017 19:06
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\blogic\shop;


use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use zetsoft\system\Az;
use zetsoft\system\control\ZControlCmd;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\places\ZamchartMap;


class Gits extends ZFrame
{
    /**
     *
     * Function  actionRun
     * @throws \Exception
     */


    public $rename = true;

    private $sample;


    private $folder;
    private $file;

    private $linkMain;
    private $linkDemo;
    private $linkGithub;

    private $author;
    private $library;


    public function run()
    {
        $this->sample = file_get_contents(Root . '/binary/urlap/sample.url');

        $this->git();
        // $this->test();

    }

    public function dir()
    {
        $dir = Root . '/process/Accordeon/';

        $this->folder($dir);
    }


    public function test()
    {
        $file = Root . '/process/Menu/Microsoft Products Inspired Tab Navigation Based On Bootstrap 4 _ Free jQuery Plugins.mhtml';

        $this->file($file);
    }


  //  public $root = 't:/PHP/Projects/zetsoft/process/';
    public $root = 'd:\Develop\Interface\___\\';

    private function git()
    {

        $folders = ZFileHelper::findDirectories($this->root, [
            'recursive' => false
        ]);

        foreach ($folders as $folder) {
            $folder = ZFileHelper::normalizePath($folder);
            $this->folder($folder);
        }


    }


    private function folder($folder)
    {
        $files = ZFileHelper::findFiles($folder, [
            'recursive' => false
        ]);

        foreach ($files as $file) {
            $this->file($file);
        }

    }

    private function file($file)
    {
        if (!file_exists($file))
            return Az::warning($file, 'File Not exists');


        Az::debug($file, 'Now Processing');


        /**
         *
         * Process Text
         */
        $text = file_get_contents($file);
        $text = str_replace(array('=' . PHP_EOL, '3D'), '', $text);


        /**
         *
         * Get Links
         */

        $this->linkMain = $this->snapshot($text);
        Az::debug($this->linkMain, 'Main Link');

        $github = $this->github($text);

        if (empty($github))
            return true;

        $this->linkGithub = $github['github'];

        if (ZStringHelper::find($this->linkGithub, '"'))
            return true;

        $this->author = $github['author'];
        $this->library = $github['library'];

        Az::debug($github, 'Github');


        if (empty($this->library))
            return false;

        $this->linkDemo = $this->demo($text);
        Az::debug($this->linkDemo, 'Demo Link');


        /**
         *
         * Create Folder
         */
        $this->folder = dirname($file);

        $this->folder .= '/' . $this->author . ' ' . $this->library;

        ZFileHelper::createDirectory($this->folder);
        Az::debug($this->folder, 'Created Folder');

        //  $this->url($this->linkMain);
        $this->url($this->linkGithub);
        $this->url($this->linkDemo);


        $this->url("https://yarnpkg.com/en/packages?q=$this->library");
        $this->url("https://yarnpkg.com/en/package/$this->library");
        $this->url("http://jsdelivr.com/package/npm/$this->library");
        $this->url("https://asset-packagist.org/package/search?query=$this->library&platform=bower%2Cnpm");

        /**
         *
         * Move file
         */

        if (!$this->rename)
            return false;

        $filename = bname($file);
        $newname = "$this->folder/$filename";
        rename($file, $newname);

        return true;

    }

    private function url($link)
    {

        if (empty($link))
            return false;

        $search = [
            'www.',
            'search?q=',
            '/',
            '\\',
            '?',
            ':',
        ];

        $name = str_replace($search, ' ', $link);

        $search = [
            '#',
            'https',
            'http',
            '.html',
            '.htm',
            '.aspx',
            '.asp',
        ];


        $name = str_replace($search, '', $name);
        $name = trim($name);

        $urlFile = "$this->folder/$name.url";

        $text = strtr($this->sample, [
            '{url}' => $link
        ]);

        if (file_put_contents($urlFile, $text))
            Az::debug($urlFile, 'Written');
        else
            Az::warning($urlFile, 'Not Written');

        return false;
    }


    private function snapshot(string $text)
    {

        preg_match_all('/Snapshot-Content-Location: (.*)?/', $text, $return);

        if (empty($return[0]))
            return [];

        $return = ZArrayHelper::getValue($return, '1.0');

        return $return;
    }

    private function github(string $text)
    {

        preg_match_all('/\"(https:\/\/github.com\/(.*?)\/(.*?))\"/', $text, $return);

        if (empty($return[0]))
            return [];

        $github = ZArrayHelper::getValue($return, '1.0');
        $author = ZArrayHelper::getValue($return, '2.0');
        $library = ZArrayHelper::getValue($return, '3.0');

        /*      $author = ZInflector::humanize($author, true);
              $library = ZInflector::humanize($library, true);*/
        $library = str_replace(array('/', '\\'), '', $library);

        return [
            'github' => $github,
            'author' => $author,
            'library' => $library,
        ];
    }


    private function demo(string $text)
    {

        preg_match_all('/\"(https:\/\/www.jqueryscript.net\/asset\/.*?)"/', $text, $return);

        if (empty($return[0]))
            return [];

        $return = ZArrayHelper::getValue($return, '1.0');

        return $return;
    }


}
