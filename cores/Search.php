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

class Search extends ZFrame
{
    #region Vars
    public $pathBlocks;

    #endregion


    #region Main

    public function init()
    {
        parent::init();

        $this->pathBlocks = Root . '/webhtm/block/';

        
    }
    #endregion

    #region Service

    private function search($class)
    {

        /** @var Models $class */
        $models = $class::find()
            ->where(['like', 'name', $_GET['q'] . '%', false])->all();

        return $models;

    }

    #endregion





}
