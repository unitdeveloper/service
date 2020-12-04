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
use zetsoft\system\assets\ZColor;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZWidget;
use zetsoft\widgets\former\ZGrapesJsWidgetRavshan;
use zetsoft\widgets\inputes\ZFileInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;


class Grapes_ extends ZFrame
{

    #region GRAPES WIDGET METHODS


    public function getCategoryTitle()
    {

        //$files = $this->findCategoryALL();
        $files = [];

        $params = [];

        foreach ($files as $key => $file) {

            $obj = new ZCategories();
            $item = new $file();

            $obj->title = $item->title;
            $obj->icon = $item->icon;
            $obj->desc = $item->desc;

            $params[$key] = $obj;
        }

        return $params;

    }

    public function findAllFoldersWidgets()
    {

        $pathes = ZFileHelper::scanFolder(Root . '/widgets');

        $folders = [];

        foreach ($pathes as $path) {
            $folders[$path] = bname($path);
        }

        return $folders;
    }

    public function findWidgets($folder, $grapes = false)
    {

        $files = ZFileHelper::scanFilesPHP(Root . '/widgets/' . $folder);

        return Az::$app->utility->mains->getWidget($files, $grapes);
    }

    public function grapesCategories($folders)
    {
        $excludeFolders = [
            'values',
            'chates',
            'animo',
            'inptest'
        ];

        if (empty($folders))
            $folders = [];

        $data = [];
        foreach ($folders as $folder) {

            if (ZArrayHelper::isIn($folder, $excludeFolders))
                continue;

            $category = $folder;
            $categories = ZWidget::categories;

            if (ZArrayHelper::keyExists($folder, $categories)) {
                $opts = $categories[$folder];
                $category = ZArrayHelper::getValue($opts, 'title');
            }

            foreach ($this->findWidgets($folder, true) as $widget) {
                $basename = bname($widget);
                $widget = str_replace('\\', '/', $widget);
                $item = new GrapeItem();
                $item->blockId = $widget;
                $item->blockName = str_replace(['Z', 'ZK', 'ZH', 'Widget'], '', $basename);
                $item->category = $category;
                $item->droppable = true;
                $item->resizable = false;
                $item->draggable = true;
                $item->ajaxName = $widget;
                $item->type = 'default';
                $item->icon = Az::$app->utility->mains->icon(false);
                $item->components = null;
                $item->title = $basename;
                $data[] = $item;
            }

        }

        return $data;
    }

    public function getTemplates()
    {
        $data = [];
        $folders = PageBlocksType::find()->all();
        /** @var PageBlocks $block */

        foreach ($folders as $folder) {

            $blocks = PageBlocks::find()->where([
                'page_blocks_type_id' => $folder->id
            ])->all();

            foreach ($blocks as $block) {

                $path = 'zetsoft/blocks/' . $block->name;

                $item = new GrapeItem();
                $item->blockId = bname($block->title);
                $item->blockName = $path;
                $item->category = $folder->name;
                $item->isAll = true;
                $item->resizable = false;
                $item->ajaxName = $path;
                $item->title = bname($block->title);
                $item->droppable = true;
                $item->draggable = true;
                $item->type = 'default';
                $item->icon = Az::$app->utility->mains->icon(false);
                $item->components = null;

                $data[] = $item;

            }

        }

        return $data;
    }

    public function getColumns()
    {
        $data = [];

        foreach ($this->findWidgets('columns') as $path) {

            $widget = str_replace('\\', '/', $path);
            $item = new GrapeItem();
            $item->blockId = bname($widget);
            $item->blockName = $widget;
            $item->category = 'columns';
            $item->title = bname($widget);
            $item->content = $path::widget();
            $item->droppable = true;
            $item->resizable = false;
            $item->draggable = true;
            $item->layerable = true;
            $item->highlightable = 1;
            $item->icon = Az::$app->utility->mains->icon(false);

            $data[] = $item;
        }

        return $data;
    }

