<?php

namespace zetsoft\service\graph;


use GraphQL\Type\Definition\InputObjectType;
use zetsoft\service\graph\Types;

class InputUserType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'Добавление пользователя',
            'fields' => function() {
                return [
                    'name' => [
                        'type' => Types::string(),
                        'description' => 'Имя пользователя'
                    ],
                    'password' => [
                        'type' => Types::string(),
                        'description' => 'Password'
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }
}
