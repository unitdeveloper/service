<?php


/**
 * Author:  Boburjon Komiljonov
 * Date: 01.06.2020
 * Refactored by : Xolmat Ravshanov
 */

namespace zetsoft\service\search;

use DirectoryIterator;
use TeamTNT\TNTSearch\TNTSearch;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use zetsoft\models\ALL\PageApp;
use zetsoft\models\ALL\CoreCatalog;
use zetsoft\models\ALL\CoreCategory;
use zetsoft\models\ALL\CoreElement;
use zetsoft\models\ALL\CoreProduct;

use zetsoft\service\smart\Model;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


class TntSearchService extends Zframe
{
    #region Vars
    private $conParams = [
        'driver' => 'pgsql',
        'host' => '10.10.3.207',
        'database' => 'db_mplace_01',
        'user' => 'postgres',
        'pass' => 'serverpass1234',
        'storage' => '\\\10.10.3.171\asrorz\process\Search\tnt\\'
    ];
    public $control;
    public $subControl;
    public $classifier;
    public $statement;
    public $theme;
    public $fuzzy_distance;
    public $max_expansions;
    public $fuzzy_prefix_length;
    public $enableSearchById = true;
    public $name;
    public $classes;
    public $columns = [];
    public $dbColums;
    public $modal;
    public $modalName;
    public $query;
    public $lang;
    public $primary_key = 'id';
    public $search;
    public $notExactSearch = true;
    public $maxNum = 1000;
    public $result;
    public $res;
    public $html;
    public $someRes;
    public $shouldHave;
    public $shouldNotHave;
    public $pathForClas;
    public $nameForClas;
    public $prediction;
    public $data;
    public $id;
    public $files = [];

    #endregion

    #region Init

    /**
     * Loops through all index files and returns an array of nameOn
     */
    private function getFileNames()
    {
        foreach (new DirectoryIterator($this->conParams['storage']) as $file) {
            if ($file->isFile()) {
                $this->files[] = $file->getFilename();
            }
        }
    }

    public function init()
    {
        parent::init();
        $this->conParams['database'] = $this->bootEnv('dbName');
        $this->control = new TNTSearch;
        $this->pathForClas = $this->conParams['storage'] . '\classification';
        $this->control->loadConfig([
            'driver' => $this->conParams['driver'],
            'host' => $this->conParams['host'],
            'database' => $this->conParams['database'],
            'username' => $this->conParams['user'],
            'password' => $this->conParams['pass'],
            'storage' => $this->conParams['storage'],
            'stemmer' => \TeamTNT\TNTSearch\Stemmer\PorterStemmer::class//optional
        ]);
        $this->classifier = new TNTClassifier();
    }

    #endregion

    #region Actions


    /**
     * Creates indexes
     */
    public function createIndexService()
    {
        if (isset($this->name)) {
            $this->chooseModal();
            $this->normalize();

            $this->subControl = $this->control->createIndex($this->name . '.index');
        
            if ($this->enableSearchById)
                $this->subControl->includePrimaryKey();

            // $this->getPrimaryKey();
            // echo $this->primary_key . PHP_EOL;

            if (isset($this->primary_key))
                $this->subControl->setPrimaryKey($this->primary_key);

            if (isset($this->lang))
                $this->subControl->setLanguage($this->lang);

            $this->selectColumns();
            $this->selectQuery();
            if (isset($this->query)) {
                echo $this->query . PHP_EOL;
                $this->subControl->query($this->query .';');
                $this->subControl->run();
            } else {
                echo 'Wrong Name';
            }
        } else {
            echo 'Insert The name first ';
        }

    }


    /**
     * Select the primary key
     * Used in creating indexes
     */
    private function getPrimaryKey()
    {
        vdd(Az::$app->db->schema->getTableSchema($this->name)->getColumnNames());
        $tables = Az::$app->db->schema->getTableSchema($this->name)->getColumnNames();
        if (!in_array('id', $tables)) {
            $this->primary_key = $tables[0];
        }
    }


