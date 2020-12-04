<?php

/**
 * Author:  Xolmat Ravshanov
 * Date: 31.05.2020
 */

namespace zetsoft\service\search;

use Elasticsearch\ClientBuilder;
use zetsoft\service\smart\Model;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


class ElasticSearch extends ZFrame
{

    private $hosts = [
        '192.168.1.1:9200',         // IP + Port
        '192.168.1.2',              // Just IP
        'mydomain.server.com:9201', // Domain + Port
        'mydomain2.server.com',     // Just Domain
        'https://localhost',        // SSL to localhost
        'https://192.168.1.3:9200'  // SSL to IP + Port
    ];
    private $handelers = [
        'defaultHandler'
    ];
    private $indexs = [
        'index' => 'index',
        'zetsoft' => 'zetsoft'
    ];
    private $connectionPool = [
        'default' => '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool'

    ];
    private $serializer = [
        'default' => '\Elasticsearch\Serializers\SmartSerializer'

    ];
    private $selector = [
        'default' => '\Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector'
    ];
    private $client;
    public $body = [];
    public $fields = [];
    public $index;
    public $model;

    public function init()
    {
        parent::init();
        $this->indexName();
        $this->client = ClientBuilder::create()->build();

    }

    #region config
    private function config()
    {
        $response = ClientBuilder::create()
            ->setHosts($this->hosts)
            ->build();
        return $response;

    }

    /**
     *
     * Function  index
     */
    public function run()
    {
        $classes = Az::$app->smart->migra->scan();
        foreach ($classes as $class) {
            $object1 = new $class();
            if ($object1->configs->indexSearch) {
                $object = $class::find()->all();
                $this->model = $class;

                if (!$this->indexExists())
                    $this->createIndex();

                $arr = [];
                foreach ($object as $model) {
                    foreach ($model->columns as $key => $column) {
                        if ($column->indexSearch)
                            $arr[$key] = $model->$key;
                    }
                    $this->body = $arr;
                    $this->createdoc($model->id);
                }
            } else {
                continue;
            }
        }
    }


    public function handler()
    {
        // default        synchronous
        // multiHandler  asynchronous

        // $defaultHandler = ClientBuilder::defaultHandler();
        //$singleHandler  = ClientBuilder::singleHandler();
        $multiHandler = ClientBuilder::multiHandler();
        // $customHandler  = new MyCustomHandler();

        $response = $this->client = ClientBuilder::create()
            ->setHandler($multiHandler)
            ->build();
        return $response;
    }

    private function connectionPool()
    {
        $response = ClientBuilder::create()
            ->setConnectionPool($this->connectionPool['default'])
            ->build();
        return $response;
    }

    private function selector()
    {
        $response = ClientBuilder::create()
            ->setSelector($this->selector['default'])
            ->build();
        return $response;
    }

    private function serializer()
    {
        $response = ClientBuilder::create()
            ->setSerializer($this->serializer['default'])
            ->build();
        return $response;
    }
    #end region

    #region doc
    public function createDoc($id)
    {

        $params = [
            'index' => $this->index,
            'type' => '_doc',
            'id' => $id,
            'body' => $this->body

        ];
        $response = $this->client->index($params);
        return $response;
    }

