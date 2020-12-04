<?php

/**
 * Author:  Boburjon Komiljonov
 * Date: 05.06.2020
 */

namespace zetsoft\service\search;

use zetsoft\models\test\Test3;
use zetsoft\models\test\Test5;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Drivers\Mysqli\Connection;
use function Safe\msql_query;

class SphinxService
{

    #region Vars
    /**
     * @var $params
     * Parametres for Connection
     */

    public $params = [
        'ip' => '127.0.0.1',
        'port' => '9306'
    ];

    #region Config Data

    /**
     * @var $path
     * Path for SphinxSearch installation folder
     */
    public $path = "d:/Sphinx/sphinx311/";

    /**
     * @var $confData
     *
     */
    public $confData;
    //name of modal

    /**
     * @var $name
     * Name of the modal
     */
    public $name;

    /**
     * @var $Dname
     * Transformed name of the database table
     */
    public $Dname;

    /**
     * @var $ids
     * Contains ids that Sphinx Returned
     */
    public $ids = array();

    #endregion

    /**
     * @var $control
     * From here you control the Api library
     */
    public $control;

    /**
     * @var $search
     * What user wants to search for
     */
    public $search;

    /**
     * @var $classes
     * list of all model classes
     */
    public $classes = array();

    /**
     * @var $modal
     *  Modal itself
     */
    public $modal;
    public $modelName;
    /**
     * @var $objecttt
     * Where the object of modal is stored
     */
    public $objecttt;

    /**
     * @var $columns
     * list of all columns of the modal that needs to be indexed(Array Form)
     */
    public $columns = array();

    /**
     * @var $dbParams
     * The parametres for connection to database
     */
    public $dbParams = [
        'driver' => 'pgsql',
        'host' => '10.10.3.207',
        'port' => '5432',
        'user' => 'postgres',
        'pass' => 'serverpass1234',
        'db' => 'db31'
    ];

    /**
     * @var $dbColumns
     * List of all columns of the modal that needs to be indexed(String Form)
     */
    public $dbColumns;

    /**
     * @var $query
     * The query that is used in config file
     */
    public $query;

    /**
     * @var $mainP
     * Main Point From where queries will run
     */
    public $mainP;
    public $helper;

    /**
     * $var $result
     * Where the result is stored
     */
    public $result;

    /**
     * @var $vals
     * Where the operation values are stored
     */
    public $vals;

    #endregion

    #region Init

    /**
     * Runs when object is created
     * Give Value for $develop only for attaching indexes
     * In All the other cases let $develop to be null
     */
    public function __construct()
    {
        $conn = new Connection();
        $conn->setParams(array('host' => $this->params['ip'], 'port' => $this->params['port']));
        $this->mainP = (new SphinxQL($conn));
        $this->helper = (new \Foolz\SphinxQL\Helper($conn));
    }

    #endregion

    #region SphinxActions

    /**
     * Attaching Regular index to Real Time
     */
    public function attachIndex()
    {
        if (isset($this->name)) {
            if (!isset($this->Dname)) {
                $this->normalize();
            }
            $res = $this->mainP->Query("attach index " . $this->Dname . " to rtindex " . $this->Dname . "Rt");
            $res = $res->execute();
            var_dump($res);
        }
    }

    /**
     * Function that filters given data from unnecessary information
     */
    private function filter()
    {
        $this->chooseModal();
        $rrr = array();
        $rrr['id'] = $this->vals['id'];
        foreach ($this->vals as $key => $val) {
            if (in_array($key, $this->columns)) {
                $rrr[$key] = $val;
            }
        }
        return $this->vals = $rrr;
    }

    /**
     * Function that checks if index exists inside conf file
     * @return bool
     */
    private function check(): bool
    {
        if (!isset($this->Dname)) {
            $this->normalize();
        }
        $matches = array();
        $search = $this->path . "bin/sphinx.conf";
        $contents = file_get_contents($search);
        if (strpos($contents, strval($this->Dname)))
            $matches[] = $search;
        if (!empty($matches))
            return false;
        return true;
    }


