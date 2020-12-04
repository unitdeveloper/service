<?php

/**
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\smart;


use yii\helpers\Inflector;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\Settings;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class Norms extends ZFrame
{

#region Vars
    public $dev = false;

    public const devdata = [
        'dir' => 'zetsoft/storing/testing/service/',
        'use' => 'zetsoft\storing\testing\service\\',
    ];
    public const proddata = [
        'dir' => 'zetsoft/service/',
        'use' => 'zetsoft\service\\',
    ];

    private $utd;
    private $fl;


    /* @var ALLApp $allApp */
    public $allApp;

    /* @var Settings $proper */
    public $setter;

    private $path;
    private $pathAll;

    /**
     *
     * Services
     */

    private $properties;
    private $uses;

    private $pathServiceAll;
    private $pathService;

    public $hasOne = [];
    public $hasMulti = [];
    public $hasMany = [];

    /**
     *
     * Constants
     */

    public const Path = [

        'namespaceCore' => "zetsoft\dbcore\\" . App . '\\',
        'namespaceCoreAll' => "zetsoft\dbcore\\ALL\\",

        'aliasFormPath' => '@zetsoft/former/' . App,
        'aliasFormAllPath' => '@zetsoft/former/ALL',

    ];
#endregion

#region ALL

    public function init()
    {
        parent::init();
        /**
         *
         * Path Fixes
         */
        $this->path = \Yii::getAlias(self::Path['aliasFormPath']);
        $this->pathAll = \Yii::getAlias(self::Path['aliasFormAllPath']);

        if ($this->dev) {
            $this->utd = self::devdata['use'];
            $this->fl = self::devdata['dir'];
        } else {
            $this->utd = self::proddata['use'];
            $this->fl = self::proddata['dir'];
        }


    }

    public function run()
    {
        $this->model();
        $this->form();
    }

    #endregion

#region Model

    public function model()
    {
        $this->paramSet(paramNorms, true);
        $this->paramSet(paramFull, true);

        global $classIgnore;
        $classes = Az::$app->smart->migra->scan();
        $allModels = Az::$app->smart->migra->models();
        $this->relation($allModels);

        foreach ($classes as $class) {

            /** @var Models $model */
            $model = new $class();
            Az::debug('Normalizing class is: ' . $class);
            $this->setter = new Settings();

            $this->setter->type = Settings::type['model'];

            $this->setter->class = $class;
            $this->setter->className = bname($class);

            $parent = get_parent_class($model);
            $this->setter->classBase = $parent;

            $namespace = str_replace("\\" . $this->setter->className, '', $class);
            $this->setter->namespace = $namespace;

            /**
             *
             * AllApp Filling
             */

            $this->allApp = $model->allApp();
            $this->allApp->configs->hasMulti = [];
            $this->allApp->configs->hasOne = [];
            $this->allApp->configs->hasMany = [];
            if (isset($this->hasOne[bname($class)])) {
                foreach ($this->hasOne[bname($class)] as $key => $item) {
                    if (ZArrayHelper::isIn($key, $classIgnore))
                        continue;
                    foreach ($item as $k => $v) {
                        $this->allApp->configs->hasOne[$key] = $item;
                    }
                }
            }

            if (isset($this->hasMulti[bname($class)])) {
                foreach ($this->hasMulti[bname($class)] as $key => $item) {
                    if (ZArrayHelper::isIn($key, $classIgnore))
                        continue;

                    foreach ($item as $k => $v) {
                        $this->allApp->configs->hasMulti[$key] = $item;
                    }
                }
            }
            if (isset($this->hasMany[bname($class)])) {
                foreach ($this->hasMany[bname($class)] as $key => $item) {

                    if (ZArrayHelper::isIn($key, $classIgnore))
                        continue;

                    foreach ($item as $k => $v) {
                        $this->allApp->configs->hasMany[$key] = $item;
                    }
                }
            }

            $this->setter->allApp = $this->allApp;
            $this->allApp->cards = $this->blocksFix();


            Az::$app->smart->puters->run($this->setter);
        }
    }

    private function relation($classes)
    {
        $this->hasOne = [];
        $this->hasMulti = [];
        $this->hasMany = [];
        foreach ($classes as $class) {
            /** @var Models $model */

            $model = new $class();

            $hasManyTables = [];
            $hasMultiTables = [];
            $hasOnetables = [];

            foreach ($model->allApp()->columns as $key => $formDB) {
                switch (true) {
                    case $formDB->fkMulti:
                        $tableName = $formDB->fkMulti;

                        $className = ZInflector::camelize($tableName);

                        $hasMultiTables[] = [
                            'className' => $className,
                            'key' => $key,
                        ];

                        break;

                    case !empty($formDB->fkTable):
                        $tableName = $formDB->fkTable;

                        $className = ZInflector::camelize($tableName);
                        $className = 'zetsoft\\models\\' . $this->catModel($className) . '\\' . $className;

                        $hasManyTables[] = [
                            'className' => $className,
                            'key' => $key,
                        ];
                        $hasOnetables[] = [
                            'className' => bname($className),
                            'key' => $key,
                        ];


                        break;

                    case ZStringHelper::endsWith($key, '_ids'):
                        $tableName = str_replace('_ids', '', $key);

                        $className = Inflector::camelize($tableName);

                        $hasMultiTables[] = [
                            'className' => $className,
                            'key' => $key,
                        ];
                        break;

                    case ZStringHelper::endsWith($key, '_id'):
                        $tableName = str_replace('_id', '', $key);

                        $className = Inflector::camelize($tableName);

                        $hasManyTables[] = [
                            'className' => $className,
                            'key' => $key,
                        ];
                        $hasOnetables[] = [
                            'className' => $className,
                            'key' => $key,
                        ];


                        break;


                }
            }


            foreach ($hasOnetables as $hasOneTable) {

                $baseClass = bname($class);
                $baseRelateClass = bname($hasOneTable['className']);

                //Check if class one of the Core Models

                if (ZStringHelper::startsWith($baseClass, 'Test', false))
                    continue;

                $this->hasOne[bname($class)][$hasOneTable['className']][$hasOneTable['key']] = 'id';
            }

            foreach ($hasMultiTables as $hasMultiTable) {

                $baseClass = bname($class);
                $baseRelateClass = bname($hasMultiTable['className']);
                //Check if class one of the Core Models
                if (ZStringHelper::startsWith($baseClass, 'Test', false))
                    continue;

                $this->hasMulti[bname($class)][$hasMultiTable['className']][$hasMultiTable['key']] = 'id';

            }

            foreach ($hasManyTables as $hasManyTable) {

                $baseClass = bname($class);
                $baseRelateClass = bname($hasManyTable['className']);

                //Check if class one of the Core Models
                if (ZStringHelper::startsWith($baseClass, 'Test', false))
                    continue;

                $this->hasMany[$baseRelateClass][$baseClass][$hasManyTable['key']] = 'id';
            }
        }

    }

