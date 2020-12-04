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

namespace zetsoft\service\webs;


use yii\base\Exception;
use yii\base\UserException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use zetsoft\dbitem\core\CpasTrackerItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;

class Errors extends ZFrame
{

    public $viewFile = '/webhtm/core/error/exception.php';


    private $message;
    private $name;
    private $code;
    private $exception;


    public function run()
    {

        $this->collect();

        $exception = Az::$app->errorHandler->exception;

//vdd($exception);

        if ($exception !== null) {
            return $this->require($this->viewFile, [
                'exception' => $exception,
                'handler' => Az::$app->errorHandler
            ]);
        }

        switch (true) {
            /*    case Az::$app->utility->mains->urlRest():
                    return $this->_rest();
                    break;*/

            case !$this->userIsGuest():
                return $this->_user();
                break;

            default:
                return $this->_user();
                break;

        }


    }


    private function collect(): void
    {

        /**
         *
         * Get Exception
         */

        if (($this->exception = Az::$app->errorHandler->exception) === null)
            $this->exception = new NotFoundHttpException('Страница не найдена в Системе...');


        /**
         *
         * Get Message
         */


        if ($this->exception instanceof UserException) {
            $this->message = $this->exception->getMessage();
        } else
            $this->message = 'Обнаружены исключения в работе системы...';


        /**
         *
         * GEt Code
         */

        if ($this->exception instanceof HttpException) {
            $this->code = $this->exception->statusCode;
        } else
            $this->code = $this->exception->getCode();


        /**
         *
         * Get Name
         */

        if ($this->exception instanceof Exception) {
            $this->name = $this->exception->getName();
        } else {
            $this->name = $this->defaultName;
        }

        $this->name .= " (#$this->code)";


    }


    private function _rest()
    {
        Az::$app->response->format = Response::FORMAT_JSON;
        return [
            'exception' => $this->exception,
            'message' => $this->message,
            'name' => $this->name,
        ];
    }

    public function _user()
    {
        return $this->require($this->viewFile, [
            'exception' => $this->exception,
            'message' => $this->message,
            'name' => $this->name,
        ]);

    }


}