    public function grapesWidgets($widgets = [])
    {
        $data = [];
        foreach ($widgets as $widget) {

            $folder = $this->getFolderByWidget($widget);

            $category = $folder;
            $categories = ZWidget::categories;

            if (ZArrayHelper::keyExists($folder, $categories)) {
                $opts = $categories[$folder];
                $category = ZArrayHelper::getValue($opts, 'title');
            }

            $basename = bname($widget);
            $widget = str_replace('\\', '/', $widget);
            $item = new GrapeItem();
            $item->blockId = bname($widget);
            $item->blockName = str_replace(['Z', 'ZK', 'ZH', 'Widget'], '', $basename);
            $item->category = $category;
            $item->ajaxName = $widget;
            $item->icon = Az::$app->utility->mains->icon(false);
            $item->title = bname($widget);

            $data[] = $item;

        }

        return $data;
    }

    public function getRenderPath()
    {

        $get = $this->httpGet('path');

        $basename = bname($get);
        $currentFileName = str_replace('.php', '', $basename);
        $folderName = bname(str_replace($basename, '', $get));
        $renderPath = str_replace($currentFileName, 'content', $get);

        if ($folderName === 'ALL') {
            $renderPath = str_replace($currentFileName, 'content_' . $currentFileName, $get);
        }

        return $renderPath;
    }

    public function getHrefToPage($renderPath, $PageAction)
    {

        switch (true) {

            case  $renderPath:
                $href = '/core/tester/asror/main.aspx?path=' . $renderPath;
                break;

            case  $PageAction:
                $href = '/' . $PageAction->data . '.aspx';
                break;

            default:
                $href = '/core/widget/class.aspx';

        }

        return $href;

    }

    #endregion

    #region GRAPES METHODS REPLACE

    public function getToolbarItemsReplace($_config)
    {

        if (!empty($_config)) {
            $tbButtons = $_config;
        } else {
            $tbButtons = [];

            $tbButton = new GrapeToolbarItem();
            $tbButton->id = 'widget-settings-button';
            $tbButton->class = 'fa fa-cog';
            $tbButton->title = Az::l('Редактировать');
            $tbButton->name = '';
            $tbButton->draggable = false;
            $tbButton->command = <<<JS
        function (e) {
            const buttonManager = editor.Panels;
            const openSm=buttonManager.getButton('views','open-tm');
            openSm.set('active',1)
        } 
JS;
            $tbButtons[] = $tbButton;

            $tbButton = new GrapeToolbarItem();
            $tbButton->id = '';
            $tbButton->class = 'fa fa-paint-brush';
            $tbButton->title = Az::l('Редактировать');
            $tbButton->name = '';
            $tbButton->draggable = false;
            $tbButton->command = <<<JS
         function (event) {
            const buttonManager = editor.Panels;
            const openTm = buttonManager.getButton('views','open-sm');
            openTm.set('active',1) ;
         } 
JS;
            $tbButtons[] = $tbButton;

            $tbButton = new GrapeToolbarItem();
            $tbButton->id = 'tbdrag';
            $tbButton->class = 'fa fa-arrows';
            $tbButton->title = Az::l('Выдвинуть');
            $tbButton->name = '';
            $tbButton->draggable = true;
            $tbButton->command = "'tlb-move'";
            $tbButtons[] = $tbButton;

            $tbButton = new GrapeToolbarItem();
            $tbButton->id = 'arrowUp';
            $tbButton->class = 'fa fa-arrow-up';
            $tbButton->title = Az::l('Выдвинуть на верх');
            $tbButton->name = '';
            $tbButton->draggable = false;
            $tbButton->command = <<<JS
        function command(ed) {
            return ed.runCommand('core:component-exit', {
                force: 1
            });
        }
JS;
            $tbButtons[] = $tbButton;

            $tbButton = new GrapeToolbarItem();
            $tbButton->id = 'tbclone';
            $tbButton->class = 'fa fa-clone';
            $tbButton->title = Az::l('Клонировать');
            $tbButton->name = '';
            $tbButton->draggable = false;
            $tbButton->command = "'tlb-clone'";
            $tbButtons[] = $tbButton;

            $tbButton = new GrapeToolbarItem();
            $tbButton->id = 'tbdelete';
            $tbButton->class = 'fa fa-trash-o';
            $tbButton->title = Az::l('Удалить');
            $tbButton->name = '';
            $tbButton->draggable = false;
            $tbButton->command = "'tlb-delete'";
            $tbButtons[] = $tbButton;
        }

        return $tbButtons;

    }

