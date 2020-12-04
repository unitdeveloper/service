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

namespace zetsoft\service\cores;

use yii\base\ErrorException;
use yii\caching\TagDependency;
use yii\helpers\FileHelper;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageControl;
use zetsoft\models\page\PageModule;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;
use zetsoft\system\module\Models;
use function Dash\Curry\find;

class AppPage extends ZFrame
{

    #region Vars

    public $type;

    public $pathView;
    public $pathApp;

    /**
     * @var PageModule
     */
    public $page_module;

    /**
     * @var PageControl
     */
    public $core_control;

    /**
     * @var  PageAction
     */
    public $page_action;


    #endregion


    #region Main

    public function init()
    {
        parent::init();

        $this->pathView = Root . '/webhtm/';
        $this->pathApp = $this->pathView . strtolower(App);

    }

    #endregion

    #region Module

    public function module(PageModule $page_module, $is_new)
    {
        Az::start(__FUNCTION__);

        $old_name = $page_module->name;
        $page_module->data = $page_module->input;
        $page_module->name = strtolower(App) . '/' . $page_module->data;
        $page_module->check = true;

        $this->page_module = $page_module;
        if (!$is_new) {
            $this->moduleUpdate($old_name);
            return true;
        }

        if (ZArrayHelper::getValue(Az::$app->params, 'is_clone')) {
            $old_name = $this->page_module->name;
            $id = $this->page_module->id;
            $this->page_module->input .= '_' . $id;
            $this->page_module->data .= '_' . $id;
            $this->page_module->name .= '_' . $id;
            $this->moduleCreate($old_name);
        } else {
            $this->moduleCreate();
        }

        Az::end();
    }

    public function moduleUpdate($old_name)
    {
        rename($this->pathView . $old_name, $this->pathView . $this->page_module->name);
    }
        
    public function moduleCreate($old_name = null)
    {

        $pathApp = $this->pathApp;
        $path = $pathApp . '/' . $this->page_module->data;

        if (!file_exists($pathApp)) {
            Az::error($pathApp, 'App path does not exist');
            return false;
        }
        if (ZFileHelper::createDirectory($path)) {
            Az::debug($path, 'Module path created');
            if ($old_name != null) {
                $this->clone(
                    $this->pathView . $old_name,
                    $this->pathView . $this->page_module->name
                );
            } else {
                if ($this->page_module->clone != null) {
                    $from_module = PageModule::findOne($this->page_module->clone);
                    $this->clone(
                        $this->pathView . $from_module->name,
                        $this->pathView . $this->page_module->name
                    );
                }
            }

            return true;
        } else {
            Az::error($path, 'Cannot create path');
            return false;
        }

    }

    public function move_module($old_module_name)
    {
        $trash_dir = "$this->pathApp/.trash/";
        
        if (!is_dir($trash_dir))
            mkdir($trash_dir, 0777, true);

        $trash_module_dir = "$this->pathApp/.trash/" . bname($old_module_name);
        $old_module_dir = $this->pathApp . '/' . bname($old_module_name);
        if (is_dir($old_module_dir))
            rename($old_module_dir, $trash_module_dir);

    }

    #endregion

    #region Control

    public function control(PageControl $core_control, $is_new)
    {
        $old_name = $core_control->name;
        $this->page_module = PageModule::findOne($core_control->page_module_id);

        $core_control->data = $this->page_module->data . '/' . $core_control->input;
        $core_control->name = strtolower(App) . '/' . $core_control->data;
        $core_control->check = true;

        $this->core_control = $core_control;

        if (!$is_new) {
            $this->controlUpdate($old_name);
            return true;
        }
        if (ZArrayHelper::getValue(Az::$app->params, 'is_clone')) {
            $old_name = $this->core_control->name;
            $id = $this->core_control->id;
            $this->core_control->input .= '_' . $id;
            $this->core_control->data .= '_' . $id;
            $this->core_control->name .= '_' . $id;
            $this->controlCreate($old_name);
        } else {
            $this->controlCreate();
        }
    }

    public function controlUpdate($old_name)
    {

        rename($this->pathView . $old_name, $this->pathView . $this->core_control->name);

        //controller file
        $controllerName = ucfirst(strtolower($old_name));
        $pathControlOld = ZFileHelper::normalizePath(Root . '/cnweb/' . $controllerName . 'Controller.php');
        unlink($pathControlOld);

        $this->create_control_file();
        //controller file end
    }

