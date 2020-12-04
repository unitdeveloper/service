<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\market;


use zetsoft\models\calls\CallsStatus;
use zetsoft\system\kernels\ZFrame;

class CallCenter extends ZFrame
{

    public $name;
    public $user_id;
    public $time;
    public $status;
    public $callCenter;

    public function init()
    {

        $this->callCenter = collect(\zetsoft\models\user\User::find()->where(['role' => 'agent'])->asArray()->all());

        parent::init();
    }


    #region test

    public function test()
    {

        $callcentr = $this->getOperatorStats();
        vd($callcentr);
    }

    #endregion test


    #region getOperatorStats

    public function getOperatorStats()
    {

        $callcentr = $this->callCenter;

        $forms = [];

        foreach ($callcentr as $call) {

            $form = new CallsStatus();
            $form->name = $call['name'];
            $form->user_id = $call['user_id'];
            $form->time = $call['time'];
            $form->status = $call['status'];
            $forms[] = $form;

        }

        return $forms;

    }

    #endregion

}
