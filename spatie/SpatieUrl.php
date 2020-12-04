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
use function Dash\isEmpty;

class SpatieUrl extends ZFrame
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

        $this->url = 'https://www.php.net/manual/en/langref.php?utm_source=github&utm_campaign=packages';

        /*test getSchem*/
        $getSchem = $this->getScheme($this->url);
        vd($getSchem);

        /*test getHost*/
        $getHost = $this->getHost($this->url);
        vd($getHost);

        /*test getPath*/
        $getPath = $this->getPath($this->url);
        vd($getPath);

        /*test transformScheme*/
        $transformscheme = $this->getSegment($this->url,0);
        vd($transformscheme);

    }
    #endregion


    #region Url
    public function getScheme($url_main)
    {
    
        $url = Url::fromString($url_main);

        return $url->getScheme();
    }

    public function getHost($url_main)
    {
        $url = Url::fromString($url_main);
        return $url->getHost();
    }

    public function getPath($url_main)
    {
        $url = Url::fromString($url_main);
        return $url->getPath();
    }

    public function getQuery($url_main)
    {
        $url = Url::fromString($url_main);
        return $url->getQuery();
    }

    public function getQueryParametr($url_main, $parmetr)
    {
        $url = Url::fromString($url_main);
        return $url->getQueryParameter($parmetr);
    }

    public function withoutQueryParameter($url_main, $parmetr)
    {
        $url = Url::fromString($url_main);
        return $url->withoutQueryParameter($parmetr);
    }

    public function transformQueryParameter($url_main,$parametr, $transform)
    {
        $url = Url::fromString($url_main);
        if($url->getQueryParameter($parametr)){
            return $url->withQueryParameter($parametr, $transform);
        }

    }

    public function transformQuery($url_main, $transform)
    {
        $url = Url::fromString($url_main);
        if($url->getQuery()){
            return $url->withQuery($transform);
        }

    }

    public function getSegment($url_main, $position)
    {
        $url = Url::fromString($url_main);
        if ($position > 1)
        {
            return $url->getSegment($position);
        }

    }


    public function transformScheme($url_main, $transform)
    {
        $url = Url::fromString($url_main);
        if($url->getScheme()) {
            return $url->withScheme($transform);
        }
    }

    public function transformPort($url_main, $transform)
    {
        $url = Url::fromString($url_main);
        if($url->getPort()) {
            return $url->withPort($transform);
        }
    }

    public function transformUser($url_main, $transform)
    {
        $url = Url::fromString($url_main);
        if(!isEmpty($url->getUserInfo())) {
            return $url->withUserInfo($transform);
        }
    }

    public function transformPath($url_main, $transform)
    {
        $url = Url::fromString($url_main);
        if(!isEmpty($url->getPath())) {
            return $url->withPath($transform);
        }
    }








    #endregion


}
