<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 * Createt by Xakimjon Ergashov
 *
 */

namespace zetsoft\service\reacts;


use React\ChildProcess\Process;
use React\EventLoop\Factory;
use zetsoft\service\App\eyuf\User;
use zetsoft\system\kernels\ZFrame;

class ReactPhp extends ZFrame
{

    public function test()
    {

        $this->test2();
    }


    public function test1()
    {

        $loop = \React\EventLoop\Factory::create();


        $process = new \React\ChildProcess\Process("php ..\..\excmd\ALL\asrorz.php  caller/marce-ami/run --agentId={$id}");

        $process->start($loop);


        $loop->run();

    }

    public function test2()
    {

        $loop = \React\EventLoop\Factory::create();

        $ids = \zetsoft\models\user\User::find()
            ->where([
                'role' => 'operator'
            ])
            ->asArray()
            ->all();


        foreach ($ids as $id)
            $process = new Process("@php ..\..\\excmd\ALL\asrorz.php  caller/marce-ami/run --agentId=124 --agentId={$id['id']}", null, null, []);

        $process->start($loop);

        $process->on('exit', function ($exitcode) {
            echo 'exit wiSDASFSDFDth ' . $exitcode . PHP_EOL;
        });
    }


}
