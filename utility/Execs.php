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


use NumberToWords\Legacy\Numbers\Words\Locale\Ro;
use Symfony\Component\Process\Process;
use zetsoft\cncmd\cores\QueueController;
use zetsoft\dbitem\ALL\ZServiceAppItem;
use zetsoft\dbitem\core\ServiceItem;
use zetsoft\models\core\CoreQueue;
use zetsoft\service\cores\Auth;
use zetsoft\service\cores\Rbac;
use zetsoft\system\Az;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;


/**
 * Class    Execs
 * @package zetsoft\service\utility
 *
 * https://symfony.com/doc/current/components/process.html
 */
class Execs extends ZFrame
{

    public $cmd;

    public $continous;
    public $background;

    public const type = [
        'default' => 'Default',
        'continous' => 'Continous',
        'background' => 'Background',
    ];

    #region Core

    public function init()
    {
        parent::init();
        $this->chdir(Root);

        global $boot;

        if ($boot->isWindows()) {
            $this->continous = Az::getAliasNorm('@zetsoft/scripts/runner/runx.exe');
            $this->background = Az::getAliasNorm('@zetsoft/scripts/runner/exec.exe');
        } else {
            $this->continous = Az::getAliasNorm('@zetsoft/scripts/runner/runx.exe');
            $this->background = Az::getAliasNorm('@zetsoft/scripts/runner/exec.exe');

        }
    }


    public function test()
    {
        $this->execTest();
        //   $this->execTrueTest();
    }

    #endregion


    #region exec


    public function execTest()
    {

        $file = Root . '/scripts/runner/runx.exe';

        $root = Root;

        $php = "d:/Develop/Projects/ALL/server/php7/7_44/php.exe";
        $cmd = "caller/marce-ami/call";
        $arg = "--agentId=124";

        $line = ' 1 "{php}" {root}/excmd/asrorz.php {cmd} {arg}';

        $line = strtr($line, [
            '{php}' => $php,
            '{root}' => $root,
            '{cmd}' => $cmd,
            '{arg}' => $arg,
        ]);


        $this->exec($file . $line, true);

        $file = Root . '/scripts/runner/runx.exe';

        $this->exec($file . ' "d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run --agentId=124', true);
        $this->exec($file . ' "d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run --agentId=126', true);
        $this->exec($file . ' "d:/Develop/Projects/ALL/server/php7/7_44/php.exe" D:/Develop/Projects/ALL/asrorz/zetsoft/excmd/asrorz.php caller/marce-ami/run --agentId=128', true);

    }


    public function exec(string $cmd, $type = self::type['default'])
    {

        switch ($type) {
            case self::type['background']:
                $exec = $this->background;
                break;

            case self::type['continous']:
                $restartDelay = $this->paramGet('restartDelay');
                $exec = "{$this->continous} {$restartDelay}";
                break;

            default:
                $exec = '';
        }

        if ($type !== self::type['default']) {
            $cmd = "{$exec} $cmd";
            pclose(popen('start /B ' . $cmd, 'r'));
            $return = 0;
        } else
            system($cmd, $return);

        return $return;
    }

    #endregion


    #region PHP

    public function php(string $cmd, string $app = App, string $type = self::type['default']): void
    {
        $path = Root . '/excmd/asrorz.php';
        $path = ZFileHelper::normalizePath($path);

        $cmd = "php {$path} {$cmd}";;
        $cmd .= ' --app=' . $app;
        
        //    vd($cmd, $type);
        $this->exec($cmd, $type);
    }


    public function code(string $cmd, bool $isBack = false)
    {
        $cmd = '@php -r "' . $cmd . ';";';
        $this->exec($cmd, $isBack);
    }


    #endregion

    #region Utils


    private function add(string $cmd)
    {

        if (!empty($this->cmd))
            $this->cmd .= ' && ';

        $this->cmd .= $cmd;
    }

