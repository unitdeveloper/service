<?php
/*
* Author: Madaminov Shaykhnazar
*
*/

namespace zetsoft\service\spatie;

use Spatie\Async\Pool;
use Spatie\Async\Task;

class MyTask extends Task
{
    public function configure()
    {
        // Setup eg. dependency container, load config,...
    }

    public function run()
    {
        // Do the real work here.
    }

    public function pool($things = null)
    {
        $pool = Pool::create();

        foreach ($things as $thing) {
            $pool->add(function () use ($thing) {
                // Do a thing
            })->then(function ($output) {
                // Handle success
            })->catch(function (Throwable $exception) {
                // Handle exception
            });
        }

        $pool->wait();
    }
    protected function handleSet(){

        $pool = Pool::create()

            // The maximum amount of processes which can run simultaneously.
            ->concurrency(20)

            // The maximum amount of time a process may take to finish in seconds
            // (decimal places are supported for more granular timeouts).
            ->timeout(15)

            // Configure which autoloader sub processes should use.
            ->autoload(__DIR__ . '/../../vendor/autoload.php')

            // Configure how long the loop should sleep before re-checking the process statuses in microseconds.
            ->sleepTime(50000)
        ;
    }

}