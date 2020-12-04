<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\spatie;


use Spatie\Url\Url;
use zetsoft\system\kernels\ZFrame;

class TsetUrl extends ZFrame
{
   #region Vars
   public $url;
   #endregion

    #region Cores
       public function init()
       {
           parent::init();
       }

       public function test(){

       /*test getSchem*/
       $this->url = 'https://www.php.net/manual/en/langref.php';
       $getHost = $this->getHost($this->url);
       vd($getHost);

       }
    #endregion

    #region Url
    public function getScheme($url_main){
      $url = Url::fromString($url_main);
      return $url->getScheme();
    }
    public function getHost($url_main){
        $url = Url::fromString($url_main);
        return $url->getHost();
    }
    #endregion
}