    public function controlCreate($old_name = null)
    {

        $path = $this->pathApp . '/' . $this->core_control->data;

        if (file_exists($this->pathApp)) {
            if (ZFileHelper::createDirectory($path)) {
                Az::debug($path, 'Control path created');

                $this->create_control_file();

                if ($old_name != null) {
                    $this->clone(
                        $this->pathView . $old_name,
                        $this->pathView . $this->core_control->name
                    );
                } else {
                    if ($this->core_control->clone != null) {
                        $from_control = PageControl::findOne($this->core_control->clone);

                        $this->clone(
                            $this->pathView . $from_control->name,
                            $this->pathView . $this->core_control->name
                        );
                    }
                }
                return true;
            } else {
                Az::error($path, 'Cannot create path');
                return false;
            }
        } else {
            Az::error($this->pathApp, 'App path does not exist');
            return false;
        }
    }

    private function create_control_file()
    {

        $template = file_get_contents(Root . '/binary/giiapp/control.php');
        $namespace = ZFileHelper::normLinux($this->page_module->name);
        $controllerName = ucfirst(strtolower($this->core_control->input));

        $pathControl = ZFileHelper::normalizePath(Root . '/cnweb/' . $this->page_module->name . '/' . $controllerName . 'Controller.php');
        $type = ($this->core_control->rest) ? 'Rest' : 'Web';

        $controllerContent = strtr($template, [
            'ZNamespace' => $namespace,
            'ZControlName' => $controllerName,
            'ZAPP' => $type
        ]);

        file_put_contents($pathControl, $controllerContent);
    }

    public function move_control($control, $new_control_name = Null)
    {
        //conrtoller folder ni trashga ko'chirish
        $old_control_name = $control->name;
        $this->page_module = PageModule::findOne($control->page_module_id);
        $module_name = $this->page_module->name;
        $trash_dir = "$this->pathView$module_name/.trash/";
        if (!is_dir($trash_dir))
            mkdir($trash_dir, 0777, true);

        $trash_control_dir = $trash_dir . bname($old_control_name) . '_' . time();
        $old_control_dir = "$this->pathView$module_name" . '/' . bname($old_control_name);
        if (is_dir($old_control_dir))
            rename($old_control_dir, $trash_control_dir);

        //conrtoller file ni trashga ko'chirish
        $controllerName = ucfirst(strtolower($control->input));
        $pathControl = ZFileHelper::normalizePath(Root . '/cnweb/' . $this->page_module->name . '/' . $controllerName . 'Controller.php');

        $trash_dir = Root . '/cnweb/' . $this->page_module->name . "/.trash/";
        if (!is_dir($trash_dir))
            mkdir($trash_dir, 0777, true);
        $trash_file_path = Root . '/cnweb/' . $this->page_module->name . "/.trash/" . $controllerName . 'Controller_' . time() . '.php';
        rename($pathControl, $trash_file_path);
    }

    #endregion

    #region Action
    public function action(PageAction $page_action, $is_new)
    {
        $old_name = $page_action->name;
        $this->core_control = PageControl::findOne($page_action->page_control_id);

        $page_action->data = $this->core_control->data . '/' . $page_action->input;
        //$page_action->link = $page_action->data . 'aspx';
        $page_action->name = strtolower(App) . '/' . $page_action->data;
        $page_action->check = true;

        $this->page_action = $page_action;

        if (!$is_new) {
            $this->actionUpdate($old_name);
            return true;
        }
        if (ZArrayHelper::getValue(Az::$app->params, 'is_clone')) {
            $old_name = $this->page_action->name;
            $id = $this->page_action->id;
            $this->page_action->input .= '_' . $id;
            $this->page_action->data .= '_' . $id;
            $this->page_action->name .= '_' . $id;
            $this->actionCreate($old_name);
        } else {
            $this->actionCreate();
        }
    }

    public function actionUpdate($old_name)
    {
        $pathActionOld = ZFileHelper::normalizePath($this->pathView . $old_name . '.php');

        if (!file_exists($pathActionOld))
            return null;

            
        $old_content = file_get_contents($pathActionOld);
        unlink($pathActionOld);

        $for_headers = $this->getReadyTemplate();

        $this->putHeadersToContent($old_content, $for_headers);
    }

