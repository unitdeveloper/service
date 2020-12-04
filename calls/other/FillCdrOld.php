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


use DateInterval;
use DateTime;
use Fusonic\Linq\Linq;
use React\ChildProcess\Process;
use React\EventLoop\Factory;
use Yii;
use yii\helpers\ArrayHelper;
use zetsoft\models\App\eyuf\db2\Cdr;
use zetsoft\models\calls\CallsCdr;
use zetsoft\models\calls\CallsCel;
use zetsoft\models\shop\ShopOrder;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class FillCdrOld extends ZFrame
{

    /* @var CallsCdr $callCdr */
    public $callCdr;

    /* @var array $calls */
    public $calls;


    public $startDate;
    public $lastUpdate;


    #region Time

    /**
     *
     * Function  real
     * @param $time seconds  default 5 min
     */
    public function real($time = 300)
    {
        //$period = $time . ' seconds';
        //$period =  '24 hours';
        //$loop = Factory::create();
        //$loop->addPeriodicTimer($time, function (\React\EventLoop\TimerInterface $timer) use($period){
            $this->run($time);
        //});
        //$loop->run();
        //$this->startDate = $this->sessionGet('calldate');
    }

    public function hour($period)
    {
        $this->startDate = Az::$app->cores->date->dateTime("-$period");
        return $this->startDate;
    }

    public function day()
    {
        $this->startDate = Az::$app->cores->date->dateTime();
        return $this->startDate;
    }


    #endregion

    public function init()
    {
        parent::init();
    }


    public function run($time)
    {
        //echo date('Y-m-d H:i:s') . EOL;
        $this->all($time);
    }


    public function all($period)
    {

        $calls = Cdr::find()
            ->where([
                '>', 'calldate', $this->hour($period)
                //'calldate' => '2020-04-17 21:52:15',
            ])
            ->asArray()
            ->all();

        //vdd($calls);
        if (!empty($calls)) {
            foreach ($calls as $call) {

                //vdd($call['recordingfile']);
                //if ($call['recordingfile'] == 'external-310-909470755-20200417-215215-1587142335.93.wav') {

                /** @var CallsCdr $exists */
                $exists = CallsCdr::find()
                    ->where([
                        'calldate' => $call['calldate'],
                        'uniqueid' => $call['uniqueid']
                    ])
                    ->exists();

                if ($exists) {
                    Az::trace("{$this->hour($period)} | UPDATE | ID = {$call['uniqueid']} | {$this->day()}");
                    //echo 'if';
                    continue;
                    /*$model = CallsCdr::find()
                        ->where([
                            'calldate' => $call['calldate'],
                            'uniqueid' => $call['uniqueid']
                        ])
                        ->limit(1)
                        ->one();*/
                } else {
                    Az::trace("{$this->hour($period)} | CREATE | ID = {$call['uniqueid']} | {$this->day()}");
                    $model = new CallsCdr();
                    Az::$app->calls->asteriskInfo->getRecordFileByName($call['recordingfile']);
                    //echo 'else';
                }
                $this->_model($model, $call);
            }
        } else
            echo 'Bazada hech narsa yoq '. EOL;
    }


    protected function _model($callCdr, array $call)
    {
        //$call['recordingfile'] = 'internal-301-310-20200507-102508-1588829108.35.wav';
        //vdd($callCdr->recordingfile);
        //echo date('H:i:s',strtotime('2018-05-08 23:36:21 -30 minutes'));
        //$time->add(new DateInterval('PT' . -$minutes_to_add . 'M'));
        //$stamp = $time->format('Y-m-d H:i');
        //$a = $call['dst'];
        //$modelShop = "SELECT * FROM shop_order WHERE called_time BETWEEN $intervalold AND $interval";
        //$modelShop = "SELECT * FROM shop_order WHERE called_time BETWEEN '$intervalold' AND '$interval' AND contact_phone = '$a'";
        //$modelShop = ShopOrder::findBySql($modelShop)->one();
        //vdd (date('y-m-d H:i:s',strtotime($call['calldate']. ' -30 minutes')));
        //vdd($timeCall);
        //vdd($timeCall->modify("-2 minutes"));
        //var_dump($a->date);
        //$interval = date('y-m-d', strtotime($call['calldate'] . '+2 minutes'));
        //vdd($interval);
        //$timeCall = new DateTime($call['calldate']);

        $intervalold = (date('Y-m-d H:i:s', strtotime($call['calldate'] . '-2 minutes')));
        $interval = (date('Y-m-d H:i:s', strtotime($call['calldate'] . '+2 minutes')));
        $modelShop = ShopOrder::find()
            ->where(['between', 'called_time', $intervalold, $interval])
            ->andWhere(['contact_phone' => $call['dst']])
            ->one();


        $absolutePath = Az::$app->calls->asteriskInfo->absolutePath($call['recordingfile']);
        if (!empty($absolutePath)) {
            $callCdr->recordingfile = '/audioz/eyuf/call/' . $absolutePath . '/' . $call['recordingfile'];
        } else
            $callCdr->recordingfile = $absolutePath;


        if (empty($modelShop)) {
            $callCdr->shop_order_id = null;
        } else {
            $callCdr->shop_order_id = $modelShop->id;
        }
        
        $callCdr->calldate = $call['calldate'];
        $callCdr->clid = $call['clid'];
        $callCdr->src = $call['src'];
        $callCdr->dst = $call['dst'];
        $callCdr->dcontext = $call['dcontext'];
        $callCdr->channel = $call['channel'];
        $callCdr->dstchannel = $call['dstchannel'];
        $callCdr->lastapp = $call['lastapp'];
        $callCdr->lastdata = $call['lastdata'];
        $callCdr->duration = $call['duration'];
        $callCdr->billsec = $call['billsec'];
        $callCdr->disposition = $call['disposition'];
        $callCdr->amaflags = $call['amaflags'];
        $callCdr->accountcode = $call['accountcode'];
        $callCdr->uniqueid = $call['uniqueid'];
        $callCdr->userfield = $call['userfield'];
        $callCdr->did = $call['did'];
        $callCdr->cnum = $call['cnum'];
        $callCdr->cnam = $call['cnam'];
        $callCdr->outbound_cnum = $call['outbound_cnum'];
        $callCdr->outbound_cnam = $call['outbound_cnam'];
        $callCdr->dst_cnam = $call['dst_cnam'];
        $callCdr->linkedid = $call['linkedid'];
        $callCdr->sequence = $call['sequence'];
        $callCdr->peeraccount = $call['peeraccount'];

        if ($callCdr->isNewRecord) {
            if ($callCdr->save())
                Az::trace($callCdr->uniqueid, 'TblCall Success Saved! ID', 'ModelSave');

        } else {
            $aChangedAttributes = $callCdr->getDirtyAttributes();

            if (!empty($aChangedAttributes))
                if ($callCdr->save())
                    Az::trace($aChangedAttributes, 'TblCall Changes Success Saved!', 'ModelSave');

        }

    }

}