    public function getDoc($id)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'client' => [
                'ignore' => [404, 400],
                'verbose' => false,
                'timeout' => 10,
                'connect_timeout' => 10
            ]
        ];
        $response = $this->client->get($params);
        return $response;
    }

    public function getAllDoc()
    {
        $ids = [];
        $params = [
            'index' => $this->index,
            'size' => 100,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];
        $response = $this->client->search($params);

        foreach ($response['hits']['hits'] as $src)
            $ids[] = $src['_id'];


        $res = $this->model::find()->where([
            'id' => $ids
        ]);
        return $res;
    }

    public function deleteDoc($id)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
        ];
        $response = $this->client->delete($params);
        return $response;
    }

    // Update or Insert
    public function upsert($id)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'body' => [
                'script' => [
                    'source' => 'ctx._source.counter += params.count',
                    'params' => [
                        'count' => 4
                    ],
                ],
                'upsert' => [
                    'counter' => 1
                ],
            ]
        ];

        $response = $this->client->update($params);

    }

    // update with condition
    public function partialUpdate($id)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'body' => [
                'doc' => [
                    'new_field' => 'abc'
                ]
            ]
        ];
        $response = $this->client->update($params);
        return $response;
    }

    // update with condition
    public function scriptUpdate($id)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'body' => [
                'script' => 'ctx._source.counter += count',
                'params' => [
                    'count' => 4
                ]
            ]
        ];

        $response = $this->client->update($params);
        return $response;
    }

    #end region

    public function getModelAttiributes()
    {
        $obj = new $this->model();
        $searchAttributes = [];
        foreach ($obj->columns as $key => $column) {
            if ($column->indexSearch) {
                $searchAttributes[] = $key;
            }
        }
        return $searchAttributes;
    }

    // conver to model name index name
    public function indexName()
    {
        $this->index = strtolower(bname($this->model));
    }

    #region SEARCH
    public function search($search)
    {
        $ids = [];
        if (!empty($this->querySearch($search))) {
            foreach ($this->querySearch($search) as $id)
                $ids[] = $id;
        }

        if (!empty($this->prefixSearch($search))) {
            foreach ($this->prefixSearch($search) as $id)
                $ids[] = $id;
        }

        if (!empty($this->fuzzy($search))) {
            foreach ($this->fuzzy($search) as $id)
                $ids[] = $id;
        }

        if (!empty($ids)) {
            $ids = array_unique($ids);
            $res = $this->model::find()->where([
                'id' => $ids
            ]);
            return $res;
        } else {
            echo 'NOT FOUND';
            return new $this->model();
            // return $this->getAllDoc();
        }
    }

    public function querySearch($search)
    {
        $ids = [];
        $params = [
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => $search
                    ]
                ],
            ]
        ];
        $response = $this->client->search($params);
        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];
            }
            echo 'found';
            return $ids;
        }
        return $ids;
    }

    public function prefixSearch($search)
    {
        $ids = [];
        $params = [
            'body' => [
                'query' => [
                    'multi_match' => [
                        'fields' => $this->getModelAttiributes(),
                        'query' => trim($search),
                        'type' => 'phrase_prefix'
                    ],
                ],
            ]
        ];
        $response = $this->client->search($params);

        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];
            }
            echo 'found';
            return $ids;

        }
        return $ids;
    }

    public function allOfSearch($search)
    {
        $ids = [];
        $params = [
            'body' => [
                'query' => [
                    'intervals' => [
                        'email' => [
                            'all_of' => [
                                'intervals' => [
                                    'match' => [
                                        'query' => trim($search)
                                    ]

                                ]
                            ],

                        ]
                    ]
                ],

            ]
        ];

        //_field_names
        $response = $this->client->search($params);
        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];
            }
            echo 'found';
            return $ids;
        }
        return $ids;
    }

    public function fuzzy($search)
    {
        $ids = [];

        $params = [
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => trim($search),
                        'fields' => $this->getModelAttiributes(),
                        'fuzziness' => "AUTO"
                    ]

                ]
            ]
        ];
        //_field_names
        $response = $this->client->search($params);
        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];
            }
            echo 'found';
            return $ids;
        } else {
            return $ids;
        }


    }

    #region SEARCH
    public function fuzzy1($search)
    {
        $ids = [];
        $params = [
            'body' => [
                'query' => [
                    'fuzzy' => [
                        'first_name' => [
                            "value" => $search,
                            "fuzziness" => "AUTO",
                            "transpositions" => true,
                        ]
                    ]
                ],
            ]
        ];
        $response = $this->client->search($params);
        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];
            }

            $res = $this->model::find()->where([
                'id' => $ids
            ]);

            echo 'found';
            return $res;

        } else {
            echo 'NOT FOUND';
            return $this->getAllDoc();
        }


    }


    public function searchCurrent($search)
    {
        $ids = [];
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $search
                    ],
                    'should' => [
                        'match' => $search
                    ],
                    'wildcard' => [
                        'match' => $search
                    ]

                ],


            ]];

        $response = $this->client->search($params);
        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];

            }
            $res = $src['_source']['className']::find()->where([
                'id' => $ids
            ]);
            return $res;
        } else {
            return $this->getAllDoc();
        }


    }

    public function searchByIndex($search)
    {
        $params = [
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => $search
                    ]
                ],
                /* 'query' => [
                     'match_all' => new \stdClass()
                 ]*/
            ]
        ];
        $response = $this->client->search($params);
        return $response;
    }

    public function highlightSearch($search)
    {
        $params['body'] = array(
            'query' => array(
                'multi_match' => array(
                    'query' => $search,
                )
            ),
            'highlight' => array(
                'fields' => array(
                    'content' => new \stdClass()
                )
            )
        );
        return $this->client->search($params);

    }

    public function sortSearch()
    {
        $params['body'] = array(
            'query' => array(
                'match' => array(
                    'content' => 'quick brown fox'
                )
            ),
            'sort' => array(
                array('time' => array('order' => 'desc')),
                array('popularity' => array('order' => 'desc'))
            )
        );
        $results = $this->client->search($params);
        return $results;

    }

    public function jsonSearch()
    {
        $json = '{
                    "query" : {
                        "match" : {
                            "tags" : "product"
                        }
                    }
                }';

        $params = [
            'index' => $this->index,
            'body' => $json
        ];
        $results = $this->client->search($params);
        return $results;
    }


    public function boolSearch($search)
    {
        $ids = [];
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['_all' => $search]],
                            ['match' => ['_all' => $search]],
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->client->search($params);
        if (!empty($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $src) {
                $ids[] = $src['_id'];
            }
            $res = $this->model::find()->where([
                'id' => $ids
            ]);

            echo 'found';
            return $res;

        } else {
            echo 'NOT FOUND';
            return $this->getAllDoc();
        }


    }

    public function bool1Search()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'term' => ['first_name' => 'first_name']
                        ],
                        'should' => [
                            'match' => ['last_name' => 'last_name']
                        ]
                    ]
                ]
            ]
        ];


        $results = $this->client->search($params);
        return $results;
    }

    public function scrolling()
    {
        $params = [
            'scroll' => '30s',          // how long between scroll requests. should be small!
            'size' => 100,             // how many results *per shard* you want back
            'index' => $this->index,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

        $response = $this->client->search($params);

        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {


            $scroll_id = $response['_scroll_id'];


            $response = $this->client->scroll([
                    'scroll_id' => $scroll_id,
                    'scroll' => '30s'
                ]
            );
        }
    }

    #end region
    public function createIndex()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ]
            ]
        ];

        $response = $this->client->indices()->create($params);
        return $response;
    }

    public function future()
    {
        $futures = [];

        for ($i = 0; $i < 1000; $i++) {
            $params = [
                'index' => $this->index,
                'id' => $i,
                'client' => [
                    'future' => 'lazy'
                ]
            ];

            $futures[] = $this->client->get($params);
        }


        foreach ($futures as $future) {
            echo $future['_source'];
        }
        $futures[999]->wait();
    }

    // async
    public function changeBatchSize()
    {
        $handlerParams = [
            'max_handles' => 500
        ];
        $defaultHandler = ClientBuilder::defaultHandler($handlerParams);
        $this->client = ClientBuilder::create()
            ->setHandler($defaultHandler)
            ->build();
        $futures = [];
        for ($i = 0; $i < 499; $i++) {
            $params = [
                'index' => 'index',
                'id' => $i,
                'client' => [
                    'future' => 'lazy'
                ]
            ];
            $futures[] = $this->client->get($params);     //queue up the request
        }

        $body = $futures[499]['body'];
    }

    public function heterogeneous()
    {
        $futures = [];
        $params = [
            'index' => $this->index,
            'id' => 1,
            'client' => [
                'future' => 'lazy'
            ]
        ];

        $futures['getRequest'] = $this->client->get($params);     // First request
        $params = [
            'index' => 'index',
            'id' => 2,
            'body' => [
                'field' => 'value'
            ],
            'client' => [
                'future' => 'lazy'
            ]
        ];

        $futures['indexRequest'] = $this->client->index($params);       // Second request

        $params = [
            'index' => 'test',
            'body' => [
                'query' => [
                    'match' => [
                        'field' => 'value'
                    ]
                ]
            ],
            'client' => [
                'future' => 'lazy'
            ]
        ];

        $futures['searchRequest'] = $this->client->search($params);      // Third request

        $searchResults = $futures['searchRequest']['hits'];

        $doc = $futures['getRequest']['_source'];
        return $searchResults;
    }

    public function json()
    {
        $params['body'] = [
            'query' => [
                'match' => [
                    'content' => 'quick brown fox'
                ]
            ],
            'highlight' => [
                'fields' => [
                    'content' => new \stdClass()
                ]
           ]
        ];
        $results = $this->client->search($params);

        return $results;

    }

    #region mapping
    public function putMappings()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'modify_date' => [
                        'type' => 'date',
                    ],
                    'newProperties' => [
                        'type' => 'keyword',

                    ]
                ]
            ]
        ];

        $response = $this->client->indices()->putMapping($params);
        return $response;
    }

    public function getMappings()
    {
        $response = $this->client->indices()->getMapping();
        return $response;
    }
    #end region
    #region Setting
    public function getSettings()
    {
        $params = ['index' => $this->index];
        $response = $this->client->indices()->getSettings($params);
        return $response;
    }

    public function putSettings()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_replicas' => 0,
                    'refresh_interval' => -1
                ]
            ]
        ];

        $response = $this->client->indices()->putSettings($params);
        return $response;
    }
    #end region

    #region index
    public function indexExists()
    {
        $indexParams['index'] = $this->index;
        $response = $this->client->indices()->exists($indexParams);
        return $response;
    }

    public function simpleIndexCreate()
    {
        $params = [
            'index' => $this->index
        ];
        $response = $this->client->indices()->create($params);
        return $response;

    }

    public function middleIndexCreate()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 2
                ],
                'mappings' => [
                    '_source' => [
                        'enabled' => true
                    ],
                    'properties' => [
                        'first_name' => [
                            'type' => 'keyword'
                        ],
                        'age' => [
                            'type' => 'integer'
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->client->indices()->create($params);
        return $response;
    }

    public function advancedIndexCreate()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'filter' => [
                            'shingle' => [
                                'type' => 'shingle'
                            ]
                        ],
                        'char_filter' => [
                            'pre_negs' => [
                                'type' => 'pattern_replace',
                                'pattern' => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
                                'replacement' => '~$1 $2'
                            ],
                            'post_negs' => [
                                'type' => 'pattern_replace',
                                'pattern' => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
                                'replacement' => '$1 ~$2'
                            ]
                        ],
                        'analyzer' => [
                            'reuters' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase', 'stop', 'kstem']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'reuters',
                            'copy_to' => 'combined'
                        ],
                        'body' => [
                            'type' => 'text',
                            'analyzer' => 'reuters',
                            'copy_to' => 'combined'
                        ],
                        'combined' => [
                            'type' => 'text',
                            'analyzer' => 'reuters'
                        ],
                        'topics' => [
                            'type' => 'keyword'
                        ],
                        'places' => [
                            'type' => 'keyword'
                        ]
                    ]
                ]
            ]
        ];
        $this->client->indices()->create($params);


    }

    public function deleteIndex()
    {
        $params = ['index' => $this->index];
        $response = $this->client->indices()->delete($params);
        return $response;
    }

    public function bulkArrayIndex()
    {
        for ($i = 0; $i < 100; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                ]
            ];

            $params['body'][] = [
                'my_field' => 'some value',
                'second_field' => 'some more values'
            ];
        }

        $responses = $this->client->bulk($params);
        return $responses;
    }

    public function bulkBatchIndex()
    {
        $params = ['body' => []];
        for ($i = 1; $i <= 1234567; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                    '_id' => $i
                ]
            ];

            $params['body'][] = [
                'my_field' => 'my_value',
                'second_field' => 'some more values'
            ];
            // Every 1000 documents stop and send the bulk request
            if ($i % 1000 == 0) {
                $responses = $this->client->bulk($params);
                // erase the old bulk request
                $params = ['body' => []];
                // unset the bulk response when you are done to save memory
                unset($responses);
            }
        }
        // Send the last batch if it exists
        if (!empty($params['body'])) {
            $responses = $this->client->bulk($params);
        }

    }
    #end region

    #region status
    public function indexStatus()
    {
        $response = $this->client->indices()->stats();
        return $response;
    }

    public function nodesStatus()
    {
        $response = $this->client->nodes()->stats();
        return $response;
    }

    public function clusterStatus()
    {
        $response = $this->client->cluster()->stats();
        return $response;
    }

    public function spesificIndexStatus()
    {
        $params['index'] = array($this->index);
        $response = $this->client->indices()->stats($params);
        return $response;
    }
    #end region

    #region query
    public function searchByTerm($search, $field)
    {
        $params = [
            'query' => [
                'term' => [
                    "$field" => [
                        'value' => trim($search)
                    ]
                ]
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    /*
     * return array
     * keyword mappingdan foydalib bir nechta term dan search qiladi
     */
    public function searchByTerms($search, $field)
    {
        $params = [
            'query' => [
                'terms' => [
                    "$field.keyword" => [
                        trim($search),
                        trim($search),
                    ]
                ]
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion


    /**
     *
     * Function  getDocByIds
     * @param array $ids
     * @return  mixed
     */
    #region
    public function getDocByIds(array $ids)
    {
        $params = [
            'query' => [
                'ids' => $ids
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion

    /**
     *
     * Function  searchByRange
     * @param $field search qilayotgan field name
     * @param $great katta range value
     * @param $less  kichik range value
     * @return  mixed
     */
    #region
    public function searchByRange($field, $great, $less)
    {
        $params = [
            'query' => [
                'range' => [
                    "$field" => [
                        'gte' => $great,
                        'lte' => $less
                   ],
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion

    /**
     *
     * Function  searchByExists
     * @param $field field name
     * @desctcription berilgan filed not null(bo'sh bo'lmagan) bo'lgan doclarni qaytaradi
     * @return  mixed
     */
    #region
    public function searchByExists($field)
    {
        $params = [
            'query' => [
                'exists' => [
                    "field" => $field
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion

    /**
     *
     * Function  searchByPrefix
     * @param $field search qilinayotgan field name
     * @param $value  prefix so'zni boshlanish
     * @desc  qidiralayotgan so'z (vagetable) value= vage
     * @return  mixed
     */
    #region
    public function searchByPrefix($field, $value)
    {
        $params = [
            'query' => [
                'prefix' => [
                    "$field" => [
                        'value'=> trim($value)
                    ]
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion

    /**
     *
     * Function  searchByMatch
     * @param $field  search qilinayotgan field name
     * @param $value search qilinayotgan qiymat
     * @param string $operator default or bo'ladi
     * @desc match query default or operatori bilan search qiladi 
     * @return  mixed
     */

    #region
    public function searchByMatch($field, $value, $operator = 'and')
    {
        $params = [
            'query' => [
                'match' => [
                    "$field" => [
                        'query'=> $value,
                        'operator'=> $operator
                    ]
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion


    /**
     *
     * Function  searchByMatch
     * @param $field  search qilinayotgan field name
     * @param $value search qilinayotgan qiymat
     * @param string $operator default or bo'ladi
     * @desc match query default or operatori bilan search qiladi
     * @return  mixed
     */

    #region
    public function searchByMultiMatch(array $fields, $value)
    {
        $params = [
            'query' => [
                'multi_match' => [
                    'query' => $value,
                    'fields' => $fields
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion


    /**
     *
     * Function  searchByMultiMatchBestFields
     * @param array $fields
     * @param $value
     * @desc (default) Finds documents which match any field, but uses the _score from the best field. See
     * @return  mixed
     */
    #region
    public function searchByMultiMatchBestFields(array $fields, $value)
    {
        $params = [
            'query' => [
                'multi_match' => [
                    'query' => $value,
                    'type' => 'best_fields',
                    'fields' => $fields
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion


    #region
    public function searchByMultiMatchMostFields(array $fields, $value)
    {
        $params = [
            'query' => [
                'multi_match' => [
                    'query' => $value,
                    'type' => 'most_fields',
                    'fields' => $fields
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion


    /**
     *
     * Function  searchByMultiMatchPhrasePrefix
     * @param array $fields
     * @param $value
     * @desc The phrase and phrase_prefix types behave just like best_fields, but they use a match_phrase or match_phrase_prefix query instead of a match query.
     * @return  mixed
     */
    public function searchByMultiMatchPhrasePrefix(array $fields, $value)
    {
        $params = [
            'query' => [
                'multi_match' => [
                    'query' => $value,
                    'type' => 'phrase_prefix',
                    'fields' => $fields
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }

    #endregion


    /**
     *
     * Function  searchByMultiMatchBoolPrefix
     * @param array $fields
     * @param $value
     * @desc The bool_prefix type’s scoring behaves like most_fields, but using a match_bool_prefix query instead of a match query
     * @return  mixed
     */
    #region
    public function searchByMultiMatchBoolPrefix(array $fields, $value)
    {
        $params = [
            'query' => [
                'multi_match' => [
                    'query' => $value,
                    'type' => 'bool_prefix',
                    'fields' => $fields
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }
    #endregion



    /**
     *
     * Function  searchDisableSource
     * @param array $field
     * @param $value
     * @desc source false holatda source qaytarmaydi field berilgan bo'lsa faqat o'sha fieldni qaytaradi
     * @return  mixed
     */
    #region
    public function searchDisableSource($field, $value)
    {
        //'source' => false,
        $params = [
            'source' => $field,

            'query' => [
                'match' => [
                    "$field" => $value,
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;

    }
    #endregion

    /**
     *
     * Function  searchDisableSource
     * @param array $field
     * @param $value
     * @desc $limit for pagination
     * @return  mixed
     */
    #region
    public function searchSize(string $field, $value, $limit)
    {
        $params = [
            'size' => $limit,
            'query' => [
                'match' => [
                    "$field" => $value,
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }
    #endregion

    /**
     *
     * Function  searchDisableSource
     * @param array $field
     * @param $value
     * @desc $limit for pagination
     * @return  mixed
     */
    #region
    public function searchOffset(string $field, $value, int $limit = 5, int $offset = 0)
    {
        $params = [
            'size' => $limit,
            'from' => $offset,
            'query' => [
                'match' => [
                    "$field" => $value,
                ],
            ],
        ];
        $response = $this->client->search($params);
        return $response;
    }
    #endregion

    /**
     *
     * Function  searchSort
     * @param array $field
     * @param $value
     * @desc $limit for pagination
     * @return  mixed
     */
    #region
    public function searchSort(string $field, $value, $type = 'desc')
    {
        $params = [
            'query' => [
                'match' => [
                    "$field" => $value,
                ],
            ],
            'sort'=>[
                "$field" => $type
            ]

        ];
        $response = $this->client->search($params);
        return $response;
    }
    #endregion


}

