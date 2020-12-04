<?php

/**
 *
 *
 * Сreate by : Dilmurod Axmadov
 * Refactored By: Xolmat Ravshanov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\calls;


use zetsoft\models\App\market\db2\Cdr;
use zetsoft\models\calls\CallsCdr;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class FillCdr extends ZFrame
{

    /* @var CallsCdr $callCdr */

    public $callCdr;


    /* @var array $calls */

    public $calls;


    public $startDate;


    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $this->all();
    }

    public function real()
    {

        $file = Root . '/scripts/runner/runx.exe';
        $root = Root;
        $php = "d:/Develop/Projects/ALL/server/php7/7_44/php.exe";
        $cmd = "caller/fill/run";

        $line = ' 300 "{php}" {root}/excmd/asrorz.php {cmd}';

        $line = strtr($line, [
            '{php}' => $php,
            '{root}' => $root,
            '{cmd}' => $cmd,
        ]);

        Az::$app->utility->execs->exec($file . $line, true);
    }


    #region Time
    public function hour()
    {
   
        $calldate = $this->sessionGet('writeSessionCdr');
        return $calldate;

    }

    #endregion


    public function all()
    {
        $this->startDate = Az::$app->cores->date->dateTime();

        $query = Cdr::find();

        if (!$this->hour()) {
            $calls = $query
                ->asArray()
                ->all();
        } else {
            $calls = $query
                ->where([
                    '>=', 'calldate', $this->hour()
                ])
                ->asArray()
                ->all();
        }

      $this->sessionSet('writeSessionCdr', $this->startDate);

        if (!empty($calls)) {

            foreach ($calls as $call) {
                /** @var CallsCdr $exists */
                $exists = CallsCdr::find()
                    ->where([
                        'calldate' => $call['calldate'],
                        'uniqueid' => (int)$call['uniqueid']
                    ])
                    ->exists();

                if (!$exists) {
                    Az::trace("{$this->hour()} | CREATE | ID = {$call['uniqueid']}");

                    $model = new CallsCdr();

                    Az::$app->calls->asteriskInfo->getRecordFileByName($call['recordingfile']);

                    $this->_model($model, $call);

                } else
                    Az::trace("{$this->hour()} | ALREADY EXISTS | ID = {$call['uniqueid']}");
            }

        } else
            echo 'Bazada hech narsa yoq ' . EOL;
    }


    protected function _model($callCdr, array $call)
    {
        $absolutePath = Az::$app->calls->asteriskInfo->absolutePath($call['recordingfile']);

        if (!empty($absolutePath)) {
            $callCdr->recordingfile = '/audioz/' . App . '/call/' . $absolutePath . '/' . $call['recordingfile'];
        } else
            $callCdr->recordingfile = $absolutePath;

        switch (true) {

            case !empty($call['userfield']):

                $modelShopOrder = ShopOrder::findOne($call['userfield']);

                if ($modelShopOrder !== null) {

                    $user = User::findOne($modelShopOrder->operator);

                    if ($user !== null)
                        $callCdr->number = $user->number;

                    $callCdr->shop_order_id = $call['userfield'];
                }
                
                break;


            case !empty($call['accountcode']):

                $modelShopOrder = ShopOrder::findOne($call['accountcode']);

                if ($modelShopOrder !== null) {

                    $user = User::findOne($modelShopOrder->operator);

                    if ($user !== null)
                        $callCdr->number = $user->number;

                    $callCdr->shop_order_id = $call['accountcode'];

                }

                break;

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
