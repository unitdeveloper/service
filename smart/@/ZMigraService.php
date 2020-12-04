<?php

/**
 *
 * Author:  Asror Zakirov
 * Date:    22.05.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace common\service\giiapp;


use common\service\giiapp\consts\ZMigraConst;
use common\system\Az;
use common\system\helpers\ZArrayHelper;
use common\system\helpers\ZFileHelper;
use common\system\helpers\ZInflector;
use common\system\kernels\ZFrame;
use consoles\migrate\table\CompanyTable;
use PDO;
use Yii;
use yii\console\controllers\MigrateController;
use yii\db\ColumnSchema;
use yii\db\Connection;
use yii\db\Query;
use yii\db\TableSchema;
use yii\di\Instance;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

class ZMigraService extends ZFrame implements ZMigraConst
{


    public const sPathSampleMain = '@common/service/giiapp/sample/Migration.php';
    public const sPathSampleData = '@common/service/giiapp/sample/MigrationData.php';
    public const sPathAlias = '@consoles/migrate';

    public const sSpanColumn = PHP_EOL . '                ';
    public const sSpanMethod = '        ';


    public $bData = false;
    public $bOverwrite = true;


    /**
     *
     * Connection Strings
     */
    public $sTargetConnection = 'db2';
    public $sConnection = 'db';


    /** @var Connection $_db */
    private $_db;

    /** @var Connection $_tdb */
    private $_tdb;


    private $_sPath;
    private $_sPathTable;
    private $_sPathKeyes;
    private $_sPathDatas;

    private $_sSampleMain;
    private $_sSampleData;

    private $_sReplacePK = <<<PHP

                ,
