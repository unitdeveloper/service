<?php

/**
 * Author:  Xolmat Ravshanov
 * Date: 31.05.2020
 */

namespace zetsoft\service\search;

use zetsoft\models\shop\ShopElement;
use zetsoft\service\smart\Model;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


class Manticore extends ZFrame
{

    private $client;
    private $index;
    public $id;
    public $data = [];
    public $document;
    public $options;
    public $documents = [];
    public $query;
    public $model;
    public $fields = [];

    public $indexName;
    public $config = [
        'host' => '127.0.0.1',
        'port' => 9308
    ];

    public function init()
    {
        parent::init();
        $this->client = new \Manticoresearch\Client($this->config);
        $this->index = new \Manticoresearch\Index($this->client);
   

    }

    public function run()
    {
        $document = [];
        $documents = [];
        $columns = [];
        //$classes = Az::$app->smart->migra->scan();
        $classes = [
            ShopElement::class,
        ];
        foreach ($classes as $class){
            $object1 = new $class();
            if ($object1->configs->indexSearch) {
                /** @var Models $object */
                $object = $class::find()->all();

                $this->indexName = strtolower(bname($class));
                $this->index->setName($this->indexName);

                if(!empty($object)){
                    foreach ($object as $model) {
                        foreach ($model->columns as $key => $column) {
                            if ($column->indexSearch) {
                                $columns[$key] = ['type' => 'text'];
                                $document[$key] = $model->$key;
                            }
                        }
                    }
                    if(!file_exists('D:/manticore/data/'.$this->indexName.'/'.$this->indexName.'.ram')) {
                        $this->create($columns);
                    }
                }
                vd($document);
                $this->updateDocument($document, $model->id);
                //$this->addDocument($document, $model->id);
                /*if(!empty($documents))
                    $this->addDocuments($documents);*/
            }else {
                continue;
            }
        }

    }

    public function search()
    {
        return $this->index->search($this->query)->get();
    }

    public function match()
    {
        $q = new \Manticoresearch\Query\BoolQuery();

        $q->must(new \Manticoresearch\Query\Match(['query' => $this->query], '*'));

        return $this->index->search($q)->get();
    }

    public function sql()
    {
        $params = [
            'body' => [
                'query' => "SELECT * FROM $this->indexName where MATCH('$this->query')"
            ]
        ];
        return $this->client->sql($params);
    }

    public function getDocumentById()
    {
        return $this->index->getDocumentById($this->id);
    }

    public function addDocument($document, $id)
    {
        $this->index->addDocument($document, $id);
    }

    public function addDocuments($documents)
    {
      $this->index->addDocuments($documents);
    }

    public function deleteDocument()
    {
        $this->index->deleteDocument($this->id);
    }

    public function deleteDocuments()
    {
        $this->index->deleteDocuments($this->query);
    }

    public function updateDocument($document, $id)
    {
        $this->index->updateDocument($document, $id);
    }

    public function updateDocuments()
    {
        $this->index->updateDocuments($this->data, $this->query);
    }

    public function replaceDocument()
    {
        $this->index->replaceDocument($this->data, $this->id);
    }

    public function replaceDocuments()
    {
        $this->index->replaceDocuments($this->documents);
    }

    // create index
    public function create($fields)
    {
        $this->index->create($fields);
    }


    // delete index need set index

    /**
     *
     * Function  drop
     * @param  true
     */
    public function drop()
    {
        $this->index->drop();
    }

    // status
    public function describe()
    {
        $this->index->describe();
    }

    public function status()
    {
        return $this->index->status();
    }

    public function truncate()
    {
        $this->index->truncate();
    }

    public function optimize()
    {
        $this->index->optimize();
    }

    public function flush()
    {
        $this->index->flush();
    }

    public function flushramchunk()
    {
        $this->index->flushramchunk();
    }

    public function alter()
    {
        // add   or  drop
        // column name
        // type
        $this->index->alter($this->operation, $this->name, $this->type);
    }


    public function keywords()
    {
        $this->index->keywords($this->query, $this->options);
    }

    public function suggest()
    {
        $this->index->suggest($this->query, $this->options);
    }

    public function getClient()
    {
        $this->index->getClient();
    }

    public function getName()
    {
        return $this->index->getName();
    }

    public function setName()
    {
        return $this->index->setName();
    }

    public function getConnections()
    {
        return $this->client->getConnections();
    }

}