    public function getTooltipReplace($tooltips, $_layout)
    {

        $tooltip = '';
        foreach ($tooltips as $key => $value) {

            $tooltip .= strtr($_layout, [
                '{tooltipId}' => $key,
                '{src}' => $value->src,
                '{icon}' => $value->icon,
                '{title}' => $value->title,
                '{content}' => $value->content,
            ]);

        }

        return $tooltip;

    }

    public function getToolbarButtonsReplace($_layout, $_config)
    {

        $tbButtons = $this->getToolbarItemsReplace($_config);
        $toolbarButtons = '';
        foreach ($tbButtons as $tbItem) {
            $toolbarButtons .= strtr($_layout, [
                '{id}' => $tbItem->id,
                '{class}' => $tbItem->class,
                '{title}' => $tbItem->title,
                '{name}' => $tbItem->name,
                '{draggable}' => $tbItem->draggable,
                '{command}' => $tbItem->command,
            ]);
        }

        return $toolbarButtons;

    }

    public function getColumnsReplace($_layout)
    {

        $getColumns = $this->getColumns();

        $columns = '';
        foreach ($getColumns as $grapItem) {
            $columns .= strtr($_layout, [
                '{blockId}' => $grapItem->blockId,
                '{blockName}' => $grapItem->blockName,
                '{content}' => $grapItem->content,
                '{category}' => $grapItem->category,
                '{title}' => $grapItem->title,
                '{icon}' => $grapItem->icon,
            ]);
        }

        return $columns;

    }

    public function getTemplatesReplace($_layout_templates)
    {


        $getTemplates = $this->getTemplates();

        $templates = '';
        foreach ($getTemplates as $grapItem) {
            $templates .= strtr($_layout_templates, [
                '{blockId}' => $grapItem->blockId,
                '{img}' => $grapItem->img,
                '{blockName}' => $grapItem->blockName,
                '{content}' => $grapItem->content,
                '{category}' => $grapItem->category,
                '{script}' => $grapItem->script,
                '{style}' => $grapItem->style,
                '{title}' => $grapItem->title,
                '{src}' => $grapItem->src,
                '{type}' => $grapItem->type,
                '{removable}' => $grapItem->removable,
                '{draggable}' => $grapItem->draggable,
                '{droppable}' => $grapItem->droppable,
                '{badgable}' => $grapItem->badgable,
                '{stylable}' => $grapItem->stylable,
                '{highlightable}' => $grapItem->highlightable,
                '{copyable}' => $grapItem->copyable,
                '{resizable}' => 0,
                '{editable}' => $grapItem->editable,
                '{layerable}' => $grapItem->layerable,
                '{selectable}' => $grapItem->selectable,
                '{ajaxName}' => $grapItem->ajaxName,
                '{hoverable}' => $grapItem->hoverable,
                '{void}' => $grapItem->void,
                '{icon}' => $grapItem->icon,
                '{components}' => $grapItem->components,
                '{toolbar}' => $grapItem->toolbar,
                '{class}' => $grapItem->class,
                '{propagate}' => $this->jscode([$grapItem->propagate]),
                '{unstylable}' => $this->jscode([$grapItem->unstylable]),
            ]);
        }

        return $templates;

    }

    public function getButtonsReplace($btns, $_layout_button)
    {

        $buttons = '';
        /** @var
         * GrapeBtnItem $btnItem
         */
        foreach ($btns as $btnItem) {

            $buttons .= strtr($_layout_button, [
                '{panelId}' => $btnItem->panelId,
                '{id}' => $btnItem->id,
                '{className}' => $btnItem->className,
                '{tagName}' => $btnItem->tagName,
                '{icon}' => $btnItem->icon,
                '{title}' => $btnItem->title,
                '{attributes}' => json_encode($btnItem->attributes),
                '{href}' => $btnItem->href,
                '{target}' => $btnItem->target,
                '{label}' => $btnItem->title,
                '{command}' => $btnItem->command,
                '{active}' => $btnItem->active,
                '{disable}' => $btnItem->disable,
                '{dragDrop}' => $btnItem->dragDrop
            ]);
        }

        return $buttons;

    }

