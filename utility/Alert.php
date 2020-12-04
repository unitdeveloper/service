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

use zetsoft\models\auto\ChatNotify;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\notifier\ZKAlertWidget;

class Alert extends ZFrame
{

    #region Alert


    public function all($title, $data, $type)
    {

        if (!is_string($data))
            $data = ZVarDumper::export($data);

        echo ZKAlertWidget::widget([
            'config' => [
                'type' => $type,
                'title' => $title,
                'body' => $data,
                'delay' => 0,
                'isShowSeparator' => true,
            ]
        ]);

    }



    #endregion


    #region Types

    public function success($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_SUCCESS);
    }

    public function info($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_INFO);
    }

    public function warning($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_WARNING);
    }

    public function primary($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_PRIMARY);
    }

    public function default($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_DEFAULT);
    }

    public function danger($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_DANGER);
    }

    public function custom($title, $data)
    {
        $this->all($title, $data, \kartik\alert\Alert::TYPE_CUSTOM);
    }

  
    #endregion


}