    /**
     * Function that insert data into Real Time
     */
    public function insert()
    {
        if (isset($this->vals, $this->name)) {
            $this->filter();
            if (!isset($this->Dname)) {
                $this->normalize();
            }
            $insCommand = $this->mainP->insert()->into($this->Dname . 'Rt')->set($this->vals);
            $this->result = $insCommand->execute();
            if ($this->result) {
                echo 'Insertion successfully Accomplished!' . PHP_EOL;
            }
        } else {
            echo 'Insert Vals and Name first!' . PHP_EOL;
        }
    }


    /**
     * Function that replaces data in Real Time
     */
    public function update()
    {
        if (isset($this->vals, $this->name)) {
            $this->filter();
            if (!isset($this->Dname)) {
                $this->normalize();
            }
            $repCommand = $this->mainP->replace()->into($this->Dname . 'Rt')->set($this->vals);
            $this->result = $repCommand->execute();
            if ($this->result) {
                echo 'Replacement successfully Accomplished!' . PHP_EOL;
            }
        } else {
            echo 'Insert Vals and Name first!' . PHP_EOL;
        }
    }


    /**
     * Function that deletes data from Real time
     */
    public function delete()
    {
        if (isset($this->vals, $this->name)) {
            $this->filter();
            if (!isset($this->Dname)) {
                $this->normalize();
            }
            $delCommand = $this->mainP->delete()->from($this->Dname . 'Rt')->where('id', $this->vals['id']);
            $this->result = $delCommand->execute();
            if ($this->result) {
                echo 'Deleting successfully Accomplished!' . PHP_EOL;
            }
        } else {
            echo 'Insert Vals and Name first!' . PHP_EOL;
        }
    }

    /**
     * Function that performs searching using library
     */

    public function modelName($className)
    {
        return bname(strtolower($className));
    }

    /**
     * Function that checks if the following name has an Relal time index
     */
    private function isRt()
    {
        $matches = array();
        $search = $this->path . "bin/sphinx.conf";
        $contents = file_get_contents($search);
        if (strpos($contents, strval($this->Dname . 'Rt')))
            $matches[] = $search;
        if (!empty($matches))
            return true;
        return false;
    }

