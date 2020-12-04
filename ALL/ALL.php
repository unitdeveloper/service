<?php
        
namespace zetsoft\service\ALL;
        
trait ALL {

    /* @var Acme $acme */
            public $acme;
            
            /* @var Auth $auth */
            public $auth;
            
            /* @var Auto $auto */
            public $auto;
            
            /* @var Bot $bot */
            public $bot;
            
            /* @var Calls $calls */
            public $calls;
            
            /* @var Chat $chat */
            public $chat;
            
            /* @var Cores $cores */
            public $cores;
            
            /* @var Cpas $cpas */
            public $cpas;
            
            /* @var Filemanager $filemanager */
            public $filemanager;
            
            /* @var Forms $forms */
            public $forms;
            
            /* @var FreePBX $freePBX */
            public $freePBX;
            
            /* @var Fronts $fronts */
            public $fronts;
            
            /* @var Geo $geo */
            public $geo;
            
            /* @var Gitapp $gitapp */
            public $gitapp;
            
            /* @var Graph $graph */
            public $graph;
            
            /* @var Guid $guid */
            public $guid;
            
            /* @var Https $https */
            public $https;
            
            /* @var Illuminate $illuminate */
            public $illuminate;
            
            /* @var Image $image */
            public $image;
            
            /* @var Inputs $inputs */
            public $inputs;
            
            /* @var Iterate $iterate */
            public $iterate;
            
            /* @var Jsonb $jsonb */
            public $jsonb;
            
            /* @var League $league */
            public $league;
            
            /* @var Maps $maps */
            public $maps;
            
            /* @var Market $market */
            public $market;
            
            /* @var Markup $markup */
            public $markup;
            
            /* @var Maths $maths */
            public $maths;
            
            /* @var Media $media */
            public $media;
            
            /* @var Menu $menu */
            public $menu;
            
            /* @var Mobile $mobile */
            public $mobile;
            
            /* @var Office $office */
            public $office;
            
            /* @var Optima $optima */
            public $optima;
            
            /* @var Parser $parser */
            public $parser;
            
            /* @var Payer $payer */
            public $payer;
            
            /* @var Phpdoc $phpdoc */
            public $phpdoc;
            
            /* @var Process $process */
            public $process;
            
            /* @var Ratchet $ratchet */
            public $ratchet;
            
            /* @var Reacts $reacts */
            public $reacts;
            
            /* @var Search $search */
            public $search;
            
            /* @var Select $select */
            public $select;
            
            /* @var Slug $slug */
            public $slug;
            
            /* @var Smart $smart */
            public $smart;
            
            /* @var Sms $sms */
            public $sms;
            
            /* @var Soaps $soaps */
            public $soaps;
            
            /* @var Socket $socket */
            public $socket;
            
            /* @var Spatie $spatie */
            public $spatie;
            
            /* @var Temps $temps */
            public $temps;
            
            /* @var Tests $tests */
            public $tests;
            
            /* @var Utility $utility */
            public $utility;
            
            /* @var Valid $valid */
            public $valid;
            
            /* @var Webs $webs */
            public $webs;
            
            /* @var App $App */
            public $App;
            
            
       
       
       
            
    public static function ioc() {
        return [
                'acme' => Acme::class,
            
                'auth' => Auth::class,
            
                'auto' => Auto::class,
            
                'bot' => Bot::class,
            
                'calls' => Calls::class,
            
                'chat' => Chat::class,
            
                'cores' => Cores::class,
            
                'cpas' => Cpas::class,
            
                'filemanager' => Filemanager::class,
            
                'forms' => Forms::class,
            
                'freePBX' => FreePBX::class,
            
                'fronts' => Fronts::class,
            
                'geo' => Geo::class,
            
                'gitapp' => Gitapp::class,
            
                'graph' => Graph::class,
            
                'guid' => Guid::class,
            
                'https' => Https::class,
            
                'illuminate' => Illuminate::class,
            
                'image' => Image::class,
            
                'inputs' => Inputs::class,
            
                'iterate' => Iterate::class,
            
                'jsonb' => Jsonb::class,
            
                'league' => League::class,
            
                'maps' => Maps::class,
            
                'market' => Market::class,
            
                'markup' => Markup::class,
            
                'maths' => Maths::class,
            
                'media' => Media::class,
            
                'menu' => Menu::class,
            
                'mobile' => Mobile::class,
            
                'office' => Office::class,
            
                'optima' => Optima::class,
            
                'parser' => Parser::class,
            
                'payer' => Payer::class,
            
                'phpdoc' => Phpdoc::class,
            
                'process' => Process::class,
            
                'ratchet' => Ratchet::class,
            
                'reacts' => Reacts::class,
            
                'search' => Search::class,
            
                'select' => Select::class,
            
                'slug' => Slug::class,
            
                'smart' => Smart::class,
            
                'sms' => Sms::class,
            
                'soaps' => Soaps::class,
            
                'socket' => Socket::class,
            
                'spatie' => Spatie::class,
            
                'temps' => Temps::class,
            
                'tests' => Tests::class,
            
                'utility' => Utility::class,
            
                'valid' => Valid::class,
            
                'webs' => Webs::class,
            
                'App' => App::class,
            ];
    }
            
}