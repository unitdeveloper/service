<?php


namespace zetsoft\service\reacts;


use React\ChildProcess\Process;
use React\EventLoop\Factory;
use zetsoft\service\utility\Execs;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ChildProcess extends ZFrame
{
    public function timer($sec, $function)
    {
        $loop = Factory::create();
        $loop->addTimer($sec, function () use ($function) {
            $function();
        });
        $loop->run();
    }

    public function runCommand($command)
    {
        $loop = Factory::create();
        $counter = 0;
        $loop->addPeriodicTimer(0.1, function (\React\EventLoop\TimerInterface $timer) use ($command, &$counter, $loop) {
            $isArray = is_array($command);
            $execute = "";

            if ($isArray) {
                $execute = $command[$counter];
                if ($counter + 1 == count($command)) {
                    $loop->cancelTimer($timer);
                }
            } else
                $execute = $command;

                
            $process = new Process('cmd /c ' . $execute, null, null, array());
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

    public function runQueueLoop()
    {
        $loop = Factory::create();
        $loop->addPeriodicTimer(0.1, function (\React\EventLoop\TimerInterface $timer) {
            Az::$app->utility->execs->runAllQueue();
        });
        $loop->run();
    }
}
