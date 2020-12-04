<?php
/**
 * class Climate
 * @package zetsoft/service/league
 * @author UzakbaevAxmet
 * class sizga rangli matni, maxsus formatlarni va boshqalarni osongina chiqarishga imkon beradi.
 **/

namespace zetsoft\service\league;


use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class Climate extends ZFrame
{

    #region Test

    public function test()
    {
        $this->climateTest();
        $this->inputTest();
        $this->tableTest();
        $this->commandsTest();
    }

    #endregion
    public function climateTest()
    {
        $red = 'Whoa now this text is red';
        $blue = 'Blue? Wow!';
        $result = $this->climate($red, $blue);
        vd($result);
    }

    public function climate($red, $blue)
    {


        $climate = new \League\CLImate\CLImate;

        $climate->red($red);
        $climate->blue($blue);
    }

    public function inputTest()
    {
        $input = 'How you doin?';
        $result = $this->input($input);
        vd($result);
    }

    public function input($enter)
    {


        $climate = new \League\CLImate\CLImate;

        $input = $climate->input($enter);

        $response = $input->prompt();


    }

    public function tableTest()
    {

        $data = [
            [
                'Walter White',
                'Father',
                'Teacher',
            ],
            [
                'Skyler White',
                'Mother',
                'Accountant',
            ],
            [
                'Walter White Jr.',
                'Son',
                'Student',
            ],
        ];
        $result = $this->table($data);
        vd($result);
    }

    public function table($data)
    {
        $climate = new \League\CLImate\CLImate;

        $climate->table($data);

    }

    public function commandsTest()
    {
        $error = 'Ruh roh.';
        $comment = 'Just so you know.';
        $whisper = 'Not so important, just a heads up.';
        $shout = 'This. This is important.';
        $info = 'Nothing fancy here. Just some info.';
        $result = $this->commands($error, $comment, $whisper, $shout, $info);
        vd($result);
    }

    public function commands($error, $comment, $whisper, $shout, $info)
    {
        $climate = new \League\CLImate\CLImate;
        $climate->error('Ruh roh.');
        $climate->comment('Just so you know.');
        $climate->whisper('Not so important, just a heads up.');
        $climate->shout('This. This is important.');
        $climate->info('Nothing fancy here. Just some info.');
    }

}
