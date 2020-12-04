<?php
/**
 * Author:  Xolmat Ravshanov
 */
namespace zetsoft\service\parser;

use Symfony\Component\DomCrawler\Crawler;
use zetsoft\system\kernels\ZFrame;

class DomCrawler extends ZFrame
{

    #region Vars
    public $crawler;
    #endregion
    public function test()
    {
      $this->testCrawler();
    }

    public function init()
    {
        parent::init();


    }

    public function testCrawler()
    {
        $this->crawler('<!DOCTYPE html>
<html>
    <body>
        <p class="message">Hello World!</p>
        <p>Hello Crawler!</p>
    </body>
</html>');
    }




    public function crawler($html)
    {

        $this->crawler = new Crawler($html);
        $sss = new Crawler($html);

        return $this->crawler;

    }

    public function crawlerFilterPath($html, $htmlPath){
        $this->crawler=new  Crawler($html);
        $result = $this->crawler->filterXPath($htmlPath);
        return $result;
    }

    public function crawlerFilter($html, $htmlTag){
        $this->crawler= new Crawler($html);
        $result = $this->crawler->filter($htmlTag);
        return $result;
    }

    public function  crawlerRegisterNamespace($html, $type, $url){
        $this->crawler= new Crawler($html);
        $result = $this->crawler->registerNamespace($type, $url);
        return $result;

    }

    public function crawlerMatches($html, $tagString){
        $this->crawler = new Crawler($html);
        $result = $this->crawler->matches($tagString);
        return $result;
    }


}
