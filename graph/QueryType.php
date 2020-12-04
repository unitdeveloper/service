<?php

namespace zetsoft\service\graph;

use GraphQL\Type\Definition\ObjectType;
use zetsoft\models\user\User;

class QueryType extends ObjectType
{
    public function __construct()
    {
        

        $config = [
            'fields' => function() {
                return [
                    'user' => [
                        'type' => Types::user(),
                        'description' => 'Возвращает пользователя по id',
                        'args' => [
                            'id' => Types::int()
                        ],
                        'resolve' => function ($root, $args) {
                            return \zetsoft\models\user\User::findOne($args['id']);
                        }
                    ],
                    'allUsers' => [
                        'type' => Types::listOf(Types::user()),
                        'description' => 'Список пользователей',
                        'resolve' => function () {
                            return User::find()->all();
                        }
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }
}
