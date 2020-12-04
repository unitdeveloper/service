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

use yii\caching\TagDependency;
use yii\helpers\FileHelper;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageBlocks;
use zetsoft\models\page\PageBlocksType;
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

class AppBlock extends ZFrame
{

    #region Vars

    public $pathBlocks;
    public $old_content;
    public $old_name;
    /**
     * @var PageBlocks
     */
    public $blocks;

    public $blocksType;
    #endregion


    #region Main

    public function init()
    {
        parent::init();

        $this->pathBlocks = Root . '/webhtm/block/';

        //$this->control = file_get_contents(Root . '/binary/giiapp/control.php');
    }

    public function run(PageBlocks $blocks, $is_new)
    {
        $this->blocksType = PageBlocksType::findOne($blocks->page_blocks_type_id);
        $this->blocks = $blocks;
        $this->old_content = '';
        $this->old_name = $this->blocks->name;
        if(!$is_new)
            $this->old_content = file_get_contents($this->pathBlocks . $this->blocks->name . '.php');

        $this->block();
    }

    #endregion


    #region Block

    public function block()
    {
        Az::start(__FUNCTION__);
        $this->blockCreate();
        Az::end();
    }


    public function blockCreate()
    {
        //Gets template of Controller file
        //$template = file_get_contents(Root . '/binary/giiapp/block.php');
        if (file_exists($this->pathBlocks .'/'. $this->blocksType->name . '/azk/' . $this->blocks->sample . '.php'))
            $sample = file_get_contents($this->pathBlocks .'/'. $this->blocksType->name . '/azk/' . $this->blocks->sample . '.php');
        else
            $sample = file_get_contents(Root . '/binary/giiapp/block.php');

        if (!ZArrayHelper::getValue(Az::$app->params, 'is_clone'))
            $this->blocks->name = $this->blocksType->name . '/' . App . '/' . $this->blocks->input;
        
        //path and files which should be created
        $blockFile = ZFileHelper::normalizePath($this->pathBlocks . $this->blocks->name . '.php');

        if ($this->old_content == '') {
            if (ZArrayHelper::getValue(Az::$app->params, 'is_clone'))
            {
                //vdd($this->pathBlocks . $this->blocks->name . '.php');
                $parent_content =  file_get_contents($this->pathBlocks . $this->blocks->name . '.php');
                $blockContent = $this->replace_between($parent_content, "action->title = Azl . '", "')", $this->blocks->title;
                $blockContent = $this->replace_between($blockContent, "this->icon = '", "'", $this->blocks->icon);
                $this->blocks->input .= '_' . $this->blocks->id;
                $this->blocks->name = $this->blocks->name. "_".$this->blocks->id;
                $blockFile = ZFileHelper::normalizePath($this->pathBlocks . $this->blocks->name . '.php');
            }else
            {
                $blockContent = strtr($sample, [
                    '{title}' => $this->blocks->title,
                    '{icon}' => $this->blocks->icon,
                ]);
            }

        } else {
            $blockFile = ZFileHelper::normalizePath($this->pathBlocks . $this->blocks->name . '.php');
            $blockContent = $this->replace_between($this->old_content, "action->title = Azl . '", "')", $this->blocks->title;
            $blockContent = $this->replace_between($blockContent, "this->icon = '", "'", $this->blocks->icon);
            if(file_exists(ZFileHelper::normLinux($this->pathBlocks . "/" . $this->old_name . '.php')))
                unlink(ZFileHelper::normLinux($this->pathBlocks . "/" . $this->old_name . '.php'));

        }

        $file = file_put_contents($blockFile, $blockContent);

        if (is_file($blockFile) && $file !== false) {
            Az::debug($blockFile, 'Block file created');
        } else
            Az::error($blockFile, 'Cannot create block file');
    }

    public function delete(PageBlocks $block)
    {
        $old_block_name = $block->name;
        if ($block !== NULL)
            if (file_exists($this->pathBlocks . $old_block_name . '.php')) {
                $a = $this->pathBlocks . '.trash/' . $old_block_name;
                $dir = str_replace(bname($a), '', $a);
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);
                rename($this->pathBlocks . $old_block_name . '.php', $a . '.php');
            }
    }
    #endregion

    #region helper
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

    public function sample($block_type_id)
    {

        $block_type = PageBlocksType::findOne((int)$block_type_id);
        $root = Root . "/blocks/$block_type->name/azk";
        if (!is_dir($root))
            return  null;
        $data = ZFileHelper::scanFilesPHP($root);
        //return $this->result($data);

        foreach ($data as $path) {
            $base_name = str_replace('.php', '', bname($path));
            $out[$base_name] = $base_name;
        }
        
        return $out;
    }
    #endregion
}
