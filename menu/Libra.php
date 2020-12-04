<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\menu;


use yii\helpers\FileHelper;
use zetsoft\service\smart\Cruds;
use zetsoft\system\assets\ZMenu;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\module\Models;

class Libra extends ZMenu
{

    public $systemGens = [];
    public $modelGen = [];


    public function run()
    {

        Az::start(__METHOD__);
  /*      $this->develop();
        $this->library();*/

        return $this->optionsALL;

    }


    private function develop()
    {
        $this->models();
        $this->add('Разработка');
    }

    private function library()
    {

        $this->options[] = [
            'label' => 'Library',
            'items' => [
                [
                    'label' => 'Author',
                    'visible' => false,
                    'items' => [
                        [
                            'label' => 'Create',
                            'url' => ['/book/lister/create'],
                        ],
                        [
                            'label' => 'Edit ID=4',
                            'url' => [
                                '/book/lister/edit',
                                'id' => 4,
                                'page' => 5,
                            ],
                        ],
                        [
                            'label' => 'Edit ID=4 & Name',
                            'url' => [
                                '/book/lister/edit',
                                'id' => 4,
                                'name' => 'Murod',
                            ],
                        ],
                    ]
                ],

                [
                    'label' => 'Shaxzod',
                    'items' => [
                        [
                            'label' => 'AuthPhoneForm',
                            'url' => ['/shaxzod/main/phone'],
                        ],
                        [
                            'label' => 'Dell',
                            'url' => ['/shaxzod/dell/index'],
                        ],
                    ]
                ],

                [
                    'label' => 'Sahibka',
                    'items' => [
                        [
                            'label' => 'NewForm',
                            'url' => ['/sahibka/new/phone'],
                        ],
                    ]
                ],
                // echo


                [
                    'label' => 'Madina',
                    'items' => [
                        [
                            'label' => 'ComputerForm',
                            'url' => ['/madinak/computer/name'],
                        ],
                    ]
                ],
                [
                    'label' => 'Madiyor',
                    'items' => [
                        [
                            'label' => 'FormaForm',
                            'url' => ['/madiyor/forma/forma'],
                        ],
                    ]
                ],


                [
                    'label' => 'Zoxidjon',
                    'items' => [
                        [
                            'label' => 'CarForm',
                            'url' => ['/zoxidjon/car/phone'],
                        ],
                        [
                            'label' => 'CarForm2',
                            'url' => ['/zoxidjon/car/check'],
                        ]
                    ]

                ],
                [
                    'label' => 'Tolipov',
                    'items' => [
                        [
                            'label' => 'AuthPhoneForm',
                            'url' => ['/tolipov/tolipov/phone'],
                        ],
                        [
                            'label' => 'AuthPhoneForm',
                            'url' => ['/tolipov/tolipov/check'],
                        ]
                    ]

                ],


                [
                    'label' => 'Shahnoza',
                    'items' => [
                        [
                            'label' => 'StudentForm',
                            'url' => ['/shahnoza/student/student'],
                            [
                                'label' => 'SchoolForm',
                                'url' => ['/shahnoza/school/create'],
                            ],
                        ],
                    ]
                ],

                [
                    'label' => 'Form Process',
                    'items' => [
                        [
                            'label' => 'Check',
                            'url' => ['/form/data/check'],
                        ],

                    ]
                ],
            ]
        ];
        $this->add('Library');

    }


    private function models()
    {

        $modelPath = Az::getAlias(Cruds::pathModel);

        $aModel = \yii\helpers\BaseFileHelper::findFiles($modelPath, ['recursive' => false]);

        foreach ($aModel as $sModel) {
            $className = bname($sModel);
            $className = str_replace('.php', '', $className);

            /** @var Models $classNameFull */
            $classNameFull = "zetsoft\models\\" . App . "\\{$className}";
            $title = $classNameFull->configs->title;

            $controller = ZInflector::camel2id($className);
            $tableName = ZInflector::underscore($className);


            if (in_array($tableName, TablesCore, true)) {
                if ($classNameFull->configs->makeMenu)
                    $this->systemGens[] = [
                        'label' => $title,
                        'url' => "/admin/{$controller}/index.aspx",
                        'blank' => true
                    ];
            } else {

                if ($classNameFull->configs->makeMenu)
                    $this->modelGen[] = [
                        'label' => $title,
                        'url' => "/admin/{$controller}/index.aspx",
                        'blank' => true
                    ];

            }
            $a = 0;
        }

        $this->options[] = [
            'label' => 'Models',
            'items' => $this->modelGen
        ];
    }



}
