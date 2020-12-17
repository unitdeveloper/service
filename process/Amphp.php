<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 * https://github.com/stonemax/acme2
 */

namespace zetsoft\service\process;
require Root . '/vendors/amphp/vendor/autoload.php';
use Amp\Coroutine;
use Amp\Loop;
use zetsoft\models\core\CoreQueue;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use Amp\Process\Process;
use function Amp\Promise\all;

class Amphp extends ZFrame
{
    #region Vars


    #endregion
    #region Test
    public function test()
    {
        $this->testManyPing();
    }

    private function testManyPing()
    {
        $hosts = ['ping 8.8.8.8', 'ping 8.8.4.4', 'ping google.com', 'ping stackoverflow.com', 'ping github.com'];

        $func = function (Process $process) {
            yield $process->start();

            $stream = $process->getStdout();

            while (null !== $chunk = yield $stream->read()) {
                echo $chunk;
            }

            $code = yield $process->join();
            $pid = $process->getPid();

            echo "Process {$pid} exited with {$code}\n";
        };
        $this->run($hosts, $func);
    }

    #endregion

    public function init()
    {
        parent::init();

        $this->layout = [
            'output' => function (Process $process) {
                yield $process->start();
                $stream = $process->getStdout();
                while (null !== $chunk = yield $stream->read()) {
                    echo $chunk;
                }
                $code = yield $process->join();
                $pid = $process->getPid();

                echo "Process {$pid} exited with {$code}\n";
            }
        ];
    }


    /**
     *
     * Function  run
     * @param $commands
     * @param callable|null $output
     * You may send commands as string or as array and
     * your own callback function for getting output
     */

    public function run($commands, callable $output = null)
    {
        if ($output === null)
            $output = $this->layout['output'];
        Loop::run(static function () use ($commands, $output) {
            $promises = [];
            if (is_array($commands))
                foreach ($commands as $command) {
                    $process = new Process($command);
                    $promises[] = new Coroutine($output($process));
                }
            else {
                $command = $commands;
                $process = new Process($command);
                $promises[] = new Coroutine($output($process));
            }
            yield all($promises);
        });
    }

    #region Queue

    public function runQueue1($command, $id)
    {
        Loop::run(function () use ($command, $id) {
            $promises = [];

            $process = new Process($command);
            $promises[] = new Coroutine($this->saveOutput($process, $id));
        });
    }

    public function runQueue($commands = [])
    {
        Loop::run(function () use ($commands) {

            $promises = [];
            foreach ($commands as $key => $command) {
                $process = new Process($command);
                $promises[] = new Coroutine($this->saveOutput($process, $key));
            }
        });
    }

    private function saveOutput(Process $process, $id = null): \Generator
    {
        yield $process->start();

        $stream = $process->getStdout();
        $pid = $process->getPid();

        while (null !== $message = yield $stream->read()) {
            Az::$app->utility->execs->updateQueueCmd($id, $message, $pid);
        }

        $code = yield $process->join();
        $exitMsg = "Process {$pid} exited with {$code}\n";
        Az::$app->utility->execs->updateQueueCmd($id, $exitMsg, $pid, CoreQueue::status['finished']);
    }

    #endregion
}
