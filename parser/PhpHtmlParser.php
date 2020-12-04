<?php
/**
 * Author:  Xolmat Ravshanov
 */
namespace zetsoft\service\parser;
use tekintian\phphtmlparser\HtmlDomParser;

use PHPHtmlParser\Dom;
use zetsoft\system\kernels\ZFrame;



class PhpHtmlParser extends ZFrame
{

   #region Vars
   public $name;
   public $doom;
   public $tags =[ 'a', 'div', 'header', 'nav', 'video', 'img', 'footer'];

   #endregion

    public function example(){
//        $dom = HtmlDomParser::str_get_html( $str )
//        or
//        $dom = HtmlDomParser::file_get_html( $file_name );
//
//        $elems = $dom->find($elem_name);
    }

//   public function test()
//   {
////    $this->testHtmlTag();
//    $this->testLoadString();
//
//   }
//
//   public function init()
//   {
//       parent::init();
//       $this->doom = new Dom;
//
//   }
//
//    public function testHtmlTag()
//    {
//             $this->htmlTags('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>', 'div');
//    }
//
//    public function testSearchPage(){
//       $this->searchPage('https://packagist.org/packages/paquettg/php-html-parser', 'div');
//    }
//
//    public function testsearchUrl(){
//        $this->searchUrl('"www.gihub.com');
//
//    }
//
//    public function testLoadString(){
//       $this->loadString('<div>sdflkjeroisdlfkjsdf<p>sdlkjfeirodjfsdjkl<span>dslkjerijfdlskjfweiordlfjfkfkfk</span></p></div>');
//    }
//   public function htmlTags($html_code, $html_tag)
//   {
//       if(!empty($html_code) && !empty($html_tag))
//       {
//            foreach ($this->tags as $tag){
//                if($tag === $html_tag) {
//                    $this->doom->load($html_code);
//                    $tag = $this->doom->find($html_tag);
//                    return $tag;
//                }
//            }
//
//       }}
//
//    public function searchPage($page, $need){
//       if(!empty($page)) {
//           $this->doom->loadFromFile($page);
//           $contents = $this->doom->find($need);
//            return $contents;
//       }
//
//    }
//
//    public function searchUrl($url){
//       if(!empty($url)){
//           $this->doom->loadFromUrl($url);
//           $html = $this->doom->outerHtml;
//           return $html;
//       }
//    }
//
//    public function loadString($htmltag){
//
//$this->doom->loadStr($htmltag, []);
//$result = $this->doom->outerHtml;
//return $result;
//   }


}

