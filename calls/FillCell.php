<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\calls;


use Fusonic\Linq\Linq;
use React\EventLoop\Factory;
use yii\helpers\ArrayHelper;
use zetsoft\models\App\market\db2\Cdr;
use zetsoft\models\App\market\db2\Cel;

use zetsoft\models\calls\CallsCdr;
use zetsoft\models\calls\CallsCel;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class FillCell extends ZFrame
{

    /* @var CallsCel $callCell */
    public $callCell;

    /* @var array $calls */
    public $calls;


    public $startDate;

    #region Time

    public function real()
    {

        $file = Root . '/scripts/runner/runx.exe';
        $root = Root;
        $php = "d:/Develop/Projects/ALL/server/php7/7_44/php.exe";
        $cmd = "caller/fill/cell";

        $line = ' 300 "{php}" {root}/excmd/asrorz.php {cmd}';

        $line = strtr($line, [
            '{php}' => $php,
            '{root}' => $root,
            '{cmd}' => $cmd,
        ]);

        Az::$app->utility->execs->exec($file . $line, true);
    }


    public function hour()
    {
        $eventTime = $this->sessionGet('writeSessionCel');
        return $eventTime;
    }
    #endregion


    #region ReactLoop
    public function realReact($time)
    {

        //$this->startDate = $this->sessionGet('calldate');

        $period = $time . ' seconds';
        //$period =  '24 hours';
        $loop = Factory::create();
        $loop->addPeriodicTimer($time, function (\React\EventLoop\TimerInterface $timer) use ($period) {
            $this->run($period);
        });
        $loop->run();
    }

    public function hourReact($period)
    {
        $this->startDate = Az::$app->cores->date->dateTime("-$period");
        return $this->startDate;
    }

    public function dayReact()
    {
        $this->startDate = Az::$app->cores->date->dateTime();
        return $this->startDate;
    }


    #endregion

    public function init()
    {
        parent::init();
    }


    public function run()
    {

        $this->all();

    }


    public function all()
    {
        $this->startDate = Az::$app->cores->date->dateTime('-2 minutes');

        $query = Cel::find();

        if (!$this->hour()) {
            $calls = $query
                ->asArray()
                ->all();
        } else {
            $calls = $query->where([
                '>=', 'eventtime', $this->hour()
            ])
                ->asArray()
                ->all();
        }

        $this->sessionSet('writeSessionCel', $this->startDate);

        if (!empty($calls)) {
            foreach ($calls as $call) {

                /** @var CallsCdr $exists */
                
                $exists = CallsCel::find()
                    ->where([
                        'eventtime' => $call['eventtime'],
                        'uniqueid' => $call['uniqueid'],
                        'eventtype' => $call['eventtype'],
                    ])
                    ->exists();

                if ($exists) {
                    Az::trace("{$this->hour()} | RECORD EXISTS | ID = {$call['uniqueid']} ");
                } else {
                    Az::trace("{$this->hour()} | CREATE | ID = {$call['uniqueid']}");
                    $model = new CallsCel();
                    $this->_model($model, $call);
                }
            }
        } else {
            echo 'Bazada hech narsa yoq' . EOL;
        }

    }

    protected function _model($callCell, array $call)
    {

        $modelCdr = CallsCdr::find()
            ->where([
                'uniqueid' => $call['uniqueid']
            ])
            ->orWhere([
                'linkedid' => $call['linkedid']
            ])
            ->one();

        if (!empty($modelCdr)) {
            $callCell->call_cdr_id = $modelCdr->id;
            $callCell->shop_order_id = $modelCdr->shop_order_id;
        } else {
            $callCell->shop_order_id = null;
            $callCell->call_cdr_id = null;
        }


        $callCell->eventtime = $call['eventtime'];
        $callCell->eventtype = $call['eventtype'];
        $callCell->cid_name = $call['cid_name'];
        $callCell->cid_num = $call['cid_num'];
        $callCell->cid_ani = $call['cid_ani'];
        $callCell->cid_rdnis = $call['cid_rdnis'];
        $callCell->cid_dnid = $call['cid_dnid'];
        $callCell->exten = $call['exten'];
        $callCell->context = $call['context'];
        $callCell->channame = $call['channame'];
        $callCell->appname = $call['appname'];
        $callCell->appdata = $call['appdata'];
        $callCell->amaflags = $call['amaflags'];
        $callCell->accountcode = $call['accountcode'];
        $callCell->uniqueid = $call['uniqueid'];
        $callCell->linkedid = $call['linkedid'];
        $callCell->peer = $call['peer'];
        $callCell->userdeftype = $call['userdeftype'];
        $callCell->extra = $call['extra'];


        if ($callCell->isNewRecord) {
            if ($callCell->save())
                Az::trace($callCell->uniqueid, 'TblCall Success Saved! ID', 'ModelSave');
        } else {

            $aChangedAttributes = $callCell->getDirtyAttributes();

            if (!empty($aChangedAttributes)) {
                if ($callCell->save())
                    Az::trace($aChangedAttributes, 'TblCall Changes Success Saved!', 'ModelSave');
            }

        }

    }

}