    /**
     * @return bool
     * Check if the given $this->name is namespace or not
     */
    private function isNamespace()
    {
        if (strstr($this->name, '/') || strstr($this->name, '\\')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Default search
     */
    public function search1()
    {
        if (isset($this->search, $this->name)) {
            if (!isset($this->modal)) {
                $this->chooseModal();
            }
            if (!isset($this->Dname)) {
                $this->normalize();
            }
            if ($this->isRt()) {
                $res = $this->mainP->select('*')
                    ->from($this->Dname . 'Rt')
                    ->match('*', $this->search);
                $res = $res->execute();
            } else {
                $res = $this->mainP->select('*')
                    ->from($this->Dname)
                    ->match('*', $this->search);
                $res = $res->execute();
            }
            foreach ($res as $re) {
                $this->ids[] = $re['id'];
            }
            return $this->result = $this->modal::findAll($this->ids);
        } else {
            echo 'Search Or Name is not Selected!' . PHP_EOL;
        }
    }


    /**
     * Function that Updated the index with given name
     */
    public function updateIndex()
    {
        if (isset($this->name)) {
            $this->normalize();
            exec($this->path . "bin/indexer --rotate " . $this->Dname . " --config " . $this->path . "bin/sphinx.conf", $o);
            print_r($o);
        } else {
            exec($this->path . "bin/indexer --rotate --all --config " . $this->path . "bin/sphinx.conf", $o);
            print_r($o);
        }
    }


    /**
     * Function that Updates the config file
     * Before calling this function make sure You have used chooseModal() first
     */
    public function newIndex()
    {
        if (isset($this->name)) {
            if (!isset($this->modal)) {
                $this->chooseModal();
                if (!isset($this->dbColumns)) {
                    echo 'No columns Selected!';
                    return 0;
                }
            }
            $this->normalize();
            if ($this->check()) {
                $fp = fopen($this->path . 'bin/sphinx.conf', 'a');//opens file in append mode
                fwrite($fp, "\n#start" . $this->Dname . "\n");
                fwrite($fp, "\nsource " . $this->Dname . "\n");
                fwrite($fp, "{\n");
                fwrite($fp, "type			= " . $this->dbParams['driver'] . "\n");
                fwrite($fp, "sql_host		= " . $this->dbParams['host'] . "\n");
                fwrite($fp, "sql_user		= " . $this->dbParams['user'] . "\n");
                fwrite($fp, "sql_pass		= " . $this->dbParams['pass'] . "\n");
                fwrite($fp, "sql_db			= " . $this->dbParams['db'] . "\n");
                fwrite($fp, "sql_port		= " . $this->dbParams['port'] . "\n");
                fwrite($fp, "sql_query		= " . $this->query . "\n");
                fwrite($fp, "}\n");
                fwrite($fp, "\nindex " . $this->Dname . "\n");
                fwrite($fp, "{\n");
                fwrite($fp, "source			= " . $this->Dname . "\n");
                fwrite($fp, "path			= " . $this->path . "data/" . $this->Dname . "\n");
                fwrite($fp, "}\n");
                fwrite($fp, "\n#end" . $this->Dname . "\n");
                fclose($fp);
                echo 'CONFIG FILE HAS BEEN UPDATED SUCCESSFULLY!' . PHP_EOL;
                $this->createIndex();
            } else {
                echo 'Index ' . $this->Dname . ' already exists!' . PHP_EOL;
            }
        } else {
            echo 'Please enter the Name of the Modal First and choose It!' . PHP_EOL;
        }
    }

    /**
     * Function that adds new rt to config
     */
    public function writeRt()
    {
        if (isset($this->name)) {
            if (!isset($this->modal)) {
                $this->chooseModal();
            }
            if (!isset($this->Dname)) {
                $this->normalize();
            }
            if (!$this->check()) {

                $fp = fopen($this->path . 'bin/sphinx.conf', 'a');//opens file in append mode
                fwrite($fp, "\n#start" . $this->Dname . "rt\n");
                fwrite($fp, "\nindex " . $this->Dname . "Rt\n");
                fwrite($fp, "{\n");
                fwrite($fp, "type			= rt\n");
                fwrite($fp, "path			= " . $this->path . "data/" . $this->Dname . "Rt\n");
                foreach ($this->columns as $col) {
                    fwrite($fp, "rt_field			= " . $col . "\n");
                }
                fwrite($fp, "}\n");
                fwrite($fp, "\n#end" . $this->Dname . "rt\n");
                fclose($fp);
                echo 'CONFIG FILE HAS BEEN UPDATED SUCCESSFULLY!' . PHP_EOL;
            } else {
                if (isset($this->dbColumns)) {
                    $this->newIndex();
                    $this->writeRt();
                } else {
                    echo 'Could not Find Give Model ' . $this->Dname . PHP_EOL;
                }
            }

        } else {
            echo 'Please enter the Name of the Modal First and choose It!' . PHP_EOL;
        }
    }


    /**
     * Function that creates index files from written config
     */
    private function createIndex()
    {
        $command = $this->path . "bin/indexer " . $this->Dname . " --config " . $this->path . "bin/sphinx.conf";
        echo 'Executing Command: ' . $command . PHP_EOL;
        exec($command, $o);
        print_r($o);
    }

    #endregion

    #region Actions

    /**
     * Function that Transforms ModelName into valid db table name
     */
    private function normalize()
    {
        if ($this->isNamespace()) {
            preg_match("/[^\/]+$/", $this->name, $matches);
            $this->name = $matches[0];
        }
        $arr = preg_split('/(?=[A-Z])/', $this->name);
        if (count($arr) !== 1) {
            $this->Dname = '';
            $max = count($arr);
            for ($i = 1; $i < $max; $i++) {
                if ($i < count($arr) - 1) {
                    $this->Dname .= $arr[$i] . '_';
                } else {
                    $this->Dname .= $arr[$i];
                }
            }
        } else {
            $this->Dname = $this->name;
        }
        $this->Dname = strtolower($this->Dname);
        //now $this->Dname will contain table name in database
        //Transformation: from User to user
    }


    /**
     * Function that lists through all modals and chooses right one
     * Sets modal
     * Sets columns
     * Sets dbColumns
     * Sets Query
     */
    private function chooseModal()
    {
        $this->columns = array();
        if (isset($this->name)) {
            $this->classes = Az::$app->smart->migra->scan();
//            $this->classes = [
//                User::class
//            ];
            $givenName = strtolower($this->name);
            foreach ($this->classes as $class) {
                $name = strtolower(bname($class));
                if ($name === $givenName) {
                    $object = $class::find()->all();
                    $this->modal = new $class();
                    //The $this->modal is chosen
                    if ($this->check()) {
                        foreach ($object[0]->columns as $key => $column) {
                            if ($column->indexSearch) {
                                $this->columns[] = $key;
                            }
                        }
                    }

                    //$this->Columns will contain an array of all needed columns
                }
            }
            if ($this->check()) {
                $this->selectColumns();
                //now $this->dbColumns will contain stuff like (name, surname, ...) in one string
                $this->buildQuery();
                //now the query is ready to use
            }
        } else {
            echo 'Name Is not Selected' . PHP_EOL;
        }
    }


    /**
     * Function that build query using dbColumns and Dname
     */


    private function buildQuery()
    {
        $this->query = '';
        if (!isset($this->Dname)) {
            $this->normalize();
        }
        if (isset($this->dbColumns)) {
            $this->query = 'SELECT id, ' . $this->dbColumns . ' FROM ' . $this->Dname;
        } else {
            echo '$this->dbColumns is not set Yet!' . PHP_EOL;
        }
    }


    /**
     * Function that transforms db columns from array to string format
     */

    private function selectColumns()
    {
        $this->dbColumns = '';
        if (isset($this->columns)) {
            $max = count($this->columns) - 1;
            foreach ($this->columns as $k => $s) {
                if ($k < $max) {
                    $this->dbColumns .= $s . ', ';
                } else {
                    $this->dbColumns .= $s;
                }
            }
            //Transformation: from ['name', 'email', ... ] to name, email, ...
        } else {
            echo '$this->Columns is not set Yet!' . PHP_EOL;
        }
    }
    #endregion

    #region TestCases

    //If you are using this autside of the service, consider changing the configurations
    //Those are mainly members of this class
    //IF YOU CHANGE THE SPHINX LOCATION CONSIDER CHANGING PATH LOCAL VARIABLE THERE!
    public function testNewIndex()
    {
        //in order to update regular indexes use function (updateIndex)
        //function (update) MUST be used with Real Times only, that is difference
        $this->name = 'test5';
        $this->newIndex();
    }

    public function testWriteRt()
    {
        //Real time gives you ability to update delete insert functions
        $this->name = 'test5';
        $this->writeRt();
    }

    //Note that, functions below all must have valid Real time Index to be able to work
    public function testInsert()
    {
        //Note this only works with Real Times
        $this->name = 'test5';
        $this->vals = ['id' => 123123, 'first_name' => 'Somebody'];
        $this->insert();
    }

    public function testUpdate()
    {
        //Note this only works with Real Times
        $this->name = 'test5';
        //Note, if you give id that does not exist this will create new record instead of updating
        $this->vals = ['id' => 123123, 'first_name' => 'Somebody'];
        $this->update();
    }

    public function testDelete()
    {
        //Note this only works with Real Times
        $this->name = 'test5';
        //does not matter what you give after id, id is the most important, and the function is gonna take only id anyway
        $this->vals = ['id' => 123123, 'first_name' => 'Somebody'];
        $this->delete();
    }

    public function testSearch()
    {
        $this->name = 'test5';
        $this->search = 'vera';
        $this->search1();
        //As simple as that
    }

    #endregion
}



