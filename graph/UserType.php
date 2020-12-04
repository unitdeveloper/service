<?php

namespace zetsoft\service\graph;

use GraphQL\Type\Definition\ObjectType;

/**
 * Class UserType
 *
 * Тип User для GraphQL
 *
 *
 */
class UserType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Пользователь',
            'fields' => function() {
                return [
                    'id' => [
                        'type' => Types::string(),
                        'description' => 'Идентификатор пользователя'
                    ],
                    'name' => [
                        'type' => Types::string(),
                        'description' => 'Имя пользователя'
                    ],
                    'email' => [
                        'type' => Types::string(),
                        'description' => 'E-mail пользователя'
                    ],
                    'phone' => [
                        'type' => Types::string(),
                        'description' => 'Телефон пользователя'
                    ],
                ];
            }
        ];
        parent::__construct($config);
    }
}