PHP;


    /**
     *
     * Function  init
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->_db = Instance::ensure($this->sConnection, Connection::class);
        $this->_tdb = Instance::ensure($this->sTargetConnection, Connection::class);

        $this->_sPath = Yii::getAlias(self::sPathAlias);


        /**
         *
         * Folders
         */
        $this->_sPathTable = "{$this->_sPath}/table";
        $this->_sPathKeyes = "{$this->_sPath}/keyes";
        $this->_sPathDatas = "{$this->_sPath}/datas";

        ZFileHelper::createDirectory($this->_sPathTable);
        ZFileHelper::createDirectory($this->_sPathKeyes);
        ZFileHelper::createDirectory($this->_sPathDatas);


        /**
         *
         * Sample Init
         */

        $this->_sSampleMain = file_get_contents(Yii::getAlias(self::sPathSampleMain));
        $this->_sSampleData = file_get_contents(Yii::getAlias(self::sPathSampleData));


    }


    private const Type_Table = 1;
    private const Type_Keys = 2;
    private const Type_Data = 3;

    private const Name_Table = 'consoles\migrate\table\\';
    private const Name_Keys = 'consoles\migrate\keys\\';
    private const Name_Data = 'consoles\migrate\data\\';

    private function _class(int $iType = self::Type_Table, bool $bNameSpace = true)
    {

        switch ($iType) {
            case self::Type_Table:
                $modelPath = $this->_sPathTable;
                break;

            case self::Type_Keys:
                $modelPath = $this->_sPathKeyes;
                break;

            case self::Type_Data:
                $modelPath = $this->_sPathDatas;
                break;
        }

        $aModel = ZFileHelper::findFiles($modelPath, [
            'recursive' => false
        ]);

        foreach ($aModel as $sModel) {
            $sClassName = bname($sModel);

            $sClassName = str_replace('.php', '', $sClassName);
            if (!$bNameSpace)
                $aResultModel[] = $sClassName;
            else {
                switch ($iType) {
                    case self::Type_Table:
                        $sNamespace = self::Name_Table;
                        break;

                    case self::Type_Keys:
                        $sNamespace = self::Name_Keys;
                        break;

                    case self::Type_Data:
                        $sNamespace = self::Name_Data;
                        break;
                }

                $aResultModel[] = $sNamespace . $sClassName;
            }
        }

        return $aResultModel;
    }


    public function create()
    {
        $this->_migration();
        if ($this->bData)
            $this->_data();
    }


    public function apply($sConn = 'db2')
    {

        /** @var CompanyTable[] $aClass */
        $aClass = $this->_class();

        foreach ($aClass as $sClass) {

            /** @var CompanyTable $table */
            $table = new $sClass();
            $table->db = $sConn;
            $table->init();
            $table->remove();
            $table->create();
        }

        print_r($aClass);

        /*   $this->_aMigrationT();

           if ($this->bData)
               $this->_AData();

           $this->_AMigrationK();
           $this->_APK();*/
    }

    public function remove($sConn = 'db2')
    {

        /** @var CompanyTable[] $aClass */
        $aClass = $this->_class();

        foreach ($aClass as $sClass) {

            /** @var CompanyTable $table */
            $table = new $sClass();
            $table->db = $sConn;
            $table->init();
            $table->remove();
        }

    }


    public function APK($connection = 'db')
    {
        $this->_APK();
    }

    public function copy($data = true, $connection = 'db', $targetConnection = 'db2')
    {
        $this->create($connection, $data);
        $this->apply($targetConnection, $data);
    }


    private function _prepareDb($params)
    {

        switch ($params['type']) {

            case 'pgsql' :
                $this->_preparePg($params);
                break;

            case 'mysql' :
                $this->_prepareMysql($params);
                break;

            default:
                throw new Exception("What DB is this??");
        }

        return true;

    }

    private function _parseConnection($conn)
    {

        $out = [];
        $components = Yii::$app->components;

        if (!isset($components[$conn]))
            return false;

        $out['username'] = $components[$conn]['username'];
        $out['password'] = $components[$conn]['password'];

        $dsn = explode(':', $components[$conn]['dsn']);
        $out['type'] = $dsn[0];

        foreach (explode(';', $dsn[1]) as $item) {

            $param = explode('=', $item);
            $out[$param[0]] = $param[1];

        }

        return $out;

    }

    private function _preparePg($params)
    {

        $cstring = 'host=' . $params['host'];
        $cstring .= ' port=' . @$params['port'];
        $cstring .= ' user=' . $params['username'];
        $cstring .= ' password=' . $params['password'];

        $dbconn = pg_connect($cstring);

        $query = "CREATE DATABASE \"" . $params['dbname'] . "\" ";
        $query .= "WITH OWNER = \"" . $params['username'] . "\" ENCODING = 'UTF8'";

        $r = pg_query($dbconn, $query);

        pg_close($dbconn);

        return $r;

    }

    private function _prepareMysql($params)
    {

        $port = @$params['port'] ?? '3306';
        $dbconn = new PDO("mysql:host={$params['host']};port={$port}", $params['username'], $params['password']);;

        $query = "CREATE DATABASE `{$params['dbname']}` CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';";
        $r = $dbconn->exec($query);

        return $r;

    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //----------------------------------------MIGRATION-GENERATOR----------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    /**
     *
     * Function  _migration
     * @throws \yii\base\Exception
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    private function _migration()
    {

        $sMode = 'table';

        foreach ($this->_db->schema->getTableSchemas() as $table) {

            if (ZArrayHelper::isIn($table->name, aCoreTable))
                continue;

            Az::debug($table->name, 'Current Process');

            $sClassName = ZInflector::classify($table->name);
            $sClassName .= ZInflector::titleize($sMode);


            $sContent = strtr(
                $this->_sSampleMain,
                [
                    'ZClassName' => $sClassName,
                    'ZMigrateMode' => $sMode,
                    '// create' => $this->_createUp($table),
                    '// remove' => $this->_createDrop($table),
                ]);


            $sFileName = "{$this->_sPathTable}/{$sClassName}.php";

            if (!file_exists($sFileName)) {
                file_put_contents($sFileName, $sContent);
            } else {
                if ($this->bOverwrite) {
                    file_put_contents($sFileName, $sContent);
                }
            }

        }


        /*  $keys = $this->_structure($name . '_k', $this->_createUpK());


          FileHelper::createDirectory($file . 'keys/');

          file_put_contents($file . 'keys/' . $name . '_k' . '.php', $keys);*/
    }


    /**
     *
     * Function  _createUp
     * @return  string
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    private function _createUp(TableSchema $table)
    {

        $sReturn = $this->_generateCreateTable($table->name);
        $sReturn .= $this->_generateColumns($table->columns, $this->_db->schema->findUniqueIndexes($table));

        $sReturn .= $this->_generatePrimaryKey($table->primaryKey, $table->columns);

        $sReturn .= '        ]);' . PHP_EOL;

        $sReturn .= $this->_generateTableComment($table);

        $sReturn = strtr($sReturn, [
            $this->_sReplacePK => ','
        ]);

        return $sReturn;

    }

    private function _createDrop(TableSchema $table)
    {

        $sReturn = $this->_generateDropTable($table->name);

        if (!empty($table->foreignKeys)) {

            $sReturn .= ' // fk: ';

            foreach ($table->foreignKeys as $fk) {

                foreach ($fk as $k => $v) {

                    if (0 === $k)
                        continue;

                    $sReturn .= "$k, ";

                }

            }
            $sReturn = rtrim($sReturn, ', ');

        }
        $sReturn .= "\n";

        return $sReturn;

    }


    private function _createUpK()
    {

        $stdout = '';

        foreach ($this->_db->schema->getTableSchemas() as $table)
            $stdout .= $this->_generateForeignKey($table);

        return $stdout;

    }


    private function _generateForeignKey($table)
    {
        if (empty($table->foreignKeys))
            return;

        $definition = "// fk: $table->name\n";

        foreach ($table->foreignKeys as $fk) {

            $refTable = '';
            $refColumns = '';
            $columns = '';

            foreach ($fk as $k => $v) {

                if (0 === $k) {

                    $refTable = $v;

                } else {

                    $columns = $k;
                    $refColumns = $v;

                }

            }

            $definition .= sprintf("\$this->addForeignKey('%s', '{{%%%s}}', '%s', '{{%%%s}}', '%s');\n",
                'fk_' . $table->name . '_' . $columns, $table->name, $columns, $refTable, $refColumns);
        }

        return "$definition\n";

    }


    private function _generateCreateTable($name)
    {

        $sReturn = sprintf("        // %s\n\n", $name);
        $sReturn .= sprintf("        \$this->tableCreate('%s', [\n\n", $name);

        return $sReturn;

    }


    private function _generateColumns(array $columns, array $unique)
    {

        $definition = '';

        foreach ($columns as $column) {

            if (ZArrayHelper::isIn($column->name, ZModelService::aExceptionColumn))
                continue;

            $tmp = sprintf("            '%s' => \$this->%s%s,\n",
                $column->name, $this->_getSchemaType($column), $this->_other($column, $unique));

            if (null !== $column->enumValues)
                $tmp = $this->_replaceEnumColumn($tmp);

            $definition .= $tmp . PHP_EOL;

        }

        return $definition;

    }


    private function _generatePrimaryKey(array $pk, array $columns)
    {

        if (empty($pk))
            return '';

        /*  // Composite primary keys
          if (2 <= count($pk)) {

              $ppk = array_map(function ($itm) {
                  return '"' . $itm . '"';
              }, $pk);

              $compositePk = implode(', ', $pk);
              $compositePPk = implode(', ', $ppk);

              return "    'PRIMARY KEY ('.(\$this->db->driverName === 'pgsql' ? '$compositePPk' : '$compositePk').')',\n";

          }*/

        // Primary key not an auto-increment
        /*$flag = false;

        foreach ($columns as $column)
            if ($column->autoIncrement)
                $flag = true;

        if (false === $flag)
            return sprintf("    'PRIMARY KEY ('.(\$this->db->driverName === 'pgsql' ? '\"%s\"' : '%s').')',\n", $pk[0], $pk[0]);*/

        return '';

    }


    private function _generateDropTable($name)
    {

        return self::sSpanMethod . "\$this->tableDrop('{$name}');";

    }


    private function _getSchemaType($column)
    {

        // primary key
        if ($column->isPrimaryKey && $column->autoIncrement) {

            if ('bigint' === $column->type)
                return 'bigPrimaryKey()';

            return 'primaryKey()';

        }

        // boolean
        if ('tinyint(1)' === $column->dbType) {

            return 'boolean()';

        }

        // smallint
        if ('smallint' === $column->type) {

            if (null === $column->size)
                return 'smallInteger()';

            return 'smallInteger';

        }


        // bigint
        if ('bigint' === $column->type) {

            if (null === $column->size)
                return 'bigInteger()';

            return 'bigInteger';

        }

        // enum
        if (null !== $column->enumValues) {
            // https://github.com/yiisoft/yii2/issues/9797

            $enumValues = array_map('addslashes', $column->enumValues);
            return "enum(['" . implode('\', \'', $enumValues) . "'])";

        }

        // others
        if (null === $column->size && 0 >= $column->scale)
            return $column->type . '()';

        return $column->type;

    }


    private function _other(ColumnSchema $column, array $unique)
    {

        $sReturn = '';


        switch (true) {

            case ZArrayHelper::isIn($column->type, ['time', 'timestamp', 'datetime', 'timestamptz', 'timetz']):
                $sReturn .= "()";
                break;

            case    null !== $column->scale && 0 < $column->scale:
                $sReturn .= "($column->precision, $column->scale)";
                break;

            case null !== $column->size && !$column->autoIncrement && 'tinyint(1)' !== $column->dbType:
                $sReturn .= "($column->size)";
                break;

            case  null !== $column->size && !$column->isPrimaryKey && $column->unsigned:
                $sReturn .= "($column->size)";
                break;

        }


        // unsigned
        if ($column->unsigned) {
            $sReturn .= self::sSpanColumn;
            $sReturn .= '->unsigned()';
        }

        // null
        $sReturn .= self::sSpanColumn;

        if ($column->allowNull)
            $sReturn .= '->null()';
        elseif (!$column->autoIncrement)
            $sReturn .= '->notNull()';

        // unique key
        if (!$column->isPrimaryKey && !empty($unique))
            foreach ($unique as $name)
                if (reset($name) === $column->name) {
                    $sReturn .= self::sSpanColumn;
                    $sReturn .= '->unique()';
                }

        // default value
        if (!empty($column->defaultValue)) {
            $sReturn .= self::sSpanColumn;

            if ($column->defaultValue instanceof Expression)
                $sReturn .= "->defaultExpression('$column->defaultValue')";

            elseif (is_int($column->defaultValue))
                $sReturn .= "->defaultValue($column->defaultValue)";

            elseif (is_bool($column->defaultValue))
                $sReturn .= '->defaultValue(' . var_export($column->defaultValue, true) . ')';

            elseif (is_string($column->defaultValue))
                $sReturn .= "->defaultValue('" . addslashes($column->defaultValue) . "')";

        }


        // comment
        if (null !== $column->comment && '' !== $column->comment) {

            $sReturn .= self::sSpanColumn;

            $comment = addslashes($column->comment);
            $comment = str_replace("\n", '\n', $comment);
            $comment = str_replace("\r", '\r', $comment);
            $sReturn .= "->comment('{$comment}')";
        }

        // append
        return $sReturn;

    }


    private function _replaceEnumColumn($tmp)
    {

        return preg_replace("/,\n/", "\",\n", strtr($tmp, [
            '()' => '',
            '])' => ')',
            '),' => ',',
            '$this->enum([' => '"ENUM (',
            '->notNull' => ' NOT NULL',
            '->null' => ' DEFAULT NULL',
            '->defaultValue(' => ' DEFAULT ',
            '->comment(' => ' COMMENT ',
        ]));

    }

    /**
     * @param Connection $db
     * @param TableSchema $table
     * @return string
     * @throws \yii\db\Exception
     */
    private function _generateTableComment(TableSchema $table)
    {

        $dsn = $this->_parseConnection($this->sConnection);

        switch ($this->_db->driverName) {

            case 'pgsql' :
                $sComment = @$this->_db->createCommand("SELECT obj_description('{$table->schemaName}.{$table->name}'::regclass)")->queryOne()['obj_description'];
                break;

            case 'mysql' :
                $sComment = @$this->_db->createCommand("SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='{$dsn['dbname']}' AND table_name='{$table->name}'")->queryOne()['table_comment'];
                break;
            default:
                throw new \Exception("Unknown DB");
                break;
        }


        return "\n        \$this->tableComment('{$table->name}', '\n{$sComment}');\n\n\n";
    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //-------------------------------------------DATA-GENERATOR------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    private function _data()
    {

        $file = $this->_sPath . '/data/';
        FileHelper::createDirectory($file);

        foreach ($this->_db->schema->getTableSchemas() as $table) {

            if ($table->fullName == self::sMigrationTable)
                continue;

            $data = (new Query())->from($table->fullName)->all();
            $structure = $this->_db->getTableSchema($table->fullName)->columns;
            $data = array_map(function ($itm) use ($structure, $data) {
                foreach ($itm as $k => $v) {
                    if ($v != "0") {
                        if (gettype($v) === "string")
                            $v = addslashes($v);
                        $v = str_replace('$', '\$', $v);
                        if (!$v) {
                            if ($structure[$k]->defaultValue == 'int(0)')
                                $v = "0";
                            elseif ($structure[$k]->defaultValue === null)
                                $v = "null";
                            else
                                $v = (string)$structure[$k]->defaultValue;
                        }
                    }
                    $v = (($structure[$k]->type == "integer" || $v == "null") ? $v : '"' . $v . '"');
                    if (!$v && $v !== "0" && $v !== 0) {
                        $aTest = [$v, (array)$structure[$k], $structure[$k]->defaultValue === null, gettype($v), $structure[$k]->type];
                        Az::debug($aTest, 'Test Data Dump');
                    }

                    $itm[$k] = '        "' . $k . '" => ' . $v . ",\n";
                }
                return "    [\n" . implode("", $itm) . "    ],";
            }, $data);

            $content = strtr(
                file_get_contents(Yii::getAlias('@common/service/giiapp/samplee/MigrationData.php')),
                [
                    '//{items}' => implode("\n", $data),
                ]);

            file_put_contents($file . $table->fullName . '.php', $content);

        }

    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //------------------------------------------APPLY-MIGRATION------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    private function _aMigrationT()
    {
        $migration = new MigrateController('migrate', Yii::$app);
        $migration->run('up', ['migrationPath' => $this->_sPathAlias . 'tables/', 'db' => $this->_sTConnection, 'interactive' => false]);
    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //------------------------------------------APPLY-MIGRATION------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    private function _AData()
    {
        $path = $this->_sPath . 'data/';
        foreach ($this->_tdb->schema->getTableSchemas() as $table) {
            $filename = $path . $table->fullName . '.php';
            if (is_file($filename)) {
                echo "Filling: " . $table->fullName . PHP_EOL;
                $data = include $filename;
                if (count($data) > 0)
                    $this->_tdb->createCommand()->batchInsert($table->fullName, array_keys($data[0]), $data)->execute();
                echo "Done: " . $table->fullName . PHP_EOL;
            }
        }
    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //------------------------------------------APPLY-MIGRATION------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    private function _AMigrationK()
    {
        $migration = new MigrateController('migrate', Yii::$app);
        $migration->run('up', ['migrationPath' => $this->_sPathAlias . 'keys/', 'db' => $this->_sTConnection, 'interactive' => false]);
    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //-----------------------------------------FIX-AUTO-INCREMENT----------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    private function _APK()
    {
        switch ($this->_tdb->driverName) {
            case 'pgsql' :
                $this->_k_pgsql();
                break;
            case 'mysql' :
                $this->_k_mysql();
                break;
            default:
                throw new Exception("What DB is this??");
        }
    }

    private function _k_pgsql()
    {
        foreach ($this->_tdb->schema->getTableSchemas() as $table) {
            if (isset($table->primaryKey[0]) && !empty($table->sequenceName)) {
                echo "Set PK for " . $table->fullName . PHP_EOL;
                $sql = "SELECT setval('{$table->sequenceName}'::regclass,";
                $sql .= '(SELECT MAX("' . $table->primaryKey[0] . '") FROM "' . $table->fullName . '"))';
                $this->_exec($this->_tdb, $sql);
            }
        }
    }

    private function _k_mysql()
    {
        foreach ($this->_tdb->schema->getTableSchemas() as $table) {
            if (isset($table->primaryKey[0]) && !empty($table->sequenceName)) {
                echo "Set PK for " . $table->fullName . PHP_EOL;
                $sql = "ALTER TABLE {$table->fullName} AUTO_INCREMENT=(SELECT MAX({$table->primaryKey[0]}) FROM {$table->fullName})+1";
                echo $sql . PHP_EOL;
                echo $this->_tdb->createCommand($sql)->execute();
                echo PHP_EOL . PHP_EOL;
            }
        }
    }


    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //-------------------------------------------FIX-SEQUENCES-------------------------------------------//
    //---------------------------------------------------------------------------------------------------//
    //---------------------------------------------------------------------------------------------------//


    public function keys($sTable = null)
    {
        foreach ($this->_db->schema->getTableSchemas() as $table) {
            $this->_sequence($table);
        }


    }

    /**
     *
     * Function  _RecreateSequences
     * @param TableSchema $table
     */
    protected function _sequence($table)
    {
        if (isset($table->columns['id']) || isset($table->columns['Id'])) {
            $name = $table->fullName . '_id_seq';

            $command = "ALTER TABLE \"$table->schemaName\".\"$table->fullName\" ALTER COLUMN \"id\" DROP DEFAULT";
            $this->_exec($this->_db, $command);

            if ($table->sequenceName) {
                $command = "DROP SEQUENCE $table->sequenceName CASCADE";
                $this->_exec($this->_db, $command);
            }

            $command = "DROP SEQUENCE \"$name\" CASCADE";
            $this->_exec($this->_db, $command);

            $this->_exec($this->_db, "CREATE SEQUENCE \"$name\" INCREMENT 1 MINVALUE  1 START 1 CACHE 1");

            Az::trace("", "Create sequence command");
            $this->_exec($this->_db, "SELECT setval('\"$table->schemaName\".\"$name\"', 1, false)");

            $this->_exec($this->_db, "ALTER SEQUENCE \"$table->schemaName\".\"$name\" OWNED BY \"$table->schemaName\".\"$table->fullName\".\"id\"");

            $this->_exec($this->_db, "ALTER TABLE \"$table->schemaName\".\"$table->fullName\" ALTER COLUMN \"Id\" SET DEFAULT nextval('\"$name\"'::regclass)");

            $this->_exec($this->_db, "SELECT setval('\"$name\"'::regclass,(SELECT MAX(\"Id\") FROM \"$table->fullName\"))");

            $this->_exec($this->_db, "ALTER TABLE \"$table->schemaName\".\"$table->fullName\" ALTER COLUMN \"id\" SET DEFAULT nextval('\"$name\"'::regclass)");

            $this->_exec($this->_db, "SELECT setval('\"$name\"'::regclass,(SELECT MAX(\"id\") FROM \"$table->fullName\"))");


            $this->_log("Done: $table->fullName - $name", self::F_GREEN);
            echo PHP_EOL . PHP_EOL;
        }
    }


    /**
     * @param Connection $db
     * @param string $sql
     * @return bool
     */
    private function _exec($db, $sql): bool
    {
        try {
            echo PHP_EOL;
            $this->_log('Execute:', self::F_YELLOW, self::B_BLUE);
            $this->_log(' ' . $sql, self::F_YELLOW);
            $db->createCommand($sql)->execute();
            echo PHP_EOL;
            $this->_log('- OK -', self::F_WHITE, self::B_GREEN);
            echo PHP_EOL;
            echo self::CLI_LINE;
            echo PHP_EOL;
            return true;

        } catch (\Exception $e) {
            echo PHP_EOL;
            $this->_log('Error: ' . $e->getMessage(), self::F_GREEN, self::B_RED);
            echo PHP_EOL;
            echo self::CLI_LINE;
            return false;
        }
    }

    private function _log($str, $f, $b = "")
    {
        $str = str_replace(array("\n", "\r"), '', $str);
        echo "\e[{$f};{$b}m$str\e[0m";
    }

}