    public function actionCreate($old_name = null)
    {

        $pathApp = $this->pathApp;

        if (!file_exists($pathApp)) {
            Az::error($pathApp, 'App path does not exist');
            return false;
        }

        $for_headers = $this->getReadyTemplate();
        if ($old_name != null) {
            $from_action_file_path = $this->pathView . '/' . $old_name . '.php';
            $clone_content = file_get_contents($from_action_file_path);

            $this->putHeadersToContent($clone_content, $for_headers);
        } else {
            if ($this->page_action->clone != null) {
                $from_action = PageAction::findOne($this->page_action->clone);
                $from_action_file_path = $this->pathView . '/' . $from_action->name . '.php';
                $clone_content = file_get_contents($from_action_file_path);

                $this->putHeadersToContent($clone_content, $for_headers);
            } else {
                $this->putActionContent($for_headers);
            }
        }
        
    }

    public function putHeadersToContent($content, $header)
    {
        $str = 'use zetsoft\system\kernels\ZView;';
        $position_cut = strpos($header, $str);

        $actionContent_part = substr($header, $position_cut + strlen($str));

        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->title.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->icon.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->debug.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->cache.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->cacheHttp.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->csrf.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '\$this->type.*;(\r\n|\r|\n)', '');
        $content = Az::$app->utility->pregs->pregReplace($content, '(\r\n|\r|\n)(\r\n|\r|\n)\/\*\* \@var ZView \$this \*\/(\r\n|\r|\n)(\r\n|\r|\n)', '');

        $position_cut = strpos($content, $str);

        if ($position_cut == false)
        {
            $actionContent_part = "<?\n".$str."\n".$actionContent_part."?>\n";

            $actionContent = substr_replace($content, $actionContent_part, 0, 0);
        }else{
            $actionContent = substr_replace($content, $actionContent_part, $position_cut + strlen($str), 0);
        }
        
        $this->putActionContent($actionContent);
    }

    public function putActionContent($content)
    {
        $actionFile = $this->pathApp . '/' . $this->page_action->data . '.php';
        $file = file_put_contents($actionFile, $content);

        if (is_file($actionFile) && $file !== false) {
            Az::debug($actionFile, 'View file created');
        } else
            Az::error($actionFile, 'Cannot create view file');
    }

    public function getReadyTemplate()
    {
        $template = file_get_contents(Root . '/binary/giiapp/view.php');
        $debug = $this->page_action->debug ? 'true' : 'false';
        $cache = $this->page_action->cache ? 'true' : 'false';
        $cache_http = $this->page_action->cacheHttp ? 'true' : 'false';
        $csrf = $this->page_action->csrf ? 'true' : 'false';
        $actionContent = strtr($template, [
            '{title}' => $this->page_action->title,
            '{icon}' => $this->page_action->icon,
            "'{debug}'" => $debug,
            "'{cache}'" => $cache,
            "'{cacheHttp}'" => $cache_http,
            "'{csrf}'" => $csrf,
            '{type}' => $this->page_action->type,
        ]);

        return $actionContent;
    }

    public function move_action($action)
    {
        $old_action_name = $action->name;
        $this->core_control = PageControl::findOne($action->page_control_id);
        $control_name = $this->core_control->name;
        $trash_dir = "$this->pathView$control_name/.trash/";
        if (!is_dir($trash_dir))
            mkdir($trash_dir, 0777, true);

        $trash_file = $trash_dir . bname($old_action_name) . '_' . time() . '.php';
        $old_file = "$this->pathView$control_name" . '/' . bname($old_action_name) . '.php';

        rename($old_file, $trash_file);

    }

    #endregion

    #region helper
    public function clone($from_foler, $to_folder)
    {
        $dir = opendir($from_foler);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($from_foler . '/' . $file)) {
                    $this->clone($from_foler . '/' . $file, $to_folder . '/' . $file);
                } else {
                    if (!is_dir($to_folder))
                        mkdir($to_folder, 0777, true);
                    copy($from_foler . '/' . $file, $to_folder . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function replace_between($str, $needle_start, $needle_end, $replacement)
    {
        $pos = strpos($str, $needle_start);
        $found_start = $pos;
        $start = $pos === false ? 0 : $pos + strlen($needle_start);

        $pos = strpos($str, $needle_end, $start);
        $found_end = $pos;
        $end = $pos === false ? strlen($str) : $pos;

        if ($found_start and $found_end)
            return substr_replace($str, $replacement, $start, $end - $start);
        else
            return $str;
    }
    #endregion

}
