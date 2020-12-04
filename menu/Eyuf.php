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


use kcfinder\text;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\FileHelper;
use zetsoft\service\smart\Cruds;
use zetsoft\system\assets\ZMenu;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZIcon;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\module\Models;

class Eyuf extends ZMenu
{

    public $systemGens = [];
    public $modelGen = [];


    public function run()
    {

        Az::start(__METHOD__);

        $admin = Az::$app->cores->rbac->checkRole('admin');
        $moder = Az::$app->cores->rbac->checkRole('moder');

        if ($admin || $moder)
            $this->admin();
        else
            $this->user();

        return $this->options;

    }


    private function admin()
    {

        /*     $this->options[] = [
                 'label' => 'Комплекс',
                 'visible' => false,
                 'items' => [
                     [
                         'label' => 'Create',
                         'url' => '/shop/admin/core-group-user.aspx',
                     ], [
                         'label' => 'Create',
                         'url' => '/shop/admin/core-group-user.aspx',
                     ], [
                         'label' => 'Create',
                         'url' => '/shop/admin/core-group-user.aspx',
                     ], [
                         'label' => 'Create',
                         'url' => '/shop/admin/core-group-user.aspx',
                     ],
                 ],
             ];*/



        $this->options[] = [
            'label' => 'Организации',
            'visible' => false,
            'icon' => FA::_SITEMAP,
            'url' => ['/shop/admin/core-company'],
        ];
        $this->options[] = [
            'label' => 'Уведомления',
            'visible' => false,
            'icon' => FA::_BELL,
            'url' => ['/shop/admin/core-notify'],
        ];
        $this->options[] = [
            'label' => 'Документы',
            'visible' => false,
            'icon' => 'fa-flag-o',
            'url' => ['/shop/admin/core-document'],
        ];
        $this->options[] = [
            'label' => 'Потребности',
            'visible' => false,
            'icon' => FA::_EXCHANGE_ALT,
            'url' => ['/shop/admin/needs'],
        ];
        $this->options[] = [
            'label' => 'Соотечественники',
            'visible' => false,
            'icon' => FA::_CHILD,
            'url' => ['/shop/admin/compatriot'],
        ];
        $this->options[] = [
            'label' => Az::l('Расходы'),
            'visible' => false,
            'icon' => FA::_MONEY_BILL,
            'url' => ['/shop/admin/invoice'],
        ];
        $this->options[] = [
            'label' => 'Права доступа',
            'visible' => false,
            'url' => ['/shop/admin/core-perm'],
            'icon' => FA::_UNIVERSAL_ACCESS,
        ];
        $this->options[] = [
            'label' => 'FAQ',
            'visible' => false,
            'icon' => FA::_QUESTION_CIRCLE,
            'url' => ['/core/faq/index'],
        ];
        $this->options[] = [
            'label' => 'Специальность',
            'visible' => false,
            'url' => ['/shop/admin/core-speciality'],
        ];
        $this->options[] = [
            'label' => 'Чат',
            'visible' => false,
            'icon' => FA::_USER_TIMES,
            'url' => '/core/tester/chat.aspx',
        ];
        $this->options[] = [
            'label' => 'Друзья',
            'visible' => false,
            'icon' => FA::_OBJECT_GROUP,
            'url' => ['/shop/admin/core-friend'],
        ];
        $this->options[] = [
            'label' => 'Kарта',
            'visible' => false,
            'icon' => FA::_MAP,
            'url' => ['/core/faq/globmap'],
        ];
        $this->add('');

    }

    private function user()
    {


        $this->options[] = [
            'label' => 'Мой Профиль',
            'visible' => false,
            'icon' => FA::_USER,
            'url' => ['/mains/main/profile'],
        ];


        $this->options[] = [
            'label' => 'Мониторинг',
            'visible' => false,
            'icon' => ZIcon::show(FA::_REGISTERED),
            'url' => ['/mains/main/register'],

        ];


        $this->options[] = [
            'label' => 'Регистрация',
            'visible' => false,
            'url' => ['/mains/main/register'],
        ];

        $this->options[] = [
            'label' => 'Дополнительно',
            'visible' => false,
            'icon' => FA::_PLUS,
            'url' => ['/mains/main/add-data'],
        ];


        $this->options[] = [
            'label' => 'Карта',
            'visible' => false,
            'icon' => FA::_MAP_PIN,
            'url' => '/core/tester/asror/main.aspx?path=render%5CMaps%5CZamChart4Widget%5Csample_Data',
        ];

        $this->options[] = [
            'label' => 'Кандидат',
            'visible' => false,
            'url' => '/shop/admin/candidate.aspx',
        ];


        $this->options[] = [
            'label' => 'Справочник',
            'visible' => false,
            'url' => '/render/Eyufs/qollanma.php',
        ];

        $this->options[] = [
            'label' => 'Обьявления',
            'visible' => false,
            'url' => '/render/Eyufs/elon.php',
        ];

        $this->options[] = [
            'label' => 'Объективка (эгземпляр)',
            'visible' => false,
            'url' => '/render/Eyufs/upload/ilova/namuna.docx',
        ];

        $this->options[] = [
            'label' => 'Заявление',
            'visible' => false,
            'url' => '/render/Eyufs/upload/ilova/ariza.docx',
        ];

        $this->options[] = [
            'label' => 'Чек-лист',
            'visible' => false,
            'url' => '/render/Eyufs/upload/ilova/checklist.docx',
        ];

        $this->options[] = [
            'label' => 'FAQ',
            'visible' => false,
            'url' => '/core/tester/asror/main.aspx?path=render%5Cnaviga%5CZAccWidget%5Csample',
        ];

        $this->options[] = [
            'label' => 'Контакты',
            'visible' => false,
            'url' => '/render/Eyufs/contact.php',
        ];


        $this->add('');

    }


}
