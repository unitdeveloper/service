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

namespace zetsoft\service\utility;


use React\ChildProcess\Process;
use React\EventLoop\Factory;
use React\EventLoop\TimerInterface;
use zetsoft\system\kernels\ZFrame;


/**
 * Class    Execs
 * @package zetsoft\service\utility
 *
 * https://symfony.com/doc/current/components/process.html
 */
class ExecsAsync extends ZFrame
{
    public function run($command)
    {
        $this->runCommandInCmd($command, $isArray = is_array($command));
        $this->runCommandInIDE($command, $isArray = is_array($command));
    }

    private function runCommandInIDE($command, $isArray = false)
    {
        $loop = Factory::create();
        $counter = 0;
        $loop->addPeriodicTimer(2, function (TimerInterface $timer) use ($isArray, $command, &$counter, $loop) {
            $execute = "";
            if ($isArray) {
                $execute = $command[$counter];
                if ($counter + 1 == count($command)) {
                    $loop->cancelTimer($timer);
                }
            } else
                $execute = $command;

            $process = new \Symfony\Component\Process\Process($execute);
            $process->start();

            foreach ($process as $type => $data) {
                if ($process::OUT === $type) {
                    echo "hello-----------------------------------------" . PHP_EOL;
                    echo "\nRead from stdout: " . $data;
                } else { // $process::ERR === $type
                    echo "\nRead from stderr: " . $data;
                }
            }
            $counter++;
            if (!$isArray)
                $loop->cancelTimer($timer);
        });
        $loop->run();
    }

    private function runCommandInCmd($command, $isArray = false)
    {
        $loop = Factory::create();
        $counter = 0;
        $loop->addPeriodicTimer(0.1, function (TimerInterface $timer) use ($isArray, $command, &$counter, $loop) {
            $execute = "";
            if ($isArray) {
                $execute = $command[$counter];
                if ($counter + 1 == count($command)) {
                    $loop->cancelTimer($timer);
                }
            } else
                $execute = $command;
            $process = new Process('cmd /c start cmd.exe /k ' . $execute, null, null, array());
            $process->start($loop);
            $process->on('close', function ($exitcode) {
                echo 'exit with ' . $exitcode . PHP_EOL;
            });
            $counter++;
            if (!$isArray)
                $loop->cancelTimer($timer);
        });
        $loop->run();
    }
}