    /**
     * Chooses Modal and Columns
     * Used in Creating indexes
     */
    public function chooseModal()
    {
        $this->classes = Az::$app->smart->migra->scan();
        $givenName = strtolower(bname($this->name));
        foreach ($this->classes as $class) {
            $name = strtolower(bname($class));
            if ($name === $givenName) {
                $object = $class::find()->all();
                $this->modal = new $class();

                if($this->modal->configs->indexSearch) {
                    foreach ($this->modal->columns as $key => $column) {
                        if ($column->indexSearch) {
                            $this->columns[] = $key;
                        }
                    }
                }

            }
        }
    }



    /**
     * Selects neede modal only
     */
    public function chooseModalOnly()
    {
        $this->classes = Az::$app->smart->migra->scan();
        $givenName = strtolower($this->name);
        foreach ($this->classes as $class) {
            $name = strtolower(bname($class));
            if ($name === $givenName) {
                $this->modal = new $class();
            }
        }
    }


    public function run()
    {
        $classes = Az::$app->smart->migra->scan();
        foreach ($classes as $class) {
            $object1 = new $class();
            if ($object1->configs->indexSearch) {
                if (!file_exists($this->conParams['storage'] . '/' . $this->name . '.index')) {
                    $this->name = $object1->className;
                    $this->createIndexService();
                } else {
                    return false;
                }
            }
        }

    }

    /**
     * Makes the $this->name variable
     * Transform it to usable case
     */
    private function normalize()
    {
    
        $arr = preg_split('/(?=[A-Z])/', $this->name);
        if (count($arr) !== 1) {
            $this->name = '';
            $max = count($arr);
            for ($i = 1; $i < $max; $i++) {
                if ($i < count($arr) - 1) {
                    $this->name .= $arr[$i] . '_';
                } else {
                    $this->name .= $arr[$i];
                }
            }
        }
        
        $this->name = strtolower($this->name);
    }

    /**
     * Class and executes boolean search
     */
    public function Boolsearch()
    {
        if (isset($this->name)) {
            $this->chooseModal();
            $this->normalize();
            $this->booleanSearch();
            $this->res = $this->result['ids'];
            if (empty($this->res)) {
                echo 'No Results Found';
            } else {
                $this->result = $this->modal::findBySql("SELECT * FROM " . $this->name . " WHERE id IN (" . implode(",", array_map('intval', $this->res)) . ")")->all();
                //var_dump($this->res);
                //require __DIR__ . '\TntSearchOutput\SearchTable.php';
            }
        }

    }

    /**
     * Generates query string to get data from database
     */
    private function selectQuery()
    {
        if(!empty($this->dbColums))
        $this->query = 'SELECT id, ' . $this->dbColums . ' FROM ' .$this->name;
          else
              $this->query = 'SELECT id ' . $this->dbColums . ' FROM ' .$this->name;
              
        $this->dbColums = '';
    }


    /**
     * Its is a regular search, should be used by default
     */
    public function regularSearch1()
    {
        $this->chooseModalOnly();
        $this->normalize();
        $this->selectIndexService();
        if ($this->notExactSearch) {
            $this->control->fuzziness = true;
            if (isset($this->fuzzy_distance)) {
                $this->control->fuzzy_distance = $this->fuzzy_distance;
            }
            if (isset($this->fuzzy_prefix_length)) {
                $this->control->fuzzy_prefix_length = $this->fuzzy_prefix_length;
            }
            if (isset($this->max_expansions)) {
                $this->control->fuzzy_max_expansions = $this->max_expansions;
            }
        }
        if (isset($this->search)) {
            $this->result = $this->control->search($this->search, $this->maxNum);
            $this->res = [];
            foreach ($this->result['ids'] as $id) {
                $this->res[] = $id;
            }

            // $this->result = $this->modal::findAll($this->res);
            return $this->result = $this->modal::find()->where([
                'id' => $this->res
            ]);
        } else {
            echo 'Please Insert Search Statement First';
        }
    }

