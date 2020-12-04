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
use zetsoft\service\App\eyuf\User;
use zetsoft\system\kernels\ZFrame;

class OperatorStats extends ZFrame
{

    public $operators;
    public $time;
    public $status;

    public function init()
    {

        $this->operators = collect(\zetsoft\models\user\User::find()->where(['role' => 'agent'])->asArray()->all());

        parent::init();
    }


    #region test

    public function test()
    {

        // $this->getRejectCausesTest();
        vd($this->courierReportTest());
    }

    #endregion test


    #region getOperatorStats

    public function getOperatorStats()
    {

        
    }

    #endregion

}
