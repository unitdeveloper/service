<?php

/**
 * Author:  Xolmat Ravshanov
 * Date: 31.05.2020
 *
 */
 
namespace zetsoft\service\search;

//require Root.'/ventest/manticore'

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ManticoreSearch extends ZFrame
{
    private $client;
    private $index;

    public $id;
    public $data = [];
    public $document;
    public $documents = [];
    public $query;

    public $indexName = 'test5';
    public $config = [
        'host' => '127.0.0.1',
        'port' => 9308
    ];

    public function init()
    {
        parent::init();
        $this->client = new \zetsoft\service\search\manticore\Client($this->config);
        $this->index = new \zetsoft\service\search\manticore\Index($this->client);
        $this->index->setName($this->indexName);

    }

    public function search()
    {
        return $this->index->search($this->query)->get();
    }


    public function getDocumentById()
    {
        return $this->index->getDocumentById($this->id);
    }

    public function addDocument()
    {
        $this->index->addDocument($this->document, $this->id);
    }

    public function addDocuments()
    {
        $this->index->addDocuments($this->documents, $this->id);
    }

    public function deleteDocument()
    {
        $this->index->deleteDocument($this->id);
    }

    public function deleteDocuments()
    {
        $this->index->deleteDocuments($this->query);
    }

    public function updateDocument()
    {
        $this->index->updateDocument($this->data, $this->id);
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

    public function create($fields, $settings = [])
    {
        $this->index->create($fields, $settings);
    }

    // delete index   need set index
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
        // add  or drop
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