    /**
     * Its is a regular search, should be used by default
     */
    public function regularSearch()
    {
        $ids = [];
        $this->chooseModalOnly();
        $this->normalize();
        $this->selectIndexService();
        if ($this->notExactSearch) {
            $this->control->fuzziness = true;
            if (isset($this->fuzzy_distance))
                $this->control->fuzzy_distance = $this->fuzzy_distance;

            if (isset($this->fuzzy_prefix_length))
                $this->control->fuzzy_prefix_length = $this->fuzzy_prefix_length;

            if (isset($this->max_expansions))
                $this->control->fuzzy_max_expansions = $this->max_expansions;

        }

        if (isset($this->search)) {
            $this->result = $this->control->search($this->search, $this->maxNum);

            foreach ($this->result['ids'] as $id)
                $ids[] = $id;


       
               return $ids;

        } else {
            return $ids;
        }
    }


    /**
     * Saves classification so that to be able to load it later
     */
    public function saveClassification()
    {
        if (isset($this->pathForClas)) {
            $this->classifier->save($this->pathForClas . $this->nameForClas . '.cls');
        } else {
            echo 'Set The Path First';
        }
    }

    /**
     * Loads classification files
     */
    public function loadClassification()
    {
        if (isset($this->pathForClas)) {
            $this->classifier->load($this->pathForClas . $this->nameForClas . '.cls');
        } else {
            echo 'Please Set The Path First';
        }
    }


    /**
     * Ads one record to index file
     */
    public function addDocToIndex()
    {
        if (isset($this->data)) {
            $this->filter();
            $this->normalize();
            $this->selectIndexService();
            $index = $this->control->getIndex();
            if ($this->enableSearchById) {
                $index->includePrimaryKey();
            }
            $index->insert($this->data);
            echo 'Inserted' . PHP_EOL;
        } else {
            echo 'Please Insert Data To Add First';
        }

    }


    /**
     * Filters data and makes it viable to use
     */
    private function filter()
    {
        $this->chooseModal();
        $rrr = array();
        $rrr['id'] = $this->data['id'];
        $this->id = $this->data['id'] ?? array_shift($this->data);
        foreach ($this->data as $key => $val) {
            if (in_array($key, $this->columns)) {
                $rrr[$key] = $val;
            }
        }
        return $this->data = $rrr;
    }

    /**
     * Updates one document at index file
     */
    public function updateDocInIndex()
    {
        if (isset($this->data)) {
            $this->filter();
            $this->normalize();
            $this->selectIndexService();
            $index = $this->control->getIndex();
            if ($this->enableSearchById) {
                $index->includePrimaryKey();
            }
            $index->update($this->id, $this->data);
            echo 'Updated' . PHP_EOL;
        } else {
            echo 'Please Insert Data To Update First';
        }

    }


    /**
     * Deletes one record from indexfile
     * Note, not from database
     */
    public function deleteDocFromIndex()
    {
        if (isset($this->data)) {
            $this->filter();
            $this->normalize();
            $this->selectIndexService();
            $index = $this->control->getIndex();
            if ($this->enableSearchById) {
                $index->includePrimaryKey();
            }
            $index->delete($this->id);
            echo 'Deleted' . PHP_EOL;
        } else {
            echo 'Please Insert ID To Delete';
        }

    }


    /**
     * Deletes the whole index
     */
    public function deleteIndex()
    {
        if (isset($this->name)) {
            $this->normalize();
            $file = $this->conParams['storage'] . '/' . $this->name . '.index';
            if (file_exists($file)) {
                unlink($file);
                echo 'File: ' . $this->name . '.index is deleted!';
            } else {
                echo 'File: ' . $this->name . '.index does not exist!';
            }
        }
    }


    /**
     * Sets the statement and the topic
     */
    public function setStatementAndTheme()
    {
        if (isset($this->statement) && isset($this->theme)) {
            $this->classifier->learn($this->statement, $this->theme);
        } else {
            echo 'Please Give The Statement And Topic For Classification!';
        }
    }