    public function getTopButtonsReplace($hrefToPage, $_config_buttons)
    {

        $topButtons = [];

        $btn = new GrapeBtnItem();
        $btn->panelId = 'options';
        $btn->id = 'fullscreen';
        $btn->attributes = [
            'title' => 'FullScreen'
        ];
        $btn->tagName = 'span';
        $btn->icon = 'btn btn-outline-primary fas fa-arrows-alt';
        $btn->label = '';
        $btn->command = 'fullscreen';
        $topButtons['fullscreen'] = $btn;

        $btn = new GrapeBtnItem();
        $btn->panelId = 'options';
        $btn->id = 'canvas-clear';
        $btn->attributes = [
            'title' => 'Clear'
        ];
        $btn->tagName = 'span';
        $btn->icon = 'fas fa-trash btn btn-outline-danger';
        $btn->label = '';
        $btn->command = 'clear';
        $topButtons['clear'] = $btn;

        $btn = new GrapeBtnItem();
        $btn->panelId = 'options';
        $btn->id = 'sw-visibility';
        $btn->attributes = [
            'title' => 'View'
        ];
        $btn->tagName = 'span';
        $btn->icon = 'fas fa-square-o btn btn-outline-primary';
        $btn->label = '';
        $btn->command = 'sw-visibility';
        $topButtons['view'] = $btn;

        $btn = new GrapeBtnItem();
        $btn->panelId = 'options';
        $btn->id = 'gotoPage';
        $btn->tagName = 'a';
        $btn->icon = 'fas fa-location-arrow btn btn-outline-primary gotoPage py-3';
        $btn->label = '';
        $btn->attributes = [
            'href' => $hrefToPage,
            'target' => '_blank',
            'title' => 'Go to Page'
        ];
        $btn->command = 'function(){console.log("gotoPage")}';
        $topButtons['gotoPage'] = $btn;

        if (!empty($_config_buttons)) {
            $topButtons = $_config_buttons;
        }

        return $topButtons;
    }

    public function getBlocks($tooltips, $toolbarButtons, $_config_widgets, $_config_categories, $_layout_blocks)
    {

        $data = $this->grapesCategories($_config_categories);
        if (!empty($_config_widgets)) {
            $data = $this->grapesWidgets($_config_widgets);
        }

        $blocks = '';
        foreach ($data as $grapItem) {

            $icon = $tooltips[$grapItem->title]->icon;
            if (empty($icon))
                $icon = Az::$app->utility->mains->icon(true);

            $blocks .= strtr($_layout_blocks, [
                '{blockId}' => $this->jscode($grapItem->blockId),
                '{img}' => $this->jscode($grapItem->img),
                '{blockName}' => $this->jscode($grapItem->blockName),
                '{content}' => $this->jscode($grapItem->content),
                '{category}' => $this->jscode($grapItem->category),
                '{script}' => $this->jscode($grapItem->script),
                '{style}' => $this->jscode($grapItem->style),
                '{label}' => $this->jscode($grapItem->blockName),
                '{title}' => $this->jscode($grapItem->title),
                '{src}' => $this->jscode($grapItem->src),
                '{type}' => $this->jscode($grapItem->type),
                '{removable}' => $this->jscode($grapItem->removable),
                '{draggable}' => $this->jscode($grapItem->draggable),
                '{droppable}' => $this->jscode($grapItem->droppable),
                '{isAll}' => $this->jscode($grapItem->isAll),
                '{badgable}' => $this->jscode($grapItem->badgable),
                '{stylable}' => $this->jscode($grapItem->stylable),
                '{highlightable}' => $this->jscode($grapItem->highlightable),
                '{copyable}' => $this->jscode($grapItem->copyable),
                '{resizable}' => 0,
                '{editable}' => $this->jscode($grapItem->editable),
                '{layerable}' => $this->jscode($grapItem->layerable),
                '{selectable}' => $this->jscode($grapItem->selectable),
                '{ajaxName}' => $this->jscode($grapItem->ajaxName),
                '{hoverable}' => $this->jscode($grapItem->hoverable),
                '{void}' => $this->jscode($grapItem->void),
                '{icon}' => $this->jscode($icon),
                '{components}' => $this->jscode($grapItem->components),
                '{class}' => $this->jscode($grapItem->class),
                '{propagate}' => $this->jscode([$grapItem->propagate]),
                '{unstylable}' => $this->jscode([$grapItem->unstylable]),
                '{tbItem}' => $this->jscode($toolbarButtons),
            ]);
        }

        return $blocks;

    }


