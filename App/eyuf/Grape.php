<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;


use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use zetsoft\dbitem\grap\GrapeBtnItem;
use zetsoft\dbitem\grap\GrapeItem;
use zetsoft\dbitem\grap\GrapeToolbarItem;
use zetsoft\dbitem\grap\GrapeTooltipItem;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageBlocks;
use zetsoft\models\page\PageBlocksType;
use zetsoft\models\page\PageWidget;
use zetsoft\models\page\PageWidgetType;
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;
use zetsoft\system\kernels\ZWidget;
use zetsoft\widgets\former\ZGrapesJsWidgetRavshan;
use zetsoft\widgets\inputes\ZFileInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;


class Grape extends ZFrame
{

    public $excludes = [
        'id',
        'created_at',
        'created_by',
        'modified_at',
        'modified_by',
    ];

    #region WRITE TO FILE

    private function usesFix($fileContent)
    {

        $must = [
            'zetsoft\system\kernels\ZView;',
            'zetsoft\system\Az;',
            'zetsoft\dbitem\core\ActionItem;',
            'zetsoft\system\kernels\ZWidget;',
            'zetsoft\widgets\ajaxify\ZIntercoolerWidget;',
            'zetsoft\widgets\blocks\ZNProgressWidget;',
            'zetsoft\widgets\notifier\ZSessionGrowlWidget;'
        ];

        $uses = Az::$app->utility->pregs->pregMatchAll($fileContent, "use.(.*)\r");
        $usesArray = ZArrayHelper::getValue($uses, 1);

        $return = [];
        foreach ($usesArray as $use) {

            if (ZArrayHelper::isIn($use, $must))
                continue;

            $return[] = $use;

        }

        return $return;

    }

    public function writeFile($actions, $content, $css, $path)
    {

        $layouts = [
            'file' => file_get_contents(Az::getAlias('@zetsoft/binary/grape/files.php')),
            'block' => file_get_contents(Az::getAlias('@zetsoft/binary/grape/blocks.php')),
        ];

        $uses = '';
        
        $fileContent = file_get_contents($path);
        $usesArray = $this->usesFix($fileContent);
        
        foreach ($usesArray as $use) {
            $uses .= 'use ' . $use . "\n";
        }

        $replace = 'file';
        if (ZStringHelper::find($path, 'zetsoft\webhtm\block')) {
            $replace = 'block';
        }

        return strtr($layouts[$replace], [
            '// Uses' => $uses,
            '// Settings' => $actions,
            '// Content' => $content,
            '// Styles' => $css,
        ]);

    }

    public function writeBlock($actions, $content, $css, $path)
    {
        $layouts = [
            'block' => file_get_contents(Az::getAlias('@zetsoft/binary/grape/blocks.php')),
        ];

        return strtr($layouts['block'], [
            '// Settings' => $actions,
            '// Content' => $content,
            '// Styles' => $css,
        ]);

    }

    public function pregMatchFix($data)
    {

        $pregService = Az::$app->utility->pregs;

        //STYLES
        $content = $pregService->pregReplace($data, '<link[^>]*>+', '');
        $content = $pregService->pregReplace($content, '<style[^>]*>(?:[^<]+|<\/style>)+', '');

        //SCRIPTS
        $content = $pregService->pregReplace($content, '<script[\s\S]*?>[\s\S]*?<\/script>', '');

        //FIX
        $content = preg_replace('/<!--TEMPLATEBEGIN-->.*<!--TEMPLATEEND-->(.*)/misU', '$1', $content);
        $content = preg_replace('/<!--BEGIN-->.*<!--END-->(.*)/misU', '$1', $content);
        $content = $pregService->pregReplace($content, '<form.*>|<\/form>', '');
        $content = $pregService->pregReplace($content, '<input.*name="_csrf".*>', '');
        $content = str_replace(["\n\n\n", '<!--', '-->', '><'], ['', "\n\n<?php\n", "\n?>\n\n", ">\n<"], $content);
        $content = $pregService->pregReplace($content, ".*?'grapesWrap'\n*.*?\n*=>\n*.*?\n*false,", '');
        $content = $pregService->pregReplace($content, "\?>\W\s*?<\?php", '');

        return $content;
    }

    #endregion

    #region SETTINGS