    /**
     * Selects needed columns
     */
    private function selectColumns1()
    {
        $max = count($this->columns) - 1;
        foreach ($this->columns as $k => $s) {
            if ($k < $max)
                $this->dbColums .= $s . ', ';
            else
                $this->dbColums .= $s;
        }
        foreach ($this->columns as $i => $value)
            unset($this->columns[$i]);


    }
    private function selectColumns()
    {
        foreach ($this->columns as  $s){
            if(end($this->columns) !== $s)
                $this->dbColums .= $s . ', ';
            else
                $this->dbColums .= $s;
        }

        foreach ($this->columns as $i => $value)
            unset($this->columns[$i]);


    }

    /**
     * Used with $this->shouldHave and $this->shouldNotHave
     */
    private function booleanSearch()
    {
        $this->selectIndexService();
        if (isset($this->shouldHave) && !isset($this->shouldNotHave)) {
            $this->result = $this->control->searchBoolean($this->shouldHave);
        }
        if (!isset($this->shouldHave) && isset($this->shouldNotHave)) {
            $this->makeSearchable();
            var_dump($this->shouldNotHave);
            $this->result = $this->control->searchBoolean($this->shouldNotHave);
        }
        if (isset($this->shouldHave, $this->shouldNotHave)) {
            $this->makeSearchable();
            $this->result = $this->control->searchBoolean($this->shouldHave . " " . $this->shouldNotHave);
        }
    }

    /**
     * Makes $this->shouldNotHave viable for use
     */
    private function makeSearchable()
    {
        $arr = explode(' ', trim($this->shouldNotHave));
        $this->shouldNotHave = '';
        $num = count($arr) - 1;
        foreach ($arr as $key => $val) {
            if ($key < $num) {
                $this->shouldNotHave .= '-' . $val . ' ';
            } else {
                $this->shouldNotHave .= '-' . $val;
            }
        }
    }


    /**
     * Users Guess
     */
    public function guess()
    {
        $this->result = $this->classifier->predict($this->prediction);
        echo $this->result['label'];
    }

    /**
     * Select index that is going to be used
     */
    private function selectIndexService()
    {
        try {
            if (isset($this->name)) {
                $this->control->selectIndex($this->name . ".index");
            } else {
                echo 'Enter Name For The Index First';
            }
        } catch (\TeamTNT\TNTSearch\Exceptions\IndexNotFoundException $e) {
            echo 'Error With Selecting Index';
        }
    }

#endregion

#region TestCaseses

    public function testCreateIndexService()
    {
        $this->name = 'CoreProduct';
        $this->createIndexService();
    }

    public function testDeleteIndex()
    {
        $this->name = 'CoreProduct';
        $this->deleteIndex();
    }

//Shoudl be used inside modal in aftersave section
    public function testUpdateDocInIndex()
    {
        //Updates the index files
        $this->name = 'CoreProduct';
        //Whatever that is being saved using modal should be saved using theese functions also
        $this->data = ['id' => 28, 'name' => 'Qalam'];
        $this->updateDocInIndex();
    }

//Shoudl be used inside modal in aftersave section
    public function testAddDocToIndex()
    {
        $this->name = 'CoreProduct';
        $this->data = ['id' => 455555, 'name' => 'Menumenu'];
        $this->addDocToIndex();
    }

//Shoudl be used inside modal in afterdelete section
    public function testDeleteDocFromIndex()
    {
        $this->name = 'CoreProduct';
        //Mainly you will need only id, but if the primary key is different than 'id'
        //Like core_id or stuff
        //Make sure you put the primary ked first
        //$this->data = ['core_id'=>2342] would work as well
        $this->data = ['id' => 28, 'name' => 'Menumenu'];
        $this->deleteDocFromIndex();
    }

    public function testRegularSearch()
    {
        //This is regular search, but it lets the user make some for mistakes
        $this->name = 'CoreProduct';
        //Whatever you wanna search for
        $this->search = 'Samsung';
        $this->regularSearch();
    }

    public function testBoolSearch()
    {
        $this->name = 'CoreProduct';
        //Whatever should be in search result
        $this->shouldHave = 'Samsung';
        //Whatever should not be in search result
        $this->shouldNotHave = 'SmthElse';
        //Basically calls boolean search
        $this->Boolsearch();
    }


#endregion
}