    public function getBlocksReplace($tooltips, $config, $toolbar = null)
    {

        /** @var ZGrapesJsWidgetRavshan $config */
        $data = $this->grapesCategories($config->_config['categories']);
        if (!empty($config->_config['widgets'])) {
            $data = $this->grapesWidgets($config->_config['widgets']);
        }

        $blocks = '';
        foreach ($data as $grapItem) {

            $icon = $tooltips[$grapItem->title]->icon;
            if (empty($icon))
                $icon = Az::$app->utility->mains->icon(true);

            /** @var GrapeItem $grapItem */
            $blocks .= strtr($config->_layout['blocks'], [
                '{name}' => $this->jscode(bname($grapItem->blockName)),
                '{blockId}' => $this->jscode($grapItem->blockId),
                '{img}' => $this->jscode($grapItem->img),
                '{widgetName}' => $this->jscode($grapItem->ajaxName),
                '{content}' => $this->jscode($grapItem->content),
                '{category}' => $this->jscode($grapItem->category),
                '{script}' => $this->jscode($grapItem->script),
                '{style}' => $this->jscode($grapItem->style),
                '{label}' => $this->jscode($grapItem->blockName),
                '{title}' => $this->jscode($grapItem->title),
                '{src}' => $this->jscode($grapItem->src),
                '{type}' => $this->jscode($grapItem->type),
                '{removable}' => $this->jscode($grapItem->removable),
                '{draggable}' => $this->jscode($grapItem->draggable),
                '{droppable}' => $this->jscode($grapItem->droppable),
                '{isAll}' => $this->jscode($grapItem->isAll),
                '{badgable}' => $this->jscode($grapItem->badgable),
                '{stylable}' => $this->jscode($grapItem->stylable),
                '{highlightable}' => $this->jscode($grapItem->highlightable),
                '{copyable}' => $this->jscode($grapItem->copyable),
                '{resizable}' => 0,
                '{editable}' => $this->jscode($grapItem->editable),
                '{layerable}' => $this->jscode($grapItem->layerable),
                '{selectable}' => $this->jscode($grapItem->selectable),
                '{hoverable}' => $this->jscode($grapItem->hoverable),
                '{void}' => $this->jscode($grapItem->void),
                '{icon}' => $this->jscode($icon),
                '{components}' => $this->jscode($grapItem->components),
                '{class}' => $this->jscode($grapItem->class),
                '{propagate}' => $this->jscode([$grapItem->propagate]),
                '{unstylable}' => $this->jscode([$grapItem->unstylable]),
                '{toolbar}' => $toolbar,
            ]);

        }

        return $blocks;

    }

    #endregion GRAPES METHODS REPLACE

    #region GRAPES THEME

    public function grapes_theme($_config_theme, $mainCss)
    {

        $css = '';

        switch ($_config_theme) {

            case 'blueTheme':
            {
                $this->css = strtr($mainCss, [
                    '{primaryColor}' => ZColor::color['oxford-blue'],
                    '{secondaryColor}' => ZColor::color['royal-blue'],
                    '{thirdColor}' => ZColor::color['payne-grey'],
                    '{activeBtnColor}' => ZColor::color['glitter'],
                    '{btnItemColor}' => ZColor::color['glitter'],
                    '{blockColor}' => ZColor::color['royal-blue'],
                ]);
                break;
            }

            case 'bronzeTheme':
            {
                $css = strtr($mainCss, [
                    '{primaryColor}' => ZColor::color['paper'],
                    '{secondaryColor}' => ZColor::color['silk'],
                    '{thirdColor}' => ZColor::color['charcoal'],
                    '{activeBtnColor}' => ZColor::color['pale-gold'],
                    '{btnItemColor}' => ZColor::color['charcoal'],
                    '{blockColor}' => ZColor::color['silk'],
                ]);
                break;
            }

            case 'whiteBlackTheme':
            {
                $css = strtr($mainCss, [
                    '{primaryColor}' => ZColor::color['white'],
                    '{secondaryColor}' => ZColor::color['white'],
                    '{thirdColor}' => ZColor::color['white'],
                    '{activeBtnColor}' => ZColor::color['black'],
                    '{btnItemColor}' => ZColor::color['black'],
                    '{blockColor}' => ZColor::color['white'],
                ]);
                break;
            }

            case 'blackWhiteTheme':
            {
                $css = strtr($mainCss, [
                    '{primaryColor}' => ZColor::color['black'],
                    '{secondaryColor}' => ZColor::color['black'],
                    '{thirdColor}' => ZColor::color['black'],
                    '{activeBtnColor}' => ZColor::color['white'],
                    '{btnItemColor}' => ZColor::color['white'],
                    '{blockColor}' => ZColor::color['black'],
                ]);
                break;
            }

            case 'lightBlueTheme':
            {
                $css = strtr($mainCss, [
                    '{primaryColor}' => ZColor::color['white'],
                    '{secondaryColor}' => ZColor::color['light-grey'],
                    '{thirdColor}' => ZColor::color['medium-blue'],
                    '{activeBtnColor}' => ZColor::color['lighten-blue'],
                    '{btnItemColor}' => ZColor::color['royal-blue'],
                    '{blockColor}' => ZColor::color['light-grey'],
                ]);
                break;
            }

        }

        return $css;

    }

