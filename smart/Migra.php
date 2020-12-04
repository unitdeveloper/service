<?php

/**
 *
 * Author:  Asror Zakirov
 * Date:    22.05.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\smart;


use Yii;
use yii\caching\TagDependency;
use yii\db\ColumnSchemaBuilder;
use yii\db\Connection;
use yii\db\IndexConstraint;
use yii\db\TableSchema;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\dbitem\data\FormDb;

//use zetsoft\models;
use zetsoft\dbitem\ALL\ZMigraCreateItem;
use zetsoft\models\ALL;
use zetsoft\models\drag\DragConfig;
use zetsoft\models\drag\DragConfigDb;
use zetsoft\models\drag\DragForm;
use zetsoft\models\drag\DragFormDb;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZConnection;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZMigration;
use zetsoft\system\module\Models;
use zetsoft\system\schema\PgSqlSchema;
use zetsoft\system\kernels\ZFrame;
use const WyriHaximus\Constants\HTTPStatusCodes\CONTINUE_;

class Migra extends ZFrame
{
    #region Vars

    public $secondAttempt = false;

    /** @var Connection $db */
    public $db;

    /** @var PgSqlSchema $schema */
    public $schema;

    /** @var TableSchema[] $tables */
    public $tables;
    
    public $addComment = false;

    /**
     *
     * Constansts
     */

    public const Path = [
        'aliasPath' => '@zetsoft/models/App/' . App,
        'aliasAllPath' => '@zetsoft/models/ALL',

    ];

    /**
     *
     * Private vars
     */

    private $path;
    private $pathAll;
    private $classes;

    #endregion

    #region Main

    public function init()
    {
        parent::init();
        if (Az::$app->db->dbType !== ZConnection::Db_PgSQL)
            return Az::error($this->db->dbType, 'Primary DB Type Should Be PostgreSQL');

        $this->db = Az::$app->db;
        $this->schema = $this->db->schema;

        /**
         *
         * Path Fixes
         */
        $this->path = Yii::getAlias(self::Path['aliasPath']);
        $this->pathAll = Yii::getAlias(self::Path['aliasAllPath']);

        $this->classes = $this->scan();

        Az::count($this->classes, 'Choosed Classes');


        return true;
    }

    #endregion


    #region Drags


    public function getAttributesFromTable($modelId, $type)
    {

        $return = [];

        $class = DragFormDb::class;
        $column = 'drag_config_db_id';
        if ($type === 'form') {
            $class = DragForm::class;
            $column = 'drag_config_id';
        }

        $columns = $class::find()
            ->where([
                $column => $modelId
            ])
            ->all();

        foreach ($columns as $column)
            $return[$column->name] = $column->name;

        return $return;
    }


    public function scanFromTable($type)
    {
        $return = [];

        $models = DragConfigDb::find()->all();

        if ($type === 'form')
            $models = DragConfig::find()->all();

        foreach ($models as $model)
            $return[$model->id] = $this->bootFull($model->class_name);

        return $return;
    }


    #endregion

    #region Getter


    public function getModelAttributesFromTable($type = 'model')
    {

        $attributes = [];
        $classes = Az::$app->smart->migra->scanFromTable($type);

        foreach ($classes as $modelId => $class) {
            $attributes[$class] = $this->getAttributesFromTable($modelId, $type);
        }

        return $attributes;

    }

    public function getAttrs($modelName)
    {

        /** @var Models $model */

        $return = [];


        $model = new $modelName();

        foreach ($model->columns as $key => $column) {
            $title = ucfirst($key);
            if (!empty($column->title))
                $title = $column->title;

            $return[$key] = $title;
        }

        return $return;

    }


    public function getAttrsOfModel($exclude = [])
    {

        $models = $this->scan();


        $return = [];
        foreach ($models as $model) {

            if (!ZArrayHelper::isIn($model, $exclude))
                $return[$model] = $this->getAttrs($model);
        }

        return $return;

    }

    #endregion

    #region Scans

    public function scan($indexClass = false)
    {
        $return = [];

        $models = [];

        /**
         *
         * Fill folders
         */
        $folders = $this->category();
        $smartFolder = $this->paramGet('smartFolder');

        foreach ($folders as $folder) {
            if (!empty($smartFolder))
                if (!ZArrayHelper::isIn($folder, $smartFolder))
                    continue;

            $model = ZFileHelper::scanFilesPHP(Root . "/models/$folder");
            $models = ZArrayHelper::merge($models, $model);
        }

        //For App
        $pathApp = Root . '/models/App/' . App;
        $modelApp = [];
        if (empty($smartFolder))
            $modelApp = ZFileHelper::scanFilesPHP($pathApp, false);

        $models = ZArrayHelper::merge($models, $modelApp);

        foreach ($models as $model) {

            $smartClass = $this->paramGet('smartClass');

            $model = ZInflector::classClean($model);
            if (!empty($smartClass))
                if (!ZArrayHelper::isIn(bname($model), $smartClass))
                    continue;

            $class = ZInflector::classSpace($model);

             if (!class_exists($class))
                 continue;

            if ($indexClass)
                $return[$class] = $class;
            else
                $return[] = $class;

        }

        return $return;
    }

    /**
     *
     * Function  appCategories to get models used in app
     * @param bool $withTitles , if true array keys are models categories, and values are category titles, else array values are models categories
     * @return  array
     * @author Daho
     * @since 16.08.2020
     *
     */
    public function category($withTitles = false)
    {
        $return = [];
        $cats = $this->bootEnv('dbModel');

        foreach (ALL::run() as $key => $title) {

            if (ZArrayHelper::isIn($key, $cats))
                if ($withTitles)
                    $return[$key] = $title;
                else
                    $return[] = $key;
        }


        return $return;
    }

    public function categoryApp($withTitles = false)
    {
        $appName = $this->bootEnv('appName');
        $appTitle = $this->bootEnv('appTitle');

        $return = [];

        if ($withTitles)
            $return[$appName] = $appTitle;
        else
            $return[] = $appName;

        return $return;

    }


    public function models($withTitles = false)
    {
        $models = [];

        /**
         *
         * Fill folders
         */
        $folders = $this->category(true);

        foreach ($folders as $key => $folder) {
            $model = ZFileHelper::scanFilesPHP(Root . "/models/$key");
            $models = ZArrayHelper::merge($models, $model);
        }

        //For App
        $pathApp = Root . '/models/App/' . App;
        $modelApp = ZFileHelper::scanFilesPHP($pathApp, false);

        $models = ZArrayHelper::merge($models, $modelApp);

        $return = [];
        foreach ($models as $model) {
            $model = ZInflector::classClean($model);
            $class = ZInflector::classSpace($model);

            if (!class_exists($class))
                continue;

            /*    if ($withTitles)
                    $return[$class] = $title;
                else
                    $return[] = $class;*/

            $return[] = $class;
        }


        return $return;
    }


    public function appScan($value = false)
    {
        $return = [];
        //     vd($this->path);
        $models = ZFileHelper::scanFilesPHP($this->path);

        foreach ($models as $model) {

            $model = ZInflector::classClean($model);

            if (!empty(Az::$app->params['smartClass']))
                if (!ZArrayHelper::isIn(bname($model), Az::$app->params['smartClass']))
                    continue;

            $class = ZInflector::classSpace($model);

            if ($value)
                $return[$class] = $class;
            else
                $return[] = $class;

        }


        return $return;
    }


    public function baseModels()
    {
        $return = [];
        $models = $this->scan();

        foreach ($models as $model) {
            $return[$model] = bname($model);
        }
        return $return;
    }

    public function scanName()
    {
        $classes = $this->scan();
        $return = [];

        foreach ($classes as $class) {
            $name = bname($class);
            $return[$name] = $name;
        }

        return $return;
    }

    public function scanTitle()
    {
        $classes = $this->scan();
        $return = [];
        // vdd(  $classes);
        foreach ($classes as $class) {

            /** @var Models $object */
            $name = bname($class);
            $return[$name] = $class::$title;
        }

        return $return;
    }

    #endregion

    #region Core


    public function run()
    {


        global $boot;

        $boot->start();

        Az::start(__FUNCTION__);

        $classes = $this->classes;
        $allClasses = [];

        $this->paramSet(paramMigration, true);

        foreach ($classes as $class) {


            /** @var Models $model */
            $model = new $class();

            if (!$model->configs->makeDB)
                continue;

            $this->paramSet(paramFull, true);
            $model->columns();
            $className = ZStringHelper::basename(get_class($model));
            $tableName = ZInflector::underscore($className);

            /** @var ALLApp $allApp */
            $allApp = $model->allApp();

            /** @var ConfigDB $config */
            $config = $allApp->configs;
            $columns = [];
            $columnsMain = [];
            $indexes = [];
            $fKeys = [];
            $pKeys = [];

            if ($config->addID)
                $columns['id'] = (new ZMigration())->primaryKey()
                    ->notNull()
                    ->defaultExpression("nextval('$tableName" . "_id_seq'::regclass)")
                    ->comment('ID');

            /** @var FormDb $formDb */
            foreach ($allApp->columns as $key => $formDb) {

                $dbType = $formDb->dbType;

                /**
                 *
                 * Check ID
                 */
                Az::$app->smart->model->allApp = $allApp;
                if (Az::$app->smart->model->isIndexAddId($key))
                    continue;

                $columns[$key] = $this->schema(new ZMigration(), $formDb);
                $columnsMain[] = $key;

                if ($formDb->index)
                    $indexes[$key] = ['isUnique' => $formDb->indexUnique];

                if ($formDb->isPKey)
                    $pKeys[] = $key;

                if ($formDb->fkCreate && !empty($formDb->fkTable))
                    $fKeys[$key] = [
                        'keyTable' => $formDb->fkTable,
                        'keyColumn' => $formDb->fkColumn
                    ];
            }


            Az::debug($columnsMain, "Column for: {$tableName}");

            $allClasses[$className]['className'] = $class;
            $allClasses[$className]['columns'] = $columns;
            $allClasses[$className]['config'] = $config;
            $allClasses[$className]['indexes'] = $indexes;
            $allClasses[$className]['fKeys'] = $fKeys;
            $allClasses[$className]['pKeys'] = $pKeys;
        }

        Az::info('Starting Create Migra');


        foreach ($allClasses as $key => $allClass) {
            $migra = new ZMigration();
            $migra->class = $key;
            $migra->table = ZInflector::underscore($key);

            Az::info($migra->table, 'Creating table');

            $migra->init();
            $migra->tableAdd($allClass['columns']);

            Az::info($migra->table, 'Creating indexes');
            if ($allClass['indexes']) {
                foreach ($allClass['indexes'] as $index => $item) {
                    $migra->indexAdd($index, $item['isUnique']);
                }
            }
        }

        $boot->finish();

        foreach ($allClasses as $key => $allClass) {
            $migra = new ZMigration();

            $class = $allClass['className'];

            Az::info($key, 'Adding keys and comment to table', null, 3);

            /** @var \zetsoft\system\module\Models $model */
            $model = new $class();

            $allApp = $model->allApp();
            $tableName = ZInflector::underscore($key);
            $jsons = json_encode($allApp, JSON_PRETTY_PRINT);


            if ($this->addComment)
            if (!empty($jsons))
                $migra->addCommentOnTable($tableName, $jsons);

            $migra->class = $key;
            $migra->table = $tableName;
            $migra->init();

            foreach ($allClass['fKeys'] as $fKey => $item) {
                $migra->keyFAdd($fKey, $item['keyTable'], $item['keyColumn']);
            }

            foreach ($allClass['pKeys'] as $pKey) {
                $migra->keyPAdd($pKey);
            }

        }

    }


    public function clean()
    {
        Az::start(__FUNCTION__);

        $classes = $this->classes;
        if (!empty($classes)) {
            Az::$app->utility->cache->flushSchema();
        }

        Az::info('First Attempt', null, null, 3);

        foreach ($classes as $className) {
            TagDependency::invalidate(Az::$app->cache, $className);
            $table = new $className();
            $tableName = ZInflector::underscore(bname($className));

            if (!$this->dbHasTable($tableName))
                continue;

            $migra = new ZMigration();

            $migra->class = ZStringHelper::basename(get_class($table));
            $migra->init();
            $migra->keyRemove();

            $this->removeIndexes($table);
            $this->removeTable($table);

            Az::debug($className, 'Table Cleaned in 1-st');
        }

        if ($this->secondAttempt) {

            Az::info('Second Attempt', null, null, 3);
            foreach ($classes as $className) {

                $table = new $className();
                $tableName = ZInflector::underscore($className);

                if (!$this->dbHasTable($tableName))
                    continue;

                $migra = new ZMigration();
                $migra->class = ZStringHelper::basename(get_class($table));
                $migra->init();

                $migra->keyRemove();
                $this->removeIndexes($table);
                $this->removeTable($table);

                Az::debug($className, 'Table Cleaned in 2-nd');


            }
        }

        Az::end();

    }


    #endregion


    #region Services

    private function removeIndexes($table)
    {

        $tableName = ZInflector::underscore($table::className());
        /** @var IndexConstraint[] $indexes */
        $indexes = $this->schema->loadTableIndexes(ZInflector::underscore($tableName));

        foreach ($indexes as $index) {

            try {
                $query = 'DROP INDEX ' . $this->db->quoteTableName($index->name) . ' ON ' . $this->db->quoteTableName($tableName) . ' CASCADE';
                Az::$app->db->createCommand($query)->execute();
            } catch (\Exception $e) {
                Az::warning($e->getMessage(), 'Exception in dropIndex');
            }

        }
    }

    private function removeTable($table)
    {
        $table_name = ZStringHelper::basename(get_class($table));
        /** @var IndexConstraint[] $indexes */

        try {
            $query = 'DROP TABLE ' . $this->db->quoteTableName(ZInflector::underscore($table_name)) . ' CASCADE';
            Az::$app->db->createCommand($query)->execute();
        } catch (\Exception $e) {
            Az::warning($e->getMessage(), 'Exception in dropTable');
        }

    }

    private function schema(ZMigration $migration, FormDb $formDB)
    {
        $dbType = $formDB->dbType;

        /** @var ColumnSchemaBuilder $migra */
        $migra = $migration->$dbType($formDB->length);

        $migra->comment($formDB->title);

        if ($formDB->notNull)
            $migra->notNull();
        else
            $migra->null();


        if ($formDB->defaultValue)
            $migra->defaultValue($formDB->defaultValue);

        if ($formDB->unsigned)
            $migra->unsigned();

        $migra->inject();
        $migra->comment = $migra->comments;
        return $migra;
    }

    /**
     *
     * Function  genid
     * @author Daho
     * @since 26.10.2020
     * @lines 5
     *
     */
    public function genid()
    {
        foreach ($this->classes as $class) {
            /** @var ZActiveRecord $model */
            $model = new $class();
            if ($model->configs->addID)
                $this->sequence(ZInflector::underscore(bname($class)));
        }
    }

    /**
     *
     * Function  sequence
     * @param string $tablename
     * @author Daho
     * @lines 7
     */
    private function sequence(string $tablename)
    {

        $name = $tablename . '_id_seq';
        $db = Az::$app->db;

        $command = "DROP SEQUENCE $name CASCADE";
        $this->_exec($db, $command);


        $this->_exec($db, "CREATE SEQUENCE \"$name\" INCREMENT 1 MINVALUE  1 START 1 CACHE 1");

        $this->_exec($db, "SELECT setval('\"$name\"'::regclass,(SELECT MAX(\"id\") FROM \"$tablename\"))");

        $this->_exec($db, "ALTER TABLE \"$tablename\" ALTER COLUMN \"id\" SET DEFAULT nextval('\"$name\"'::regclass)");


    }


    /**
     * @param Connection $db
     * @param string $sql
     * @return bool
     * @author Daho
     * @lines  3
     *
     */
    private function _exec($db, $sql)
    {

        try {

            $db->createCommand($sql)->execute();


            return true;

        } catch (\Exception $e) {

        }
    }


    /**
     * @param Connection $db
     * @param string $sql
     * @return bool
     */
    public function exec($sql): bool
    {
        try {
            $this->db->createCommand($sql)->execute();

            return true;

        } catch (\Exception $e) {
            Az::warning($e->getMessage(), 'SQL createCommand($sql)->execute()');
            return false;
        }
    }


}
