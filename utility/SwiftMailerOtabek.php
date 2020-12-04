<?php

namespace zetsoft\service\utility;


use yii\helpers\Url;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use \Swift_SmtpTransport;
use \Swift_Mailer;
use \Swift_Message;
use Swift_Plugins_ThrottlerPlugin;

require Root . '/vendori/utility/ALL/vendor/autoload.php';

/**
 * Class SwiftMailer
 * Swift Mailer is a component based library for sending e-mails from PHP applications.
 * @package zetsoft\service\utility
 * @author NurbekMakhmudov
 * https://github.com/swiftmailer/swiftmailer
 * https://packagist.org/packages/swiftmailer/swiftmailer
 * https://swiftmailer.symfony.com/docs/introduction.html
 */
class SwiftMailerOtabek extends ZFrame
{

    //start|NurbekMakhmudov|2020-10-17

    #region Vars

    private Swift_SmtpTransport $swiftSmtpTransport;
    private Swift_Mailer $swiftMailer;
    private Swift_Message $swiftMessage;

    private $mailHost;
    private $mailUsername;
    private $mailPassword;
    private $mailPort;
    private $mailTitle;
    private $mailEncryption;
    private $appName;

    public $antiFlood = false;

    #endregion

    /**
     * initialization
     */
    public function init()
    {
        parent::init();

        $this->appName = $this->bootEnv('appName');
        $this->mailHost = $this->bootEnv('mailHost');
        $this->mailUsername = $this->bootEnv('mailUsername');
        $this->mailPassword = $this->bootEnv('mailPassword');
        $this->mailPort = $this->bootEnv('mailPort');
        $this->mailTitle = $this->bootEnv('mailTitle');
        $this->mailEncryption = $this->bootEnv('mailEncryption');

        $this->swiftSmtpTransport = new Swift_SmtpTransport(
            $this->mailHost, $this->mailPort, $this->mailEncryption
        );

        $this->swiftSmtpTransport->setUsername($this->mailUsername);
        $this->swiftSmtpTransport->setPassword($this->mailPassword);

        $this->swiftMailer = new Swift_Mailer($this->swiftSmtpTransport);

        $this->swiftMessage = new Swift_Message();
    }


    /**
     * @param $mail
     * @param $password
     * @param string $name
     */
    public function auth($mail, $password, $name = '')
    {
        try {
            $baseUrl = 'http://eyuf.zetsoft.uz/';
            $verificationCode = Az::$app->guid->sGuid->create();

            $body = "Мы рады, что вы с нами!"
                . "\n\nЛогин: " . $mail . "\nПароль: " . $password
                . "\n\nПодтвердить email: " . $baseUrl . "?verificationCode=" . $verificationCode . "\n\n"
                . "Если вы не регистрировались на сайте " . $baseUrl . ", просто проигнорируйте данное письмо.";

            $message = $this->swiftMessage
                ->setSubject($this->appName)
                ->setFrom([$this->mailUsername => $this->appName])
                ->setTo([$mail => $name])
                ->setBody($body);

            return $this->swiftMailer->send($message);

        } catch (\Exception $e) {
            return null;
        }
    }

    //start|Otabek|2020-10-17
    public function tdecoratorPlugin($replacements = [])
    {

        $decorator = new Swift_Plugins_DecoratorPlugin($replacements);

        $this->swiftMailer->registerPlugin($decorator);

    }

    public function throttlerPlugin($amount = null, $bytes = null)
    {

        // Rate limit to 100 emails per-minute
        $this->swiftMailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $amount, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));

        // Rate limit to 10MB per-minute
        $this->swiftMailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
            $bytes, Swift_Plugins_ThrottlerPlugin::BYTES_PER_MINUTE
        ));
    }


    //end|OtabekKarimov|2020-10-17
    #region Examples

    public function authExample()
    {
        $res = $this->auth('confirmationemail@yandex.ru', 'Pass12345');
//        $res = $this->auth('eyufconfirmationemail@gmail.com', '123Pass', 'Nurbek');
        vd($res);
    }

    #endregion


    //end|NurbekMakhmudov|2020-10-17

}