    public function chdir(string $dir, bool $isCd = false)
    {
        if ($isCd) {
            $this->add('cd /d "' . $dir . '"');
            $this->add('cd');
        } else
            chdir($dir);
    }


    #endregion


    #region Service

    public function serviceRun(int $id)
    {
        $model = CoreQueue::findOne($id);

        $item = new ServiceItem();
        $item->namespace = $model->namespace;
        $item->service = $model->service;
        $item->method = $model->method;
        $item->args = $model->args;

        return $this->service($item);
    }

    public function service(ServiceItem $item, $value = null, $parentValue = null)
    {
        $disabled = [
            'Auth',
            'Rbac',
        ];

        $service = $item->service;
        $method = $item->method;

        if (ZArrayHelper::isIn($service, $disabled))
            throw new ZException('Cannot execute disabled service');

        $namespace = $item->namespace;
        $args = $item->args;

        if (!empty($args)) {
            $args = explode('|', $args);
        } else {
            $args = [];
        }

        if (!empty($parentValue))
            $args[] = $parentValue;

        if (!empty($value))
            $args[] = $value;

        $app = ZVarDumper::ajaxValue($item->App);

        if ($app)
            return Az::$app->App->$namespace->$service->$method(...$args);
        else
            return Az::$app->$namespace->$service->$method(...$args);
    }

    #endregion

    #region Queue

    public function addQueue(ServiceItem $item)
    {
        $model = new CoreQueue();
        $model->app = $item->app;
        $model->namespace = $item->namespace;
        $model->service = $item->service;
        $model->args = $item->args;
        $model->method = $item->method;
        $model->status = CoreQueue::status['queue'];
        $model->session = Az::$app->cores->session->getCookieSession();
        $model->name = $item->app . '>' . $item->namespace . '>' . $item->service . '>' . $model->args = $item->args;

        if ($model->save()) {
            if (!$item->delay) {
                $this->runQueue($model->id);
                $model->status = CoreQueue::status['finished'];
                $model->save();
            }
            return true;
        }
        return false;
    }

    public function runQueue($id)
    {
//        Az::$app->process->amphp->runQueue1('cores/queue/run', $id);
        Az::$app->process->symfonyProcess->php('cores/queue/run', $id);
    }

    public function runAllQueue()
    {
        $commands = [];
        $models = CoreQueue::find()
            ->where([
                'status' => CoreQueue::status['queue']
            ])->all();
        if ($models) {
            foreach ($models as $model) {
                $commands[$model->id] = $model->args;
            }
            Az::$app->process->amphp->runQueue($commands);
        }
    }

    public function clearQueue($time)
    {
        $time = QueueController::time[$time];

        $queues = CoreQueue::find()->where(['status' => CoreQueue::status['finished']])->all();
        foreach ($queues as $queue) {
            if (strtotime($queue->created_at) < strtotime("-{$time}")) {
                $queue->delete();
            }
        }
    }

    public function updateQueueCmd($id, $string, $pid, $status = CoreQueue::status['process'])
    {
        $model = CoreQueue::findOne($id);

        $model->cmd .= $string;
        $model->pid = $pid;
        $model->status = $status;
        $model->save();
    }


    public function itemHttp()
    {
        $namespace = $this->httpGet('namespace');
        $service = $this->httpGet('service');
        $method = $this->httpGet('method');
        $args = $this->httpGet('args');

        /*   vd($this->httpGet());
           vdd($this->httpGet('args'));*/

        $App = $this->httpGet('App');
        $delay = $this->httpGet('delay');

        if (empty($namespace) || empty($service) || empty($method))
            throw new ZException(Az::l('Отсутствуют необходимые параметры!'));

        $item = new ServiceItem();

        $item->namespace = $namespace;
        $item->service = $service;
        $item->method = $method;
        $item->App = $App;
        $item->args = $args;
        $item->delay = $delay;

        //vdd($item);
        return $item;
    }


    #endregion


}
