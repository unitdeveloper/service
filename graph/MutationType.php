<?php

namespace zetsoft\service\graph;


use GraphQL\Type\Definition\ObjectType;
use zetsoft\models\user\User;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'changeUserEmail' => [
                        'type' => Types::user(),
                        'description' => 'Изменение E-mail пользователя',
                        'args' => [
                            'id' => Types::int(),
                            'email' => Types::string()
                        ],
                        'resolve' => function ($root, $args) {
                            $model = User::findOne($args['id']);
                            if ($model === null) {
                                throw new \Exception('Нет пользователя с таким id');
                            }
                            $model->email = $args['email'];
                            $model->save();
                            return $model;
                        }
                    ],
                    'addUser' => [
                        'type' => Types::user(),
                        'description' => 'Добавление пользователя',
                        'args' => [
                            'user' => Types::inputUser()
                        ],
                        'resolve' => function ($root, $args) {
                            // Добавляем нового пользователя в БД
                            $model = new User();
                            $model->name = $args['name'];
                            $model->password = $args['password'];
                            $model->save();
                            // Возвращаем данные только что созданного пользователя из БД
                            return $model;
                        }
                    ]
                ];
            }
        ];
        parent::__construct($config);
    }
}