#endregion

#region Forms


    /**
     * Function  form
     *
     */
    public function form()
    {

        $this->paramSet(paramNorms, true);
        $classes = $this->formScan();

        foreach ($classes as $class) {

            if (!class_exists($class))
                continue;

            /** @var Models $model */
            $model = new $class();

            $this->setter = new Settings();

            $this->setter->class = $class;
            $parent = get_parent_class($model);

            $this->setter->classBase = $parent;
            $this->setter->className = ZStringHelper::basename($class);

            $this->setter->type = Settings::type['form'];
            $this->setter->namespace = $this->space($class);

            $this->allApp = $model->allApp();
            $this->allApp->cards = $this->blocksFix();
            $this->setter->allApp = $this->allApp;


            Az::$app->smart->puters->run($this->setter);

        }
    }


    public function formScan($indexClass = false)
    {
        $return = [];
        $models = [];

        /**
         *
         * Fill folders
         */
        $folders = ZFileHelper::scanFolder(Root . '/former');

        foreach ($folders as $folder) {

            if (!empty($this->paramGet('smartFolder')))
                if (!ZArrayHelper::isIn(bname($folder), $this->paramGet('smartFolder')))
                    continue;

            $model = ZFileHelper::scanFilesPHP($folder);
            $models = ZArrayHelper::merge($models, $model);
        }

        foreach ($models as $model) {

            $model = ZInflector::classClean($model);

            if (!empty($this->paramGet('smartClass')))
                if (!ZArrayHelper::isIn(bname($model), $this->paramGet('smartClass')))
                    continue;

            $class = ZInflector::classSpace($model);

            if ($indexClass)
                $return[$class] = $class;
            else
                $return[] = $class;

        }


        return $return;
    }


    public function formScanApp()
    {
        $return = [];
        $models = [];

        if (file_exists($this->path))
            $models = ZFileHelper::findFiles($this->path, [
                'recursive' => false
            ]);

        foreach ($models as $model) {
            $model = ZFileHelper::normalizePath($model);
            $model = str_replace('.php', '', $model);

            $className = bname($model);

            if (!empty(Az::$app->params['smartClass']))
                if (!ZArrayHelper::isIn($className, Az::$app->params['smartClass']))
                    continue;

            $class = strtr($model, [
                Az::getAlias('@zetsoft') => 'zetsoft',
                '/' => '\\'
            ]);

            $return[] = $class;
        }

        return $return;
    }


    private function space($class)
    {
        $className = bname($class);
        return str_replace("\\{$className}", '', $class);
    }


#endregion

#region CoreApp


    public function null()
    {
        $this->hasOne = null;
        $this->hasMany = null;
        $this->hasMulti = null;
    }


     /**
     *
     * Function  blocksFix
     * @param ALLApp $allApp
     */
    public function blocksFix()
    {

        global $boot;

        $card = $this->allApp->cards;
        if (!$this->emptyVar($card)) {

            if (!$card instanceof \Countable)
                return $card;
        }
        if (!empty($card))
            if ($this->overwriteCard($card) && !$boot->env('overwriteCard')) {
                return $this->allApp->cards;
            }


        $blocks = [];

        foreach ($this->allApp->columns as $key => $formDB) {
            if ($this->allApp->configs instanceof ConfigDB)
                if (Az::$app->smart->model->isExcluded($key))
                    continue;

            $blocks[] = $key;
        }

        return [
            [
                'title' => Az::l('Первый этап'),
                'shows' => true,
                'items' => [
                    [
                        'title' => Az::l('Форма'),
                        'shows' => true,
                        'items' => [$blocks]

                    ]
                ]
            ]
        ];
    }

    public function overwriteCard($cards)
    {

        if (count($cards) === 1 && count(ZArrayHelper::getValue($cards, '0.items.0.items')) === 1)
            return true;

        if (empty($cards)) return true;

        return false;
    }


#endregion

#region Cards

    public function cards()
    {
                
    }
    
#endregion


}
