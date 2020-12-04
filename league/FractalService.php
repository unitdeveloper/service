<?php
/**
 * Class    FractalService
 * @package zetsoft\service\league
 *
 * @author DilshodKhudoyarov
 *
 * Class file Fractal murakkab ma'lumotlarni chiqarish uchun taqdimot va o'zgartirish qatlamini ta'minlaydi
 */

namespace zetsoft\service\league;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use zetsoft\system\kernels\ZFrame;


class FractalService extends ZFrame
{
    public function test_case() {
        $this->spread_data_collectionTest();
    }

    public function spread_data_collection($list) {
        $fractal = new Manager();
        $resource = new Collection($list, function(array $list) {
            return [
                'id'      => (int) $list['id'],
                'title'   => $list['title'],
                'year'    => (int) $list['yr'],
                'author'  => [
                    'name'  => $list['author_name'],
                    'email' => $list['author_email'],
                ],
                'links'   => [
                    [
                        'rel' => 'self',
                        'uri' => '/books/'.$list['id'],
                    ]
                ]
            ];
        });

        $array = $fractal->createData($resource)->toArray();

        echo $fractal->createData($resource)->toJson();
        vd($array);
  }

    public function spread_data_collectionTest() {
// Create a top level instance somewhere
        $fractal = new Manager();

// Get data from some sort of source
// Most PHP extensions for SQL engines return everything as a string, historically
// for performance reasons. We will fix this later, but this array represents that.
        $books = [
            [
                'id' => '1',
                'title' => 'Hogfather',
                'yr' => '1998',
                'author_name' => 'Philip K Dick',
                'author_email' => 'philip@example.org',
            ],
            [
                'id' => '2',
                'title' => 'Game Of Kill Everyone',
                'yr' => '2014',
                'author_name' => 'George R. R. Satan',
                'author_email' => 'george@example.org',
            ]
        ];

// Pass this array (collection) into a resource, which will also have a "Transformer"
// This "Transformer" can be a callback or a new instance of a Transformer object
// We type hint for array, because each item in the $books var is an array
        $resource = new Collection($books, function(array $book) {
            return [
                'id'      => (int) $book['id'],
                'title'   => $book['title'],
                'year'    => (int) $book['yr'],
                'author'  => [
                    'name'  => $book['author_name'],
                    'email' => $book['author_email'],
                ],
                'links'   => [
                    [
                        'rel' => 'self',
                        'uri' => '/books/'.$book['id'],
                    ]
                ]
            ];
        });

// Turn that into a structured array (handy for XML views or auto-YAML converting)
        $array = $fractal->createData($resource)->toArray();

// Turn all of that into a JSON string
        echo $fractal->createData($resource)->toJson();
        vd($array);
    }
}