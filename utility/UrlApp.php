<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use yii\helpers\Url;
use zetsoft\models\page\PageAction;
use zetsoft\system\Az;
use zetsoft\system\control\ZControlRest;
use zetsoft\system\control\ZControlWeb;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZView;
use zetsoft\system\kernels\ZWidget;
use zetsoft\system\module\Models;
use zetsoft\system\kernels\ZAction;
use zetsoft\system\kernels\ZFrame;

class UrlApp extends ZFrame
{


    #region Core


    public function forwards($urlData, $merge, $code)
    {

        $url = $this->urlTo($urlData, $merge);

        switch (true) {

            case $this->paramGet(paramIframe):


                echo <<<HTML
<script> window.top.location.href = "{$url}";</script>
HTML;

                return true;
                break;

            case $this->paramGet('redirect'):
                echo <<<HTML
<script> window.opener.location.href="{$url}";
            self.close();</script>
HTML;

                return true;
                break;
            case $this->paramGet('blank'):
                $url = $url . '?pop=yes';
                $this->redirect($url, $code) ;
                return true;
                break;

            default:
                return $this->redirect($url, $code);
        }
    }


    public function redirect($url, $statusCode = 302)
    {
        return Az::$app->response->redirect($url, $statusCode);
    }

    public function refreshes()
    {
        $zoft = $this->thisGet();

        switch (true) {

            case $zoft instanceof ZControlWeb:
            case $zoft instanceof ZControlRest:
                return $zoft->refresh();
                break;


            default:
                return $this->refresh();

        }
    }

    public function refresh($anchor = '')
    {
        return $this->redirect(Az::$app->request->url . $anchor);
    }



    #endregion


    #region App


    public function merge($urlData, $merge = true)
    {

        if (is_array($urlData)) {

            $index = ZArrayHelper::getValue($urlData, 0);
          
            if (!$this->checkStart($index)) {
               //
               // $index = Az::$app->request->url;
                //    $index = $this->urlMain;

                 $index = "{$this->urlModuleStr}/{$index}";
                //   $index = $this->urlArrayStr;
                
                ZArrayHelper::setValue($urlData, '0', $index);
            }

            if ($merge)
                return ZArrayHelper::merge($urlData, $this->urlParam);
            else
                return $urlData;

        }

        if ($this->checkStart($urlData))
            return $urlData;


        if ($merge)
            return $this->urlModuleStr . '/' . $urlData . $this->urlParamStr;
        else
            return $this->urlModuleStr . '/' . $urlData;

    }


    public function checkStart(?string $urlData)    {

        $b1 = ZStringHelper::startsWith($urlData, '/');
        $b2 = ZStringHelper::startsWith($urlData, 'http');

        if ($b1 || $b2)
            return true;

        return false;

    }

    public function to($urlData, $merge = true)
    {
        $url = $this->merge($urlData, $merge);
      // vd($url);

        $url = ZUrl::to($url);

        return $url;
    }

    public function check($urlTo)
    {
        $url = $this->to($urlTo);

        $exists = PageAction::find()
            ->where([
                'link' => $url
            ])
            ->exists();

        if (!$exists) {
            $this->notifyError('Необходимый action не был найден.', $url);
            return false;
        }

        return true;

    }

    #endregion

    #region GET

    public function getBack()
    {
        $url = ZArrayHelper::getValue($_SERVER, 'HTTP_REFERER', Az::$app->request->referrer);
        return $url;
    }

    public function getScheme()
    {
        if (!array_key_exists('REQUEST_SCHEME', $_SERVER))
            return null;

        $sScheme = $_SERVER['REQUEST_SCHEME'];

        return "{$sScheme}://";
    }

    public function getBase()
    {
        if ($this->isCLI())
            return "http.example.uz";
        return $this->getScheme() . $_SERVER['HTTP_HOST'];
    }

    public function getMain()
    {
        return $this->getScheme() . $this->getCore();
    }


    public function getLogin()
    {
        return $this->getBase() . '/';
    }


    public function getCore()
    {
        if (!array_key_exists('HTTP_HOST', $_SERVER))
            return null;

        $url = $_SERVER['HTTP_HOST'];
        $urls = explode('.', $url);

        $urls = array_reverse($urls);

        return "{$urls[1]}.{$urls[0]}";
    }

    #endregion


    #region GoTo

    public function goBacks()
    {
        $url = $this->sessionGet('back');
        $this->sessionDel('back');
        return $this->urlRedirect($url);
    }


    public function goIndex()
    {
        $zoft = $this->thisGet();

        $module = null;
        if ($zoft instanceof ZAction)
            $module = "{$this->moduleId}/";

        $url = $this->getBase() . "/{$module}{$this->controlId}/index.aspx";

        return $this->urlRedirect($url);
    }


    #endregion


    #region IS

    public function isActive($urls)
    {
        if (!empty($urls)) {

            $url = Url::to($urls);
            $appUrl = Az::$app->request->url;

            if ($appUrl === $url)
                return true;
        }

        return false;
    }


    public function isLogin()
    {

        global $boot;

        $current = $_SERVER['REQUEST_URI'];
        $url = $boot->env('urlLogin');
        $one = $current === "$url.aspx";
        $two = $current === "$url-frame.aspx";

        return $one || $two;
    }


    public function isRegister()
    {
        global $boot;
        $current = $_SERVER['REQUEST_URI'];
        $url = $boot->env('urlRegister');
        $one = $current === "$url.aspx";
        $two = $current === "$url-frame.aspx";

        return $one || $two;
    }

    public function isMain()
    {
        $current = Az::$app->request->url;
        return $current === '/' || $this->moduleId === '';
    }

    #endregion
}
