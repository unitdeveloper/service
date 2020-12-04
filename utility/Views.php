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

use kartik\grid\EditableColumn;
use yii\base\View;
use yii\base\ViewNotFoundException;
use yii\base\Widget;
use yii\debug\Module;
use yii\web\JsExpression;
use zetsoft\dbitem\core\WebItem;
use zetsoft\system\Az;
use zetsoft\system\control\ZControlWeb;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZAction;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;
use zetsoft\system\kernels\ZWidget;
use zetsoft\system\module\Models;
use zetsoft\system\module\ZDebug;
use zetsoft\widgets\images\ZHImgWidget;
use zetsoft\widgets\images\ZImageWidget;

class Views extends ZFrame
{

    #region Vars

    public const linkAll = 'Link: {script} {style} {image} {font}';

    public const linkJs = '<{url}>; rel=preload; crossorigin="anonymous"; as=script, ';
    public const linkCss = '<{url}>; rel=preload; crossorigin="anonymous"; as=style, ';
    public const linkFont = '<{url}>; rel=preload; crossorigin="anonymous"; as=font, ';
    public const linkImage = '<{url}>; rel=preload; crossorigin="anonymous"; as=image, ';

    public $linkJs;
    public $linkCss;
    public $linkFont;
    public $linkImage;

    #endregion


    #region Core


    public function title()
    {

        /** @var WebItem $action */
        $action = $this->paramGet(paramAction);

        if ($action === null)
            return null;
/*
        if (!$action instanceof WebItem)
            return null;*/

        $title = $action->title;

        $title = Az::l($title);

        /** @var Models $model */

        Az::$app->params['title'] = $title;
        // Az::$app->view->title = $title;

        Az::$app->params['breadcrumb'][] = [
            'label' => $title,
            'url' => [$this->urlMain]
        ];

    }

    public function toolbarMode($mode)
    {

        global $boot;

        if (!$mode)
            Az::$app->view->off(\yii\web\View::EVENT_END_PAGE, [Module::getInstance(), 'renderToolbar']);
        else
            if ($boot->enableDebug()) {
                Az::$app->view->on(\yii\web\View::EVENT_END_PAGE, [Module::getInstance(), 'renderToolbar']);

            }
    }


    #endregion


    #region Asset

    public function assets($file, $params = [])
    {

        ob_start();
        ob_implicit_flush(false);

        $zoft = $this->thisGet();

        switch (true) {
            case $zoft instanceof ZView:
                $view = $zoft;
                break;

            case $zoft instanceof ZWidget:
            case $zoft instanceof ZControlWeb:
                $view = $zoft->getView();
                break;

            case $zoft instanceof ZAction:
                $view = $zoft->controller->view;
                break;

            default:
                return null;
        }

        $view->beginPage();
        $view->head();
        $view->beginBody();
        $view->toolbar();
        //$view->renderFile($file, $params);
        $view->endBody();
        $view->endPage(true);

        return ob_get_clean();
    }

    #endregion


    #region Render


    public function renderPart($file, $params = [])
    {
        $zoft = $this->thisGet();

        $file = Root . $file;

        return Az::$app->view->renderFile($file, $params);
      //  return $this->require($file, $params);

    }

    public function renderPartGrape($file, $params = [])
    {

        $data = file_get_contents($file);

        //   $this->toolbarMode(false);
        $file = $this->require($file, $params, null, false);
        $service = Az::$app->utility->pregs;

        $scripts = ZArrayHelper::getValue($service->pregMatchAll($file, '<script.*?src="(.*?)".*?><\/script>'), 1);
        $links = ZArrayHelper::getValue($service->pregMatchAll($file, '<link.*?href="(.*?)".*?>'), 1);

        $file = $service->pregReplace($file, '<script.*?src="(.*?)".*?><\/script>');
        $file = $service->pregReplace($file, '<link.*?href="(.*?)".*?>');
        $file = $service->pregReplace($file, '<style><\/style>|<link.*>|<meta.*>|<body.*?>|<\/body>|<head>|<\/head>|<html.*?>|<\/html>|<!DOCTYPE.*?>|<title>.*?<\/title>');

        return [
            'data' => $data,
            'file' => $file,
            'scripts' => $scripts,
            'links' => $links
        ];

    }


    public function renderAjax($file, $params = [])
    {

        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();

        echo $this->require($file, $params);

        Az::$app->view->endBody();
        Az::$app->view->endPage(true);

        $code = ob_get_clean();

        return $code;
    }


    public function renderAjaxG($file, $params = [])
    {

        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();

        //echo $this->require($file, $params);
        echo Az::$app->view->renderFile($file, $params);

        Az::$app->view->endBody();
        Az::$app->view->endPage(true);

        $code = ob_get_clean();

        return $code;
    }



    #endregion


    #region Grapes


    public function renderAjaxFile($file, $params = [])
    {

        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();
        echo Az::$app->view->renderFile($file, $params);
        Az::$app->view->endBody();
        Az::$app->view->endPage(true);

        return ob_get_clean();

    }


