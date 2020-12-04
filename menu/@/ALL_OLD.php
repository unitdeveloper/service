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


use rmrevin\yii\fontawesome\FAS;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use zetsoft\service\smart\Cruds;
use zetsoft\service\smart\Model;
use zetsoft\system\assets\ZMenu;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\module\Models;

class ALL_OLD extends ZMenu
{

    public function run()
    {

        Az::start(__METHOD__);

        $this->widgets();
        $this->develop();
        //   $this->library();

        return $this->optionsALL;


    }

    private function develop()
    {

        /*   $admin = Az::$app->cores->auth->role('admin');
           $userDev = $boot->userDev();

           if ($admin || $userDev)
               return true;*/

        $this->models();
        $this->add('');

        return false;
    }

    private function widgets()
    {
        if (!$boot->userDev())
            return false;

        $scanWidget = ArrayHelper::getValue($_COOKIE, 'ScanWidget', '0') === '1';

        if ($scanWidget)
            $this->render('@zetsoft/webhtm/ALL/render/');

        return true;
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
                            'linkOptions' => [
                                'class' => '',
                                'tooltip' => null,
                                'icon' => '',
                            ]
                        ],
                        [
                            'label' => 'Edit ID=4',
                            'url' => [
                                '/book/lister/edit',
                                'id' => 4,
                                'page' => 5,
                            ],
                            'linkOptions' => [
                                'class' => '',
                                'tooltip' => null,
                                'icon' => '',
                            ]
                        ],
                        [
                            'label' => 'Edit ID=4 & Name',
                            'url' => [
                                '/book/lister/edit',
                                'id' => 4,
                                'name' => 'Murod',
                            ],
                            'linkOptions' => [
                                'class' => '',
                                'tooltip' => null,
                                'icon' => '',
                            ]
                        ],
                    ]
                ],
            ]
        ];

        $this->add('Library');

    }

    private function models()
    {

        $classes = Az::$app->smart->migra->scan();
        $systems = [];
        $models = [];



        foreach ($classes as $class) {

            $className = bname($class);
            $controller = ZInflector::camel2id($className);

            /** @var Models $model */
            $model = new $class();
            $title = $model->configs->title;

            if ($this->catModel($className) === 'ALL') {
                if ($model->configs->makeMenu)
                    $systems[] = [
                        'label' => $title,
                        'url' => ["/admin/{$controller}"],
                        'blank' => true,
                        'linkOptions' => [
                            'class' => '',
                            'tooltip' => null,
                            'icon' => 'fa fa-' . Az::$app->utility->mains->icon(),
                        ]
                    ];
            } else {
                if ($model->configs->makeMenu)
                    $elements[] = [
                        'label' => $title,
                        'url' => ["/admin/{$controller}"],
                        'blank' => true,
                        'linkOptions' => [
                            'class' => '',
                            'tooltip' => null,
                            'icon' => 'fa fa-' . Az::$app->utility->mains->icon(),
                        ]
                    ];
            }

            Az::debug($model->configs->title, 'Added to Menu');
        }

        $this->options[] = [
            'label' => Az::l('Система'),
            'items' => $systems
        ];

        $this->options[] = [
            'label' => Az::l('Элементы'),
            'items' => $elements
        ];


    }

}
