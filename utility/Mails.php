<?php

/**
 *
 *
 * Author:  Sunnat, MurodovMirbosit
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 * https://www.php.net/manual/en/function.mail
 */

namespace zetsoft\service\utility;


use zetsoft\models\chat\ChatMail;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;


/**
 * Class    Mails
 * @package zetsoft\service\utility
 *
 * https://swiftmailer.symfony.com/docs/introduction.html
 * https://swiftmailer.symfony.com/docs/plugins.html
 */
class Mails extends ZFrame
{


    #region Vars

    public $pluginDecorator = false;
    public $pluginLogger = false;

    #endregion

    #region Core
    public $view;
    public $data;
    public $to;
    #endregion

    #region DAta
    public $subject;
    public $file;
    public $ReplyTo;

    #endregion


    public function run()
    {

        $mailer = Az::$app->mailer
            ->compose(Az::$app->getView(), $this->data)
            ->setTo($this->to)
            ->setSubject($this->subject);

        if (!empty($this->ReplyTo))
            $mailer->setReplyTo($this->ReplyTo);

        if (!empty($this->file))
            $mailer->attach($this->file);

        // ->setTextBody('Plain text content')
        // ->setHtmlBody('<b>HTML content</b>')

        $mailer->send();

    }

    public function multiple()
    {
        ///      All Users   ///
        /*$messages = [];
        foreach ( $users as $user ) {
            $messages[] = Yii::$app->mailer->compose()
                ->setTo($user->email);
        }
        Yii::$app->mailer->sendMultiple($messages);*/

    }

    public function all($title, $data, $userId = null)
    {

        if ($userId === null)
            $userId = $this->userIdentity()->id;
        /** @var User $user */
        $user = User::findOne($userId);
        $to = $user->email;
        $text = ZVarDumper::export($data);

        $message = ChatMail::find()
            ->where([
                'user_id' => $userId,
                'title' => $title,
                'text' => $text,
                'to' => $to
            ])
            ->limit(1)
            ->one();

        if ($message !== null) return false;

        $message = new ChatMail();
        $message->user_id = $userId;
        $message->title = $title;
        $message->time = Az::$app->cores->date->dateTime();
        $message->text = $text;
        $message->to = $to;


        $data = [
            'text' => "Все ваши документы были приняты отделом мониторинга.",
        ];
        $subject = "Cообщение";

        $view = "test";

        if ($message->save()) {
            $this->mailAll($subject, $userId, $view, $data, "");
            return true;
        } else
            return false;
    }

}
