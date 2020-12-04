<?php

/**
 *
 *
 * Author: Mirshod Ibodov && Dilmurod Axmadov
 *
 */

namespace zetsoft\service\calls;


use User;
use zetsoft\former\call_center\CallStats;
use zetsoft\former\calls\CallsStatsAgentForm;
use zetsoft\former\calls\CallsStatsForm;
use zetsoft\models\calls\CallsCdr;
use zetsoft\models\calls\CallsStatus;
use zetsoft\models\calls\CallsStatusTime;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;


class StatsMerge extends ZFrame
{

    /*public function agentWork($agentId, $status = null, $dateOne = null, $dateTwo = null)
    {

        if ($dateOne === null)
            $dateOne = Az::$app->cores->date->dateTime('-24 hours');

        if ($dateTwo === null)
            $dateTwo = Az::$app->cores->date->dateTime();

        $return = [];
        $models = CallsStatus::find()
            ->where([
                'between', 'created_at', $dateOne, $dateTwo
            ])
            ->all();
        $user_ids = ZArrayHelper::getColumn($models, 'user_id');
        $ids = [];
        foreach ($user_ids as $user_id) {
            if (!ZArrayHelper::isIn($user_id, $ids)) {
                $ids[] = $user_id;
            }
        }
        $statuses = [];
        foreach ($models as $model) {
            if (!ZArrayHelper::getValue($statuses, [$model->user_id,$model->status]))
                $statuses[$model->user_id][$model->status] = strtotime($model->time);
            else
                $statuses[$model->user_id][$model->status] += strtotime($model->time);

        }
        vd($statuses);
        vdd(date('H:i:s', $statuses[103]['online']));
        return $return;

    }*/

    public function agentWorkedTime($from = null, $to = null)
    {

        if ($from === null)
            $from = Az::$app->cores->date->dateTime('-24 hours');

        if ($to === null)
            $to = Az::$app->cores->date->dateTime();

        $data = [];

        $agents = \zetsoft\models\user\User::find()
            ->where([
                'role' => 'agent'
            ])
            ->all();

        foreach ($agents as $agent) {
            $elem = new CallsStatsAgentForm();
            $elem->name = $agent->id;
            $elem->online = $this->workHoursByStatus($agent->id, $from, $to, 'online');
            $elem->offline = $this->workHoursByStatus($agent->id, $from, $to, 'offline');
            $elem->away = $this->workHoursByStatus($agent->id, $from, $to, 'away');
            $elem->dnd = $this->workHoursByStatus($agent->id, $from, $to, 'dnd');
            $elem->lunch = $this->workHoursByStatus($agent->id, $from, $to, 'lunch');

            $data[] = $elem;
        }

        return $data;
    }

    public function workHoursByStatus($user_id, $from, $to, $status)
    {
        $records = CallsStatusTime::find()
            ->where([
                'between', 'date', $from, $to
            ])
            ->andWhere([
                'user_id' => $user_id,
            ])
            ->asArray()
            ->all();

        $time = '00:00:00';
        foreach ($records as $record) {
            $time = Az::$app->cores->date->addTime($time, $record[$status]);
        }

        return $time;

    }

    public function operatorCalls($operatorNumber, $from = null, $to = null)
    {

        if ($from === null)
            $from = Az::$app->cores->date->dateTime('-24 hours');

        if ($to === null)
            $to = Az::$app->cores->date->dateTime();

        $query = CallsCdr::find()
            ->where([
                'between', 'calldate', $from, $to
            ])
            ->andWhere([
                'src' => $operatorNumber
            ])
            ->asArray()
            ->all();
        return $query;
    }
}