    #endregion GRAPES THEME


    #region WIDGET GRAPES


    #endregion


    #region GRAPES VIEW FILE METHODS

    public function getFolderByWidget($widget)
    {
        $name = bname($widget);
        $path = str_replace($name, '', $widget);

        return bname($path);

    }

    public function findAllFilesFromWidgets()
    {

        $files = ZFileHelper::findFiles(Root . '/widgets', [

            'filter' => function ($path) {
                $excludes = [
                    '.idea', '@', '__', 'Dups',
                    '.txt', 'AUWs', 'inptest',
                    'market',
                ];

                foreach ($excludes as $value)
                    if (ZStringHelper::find($path, $value))
                        return false;

                return true;

            }

        ]);

        return $files;

    }

    public function findAllWidgets()
    {

        $files = $this->findAllFilesFromWidgets();

        return Az::$app->utility->mains->getWidget($files, true);

    }

    public function fixSrc($widget)
    {

        $path = str_replace('zetsoft', '', Root);
        $path = $path . $widget . '/image/main.png';
        $path = str_replace(Az::getAlias('@zetsoft'), '', $path);
        $path = str_replace(['widgets', '\\'], ['render', '/'], $path);

        return $path;

    }


    public function tooltip()
    {

        $items = [];
        $widgets = $this->findAllWidgets();

        foreach ($widgets as $widget) {

            $grapes = $widget::$grapes;

            $item = new GrapeTooltipItem();
            foreach ($grapes as $key => $value)
                $item->$key = $value;

            //$item->src = $this->fixSrc($widget);
            $item->src = null;
            if (empty($grapes['icon']))
                $item->icon = Az::$app->utility->mains->icon(true);

            $items[bname($widget)] = $item;

        }

        return $items;

    }

    public function getPathGrapes($path)
    {

        $renderPath = '';
        if (!empty($path)) {

            $get = str_replace('.php', '', $path); // render/inputes/ZKSelect2Widget/grapes.php

            $basename = bname($get); // grapes

            $folderName = bname(str_replace($basename, '', $get)); // ZKSelect2Widget

            $renderPath = str_replace($basename, 'content', $get) . '.php'; // render/inputes/ZKSelect2Widget/content.php

            if ($folderName === 'ALL')
                $renderPath = str_replace($basename, 'content_' . $basename, $get) . '.php';

        }


        return $renderPath;
    }

    public function getPath($get)
    {
        $getPath = str_replace(bname($get), 'content', $get);
        $path = Root . '\\' . $getPath . '.php';
        $path = str_replace('\\', '/', $path);

        return $path;
    }

    public function getViewPath($actionId)
    {

        $PageAction = PageAction::findOne($actionId);

        $name = 'core/kernel/widget/class';

        if ($PageAction)
            $name = $PageAction->name;

        $path = Root . '/webhtm/' . $name . '.php';
        $path = str_replace('\\', '/', $path);

        return $path;

    }

    public function getPathAssets($assets, $script = 'styles')
    {

        $pregs = Az::$app->utility->pregs;

        $scripts = ZArrayHelper::getValue($pregs->pregMatchAll($assets, '<script src="(.*?)".*?>'), 1);
        $styles = ZArrayHelper::getValue($pregs->pregMatchAll($assets, '<link href="(.*?)".*?>'), 1);

        if ($script === 'scripts')
            return $scripts;


        return $styles;

    }

    #endregion

}

