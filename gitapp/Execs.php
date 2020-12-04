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





    public function gitCommit($message = null)
    {

        if ($message === null)
            $message = Az::$app->cores->date->dateTime();

        $this->chdir(Root);

        $this->add('git add .');
        $this->add('git commit -m "' . $message . '"');

        $this->exec($this->cmd);
    }


}