    public function getOptions($options, $widget)
    {

        $configs = (new $widget())->_config;

        $return = [];
        foreach ($options as $key => $value) {

            if (!ZArrayHelper::keyExists($key, $configs))
                continue;

            if ($value !== ZArrayHelper::getValue($configs, $key))
                $return[$key] = ZVarDumper::ajaxValue($value);

        }

        return $return;

    }

    public function getSettings($widget, $options)
    {

        $defaultConfigs = Az::$app->smart->widget->defaultConfig($widget);

        $array = [
            'grapesWrap' => 'grapesWrap',
            'models' => 'model',
            'attribute' => 'attribute',
            'datas' => 'data',
            'ids' => 'id',
        ];

        $config = [];
        foreach ($options as $key => $value) {

            if (ZVarDumper::ajaxValue($value) === null)
                continue;

            if (ZArrayHelper::keyExists($key, $array))
                $defaultConfigs[$array[$key]] = ZVarDumper::grapesValue($value);
            else
                $config[$key] = ZVarDumper::grapesValue($value);
                
        }


        $config = $this->getOptions($config, $widget);
        $defaultConfigs = Az::$app->smart->widget->getModel($defaultConfigs);
        $defaultConfigs['config'] = $config;

        return $defaultConfigs;

    }

    ##endregion

    #region DATA OF WIDGETS

    public function getServiceFolders()
    {

        $excludes = [
            'ALL',
            '.idea',
            'App'
        ];

        $folders = ZFileHelper::scanFolder(Root . '/service');
        $foldersApp = ZFileHelper::scanFolder(Root . '/service/App');
        $foldersAll = ZArrayHelper::merge($foldersApp, $folders);

        $return = [];
        foreach ($foldersAll as $folder) {
            if (!ZArrayHelper::isIn(bname($folder), $excludes))
                $return[bname($folder)] = bname($folder);
        }

        return $return;

    }

    public function getServices($folder = null)
    {

        if (!$folder)
            return [];

        $folders = ZFileHelper::scanFilesPHP(Root . '/service/' . $folder);

        $return = [];
        foreach ($folders as $param) {
            $param = str_replace([Az::getAlias('@zetsoft'), '/', '.php'], ['zetsoft', '\\', ''], $param);
            $keyParam = strtolower(bname($param));
            $return[$keyParam] = bname($param);
        }

        return $return;

    }


    public function globalMethods()
    {

        $methods = Az::$app->utility->pregs->refMethodList(ZFrame::class);
        
        $return = [];
        foreach ($methods as $method) {
            $return[] = $method->name;
        }

        return $return;
    }


    public function getServicesMethod($folder = null, $service = null)
    {

        if (!$folder || !$service)
            return [];

        $service = 'zetsoft\service\\' . $folder . '\\' . ucfirst($service);

        $methods = Az::$app->utility->pregs->refMethodList($service);

        if (empty($methods))
            return [];

        $globalMethods = $this->globalMethods();
        
        $return = [];
        foreach ($methods as $method) {
            if (!ZArrayHelper::isIn($method->name, $globalMethods))
                $return[$method->name] = $method->name;
        }

        return $return;     

    }

    #endregion

    public function getTemplatesByCategory($name) {

        $page_blocks_type = PageBlocksType::findOne([
            'name' => $name
        ]);

        if (!$page_blocks_type)
            return [];

        $blocks = PageBlocks::find()
            ->where([
                'page_blocks_type_id' => $page_blocks_type->id
            ])
            ->all();

        $return = [];
        foreach ($blocks as $block) {
            /** @var PageBlocks $block */

            $item = new GrapeItem();
            $item->blockId = $block->id;
            $item->name = $block->name;
            $item->title = $block->title;
            $item->icon = $block->icon;
            $item->category = $name;

            $return[] = $item;
        }

        return $return;

    }

    public function getWidgetsByCategory($name) {

        $page_widget_type = PageWidgetType::findOne([
            'name' => $name
        ]);

        if (!$page_widget_type)
            return [];

        $widgets = PageWidget::find()
            ->where([
                'page_widget_type_id' => $page_widget_type->id
            ])
            ->all();

        $return = [];
        foreach ($widgets as $widget) {
            /** @var PageWidget $widget */

            $item = new GrapeItem();
            $item->blockId = $widget->id;
            $item->name = $widget->name;
            $item->title = $widget->title;
            $item->img = $widget->image;
            $item->icon = $widget->icon;
            $item->category = $name;

            $return[] = $item;
        }

        return $return;

    }

}

