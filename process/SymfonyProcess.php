<?php


namespace zetsoft\service\process;


use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use React\EventLoop\Factory;
use Symfony\Component\Process\Process;
use zetsoft\models\core\CoreQueue;
use zetsoft\service\utility\Execs;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class SymfonyProcess extends ZFrame
{
    #region Vars

    public $errorCall = self::func['error'];
    public $succesCall = self::func['success'];

    public const func = [
        'error' => 'error',
        'success' => 'success',
    ];

    #endregion

    #region Test

    public function test()
    {
        $this->testRun();
    }

    public function testRun()
    {
        $this->run(['calc.exe'], $this->layout['success'], $this->layout['error']);
    }

    #endregion

    public function init()
    {
        parent::init();

        $this->layout = [
            'error' => static function ($output, $pid) {
                echo $output . '--- Process code: ' . $pid;
            },
            'success' => static function ($output, $pid) {
                echo $output . '--- Process code: ' . $pid;
            }
        ];


    }

    /**
     *
     * Function  run
     * @param $cmd
     * @param callable|null $successOut
     * @param callable|null $errorOut
     * @return  int|null
     */
    public function run($cmd, callable $successOut = null, callable $errorOut = null)
    {

        if ($successOut === null) {
            $successOut = $this->layout['success'];
        }
        if ($errorOut === null) {
            $errorOut = $this->layout['error'];
        }

        $process = new Process($cmd);

        $process->run(static function ($type, $buffer) use ($process, $successOut, $errorOut) {
            if (Process::ERR === $type) {
                $errorOut($buffer, $process->getPid());
            } else {
                $successOut($buffer, $process->getPid());
            }
        });
        return $process->getExitCode();
    }

    public function start($cmd, $function = null, $id = null)
    {
        $process = new Process($cmd);
        $process->setTimeout(240);
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                Az::$app->utility->execs->updateQueueCmd($id, 'OUT > ' . $data, $process->getPid());
            } else {
                Az::$app->utility->execs->updateQueueCmd($id, 'ERR > ' . $data, $process->getPid());
            }
        }
        $process->getExitCode();
    }

    #region Queue

    public function php($cmd, $id)
    {
        $path = Root . '\excmd\ALL\asrorz.php';
        $path = ZFileHelper::normalizePath($path);
        $this->runQueu(['php', $path, $cmd, $id], $id);
    }

    public function runQueu($cmd, $id = null)
    {
        $process = new Process($cmd);

        $process->run(function ($type, $buffer) use ($process, $id) {
            if (Process::ERR === $type) {
                Az::$app->utility->execs->updateQueueCmd($id, 'ERR > ' . $buffer, $process->getPid());
            } else {
                Az::$app->utility->execs->updateQueueCmd($id, 'OUT > ' . $buffer, $process->getPid());
            }
        });
    }

    public function startQueue($cmd, $function = null, $id = null)
    {
        $process = new Process($cmd);
        $process->setTimeout(240);
        $process->start();
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                Az::$app->utility->execs->updateQueueCmd($id, 'OUT > ' . $data, $process->getPid());
            } else {
                Az::$app->utility->execs->updateQueueCmd($id, 'ERR > ' . $data, $process->getPid());
            }
        }
        $process->getExitCode();
    }
    #endregion
}