    public function renderAjaxFilePreg($file, $params = [])
    {
        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();
        echo $this->require($file, $params);
        Az::$app->view->endBody();
        Az::$app->view->endPage(true);

        $all = ob_get_clean();

        $service = Az::$app->utility->pregs;
        $all = $service->pregReplace($all, '<script src="(.*?)".*?><\/script>');
        $all = $service->pregReplace($all, '<link href="(.*?)".*?>');
        $all = $service->pregReplace($all, '<style><\/style>');

        return $all;

    }


    public function renderHtmPart($htm)
    {
        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();

        echo $htm;

        Az::$app->view->endBody();
        Az::$app->view->endPage(true);

        $all = ob_get_clean();
        $service = Az::$app->utility->pregs;
        $all = $service->pregReplace($all, '<script src="(.*?)".*?><\/script>');
        $all = $service->pregReplace($all, '<link href="(.*?)".*?>');
        $all = $service->pregReplace($all, '<style><\/style>');


        return $all;
    }

    public function renderHtmAjax($htm)
    {

        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();

        echo $htm;

        Az::$app->view->endBody();
        Az::$app->view->endPage(true);
        return ob_get_clean();

    }


    public function renderPartFile($file, $params = [])
    {

        ob_start();
        ob_implicit_flush(false);

        Az::$app->view->beginPage();
        Az::$app->view->head();
        Az::$app->view->beginBody();

        echo Az::$app->view->renderFile($file, $params);

        Az::$app->view->endBody();
        Az::$app->view->endPage(false);

        return ob_get_clean();


    }

    #endregion

    #region Widget

    public const position = [
        'begin' => ZView::POS_BEGIN,
        'head' => ZView::POS_HEAD,
        'end' => ZView::POS_END,
    ];


    public function headCss($url)
    {
        Az::$app->params['addCss'][$url] = $url;
    }

    public function headJs($url)
    {
        Az::$app->params['addJs'][$url] = $url;
    }

    public function headImg($url, $execute)
    {
        Az::$app->params['addImg'][$url] = $url;

        if ($execute)
            echo ZImageWidget::widget([
                'config' => [
                    'url' => $url,
                    'style' => 'display:none',
                ]
            ]);
    }

    public function headFont($url)
    {
        Az::$app->params['addFont'][$url] = $url;
    }



    #endregion


    #region Head


    public function addJs(string $url, int $position, $depends, $assetz = false)
    {

        global $boot;

        if ($assetz)
            $url = $this->processUrl($url);


        /**
         *
         * Param Position
         */

        $options = [
            'depends' => $depends,
            'position' => $position
        ];

        $zoft = $this->thisGet();

        if ($boot->isCLI())
            Az::debug($url, 'Registering JS');
        else
            Az::$app->view->registerJsFile($url, $options);

    }


    public function addCss(string $url, array $depends, $assetz = false)
    {
        global $boot;

        if ($assetz)
            $url = $this->processUrl($url);

        $zoft = $this->thisGet();

        $options = [
            'depends' => $depends,
        ];

        if ($boot->isCLI())
            Az::debug($url, 'Registering CSS');
        else
            Az::$app->view->registerCssFile($url, $options);


    }


    private function processUrl($url)
    {

        if (!$this->paramGet('widget'))
            return $url;

        $urlData = parse_url($url);
        $host = ZArrayHelper::getValue($urlData, 'host');

        if (!Az::$app->utility->assetz->isAcceptableUrl($host))
            return $url;

        $data = Az::$app->utility->assetz->scanUrlPath($urlData['path']);

        $destination = '/assetz';
        $file = $destination . $data->dir . '/' . $data->file;

        $absolutePath = Az::getAlias(Assetz::dir['cache']);
        $fileLocation = $absolutePath . $data->dir . '/' . $data->file;
        if (file_exists($fileLocation))
            return $file;
        else
            return $url;
    }


    public function linkAll()
    {

        /**
         *
         * AddJS
         */
        if (ZArrayHelper::keyExistsNorm('addJs', Az::$app->params))
            foreach (Az::$app->params['addJs'] as $url) {
                $this->linkJs .= strtr(self::linkJs, [
                    '{url}' => $url
                ]);
            }

        Az::$app->params['addJs'] = null;


        /**
         *
         * AddCss
         */
        if (ZArrayHelper::keyExistsNorm('addCss', Az::$app->params))
            foreach (Az::$app->params['addCss'] as $url) {
                $this->linkCss .= strtr(self::linkCss, [
                    '{url}' => $url
                ]);
            }
        Az::$app->params['addCss'] = null;


        if (ZArrayHelper::keyExistsNorm('addImg', Az::$app->params))
            foreach (Az::$app->params['addImg'] as $url) {
                $this->linkImage .= strtr(self::linkImage, [
                    '{url}' => $url
                ]);
            }
        Az::$app->params['addImg'] = null;


        if (ZArrayHelper::keyExistsNorm('addFont', Az::$app->params))
            foreach (Az::$app->params['addFont'] as $url) {
                $this->linkFont .= strtr(self::linkFont, [
                    '{url}' => $url
                ]);
            }
        Az::$app->params['addFont'] = null;


        $header = strtr(self::linkAll, [
            '{script}' => $this->linkJs,
            '{style}' => $this->linkCss,
            '{image}' => $this->linkImage,
            '{font}' => $this->linkFont,
        ]);


    }

    #endregion

}
