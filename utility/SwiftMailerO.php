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


require Root . '/vendori/utility/ALL/vendor/autoload.php';


use Swift_SmtpTransport;
use Swift_Message;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use Swift_Mailer;
use Swift_Plugins_ThrottlerPlugin;


/**
 * Class    Mails
 * @package zetsoft\service\utility
 *
 * https://swiftmailer.symfony.com/docs/introduction.html
 * https://swiftmailer.symfony.com/docs/plugins.html
 */
class SwiftMailerO extends ZFrame
{

    //start|Otabek|2020-10-19

    #region Vars
    public $username;
    public $password;
    public $host;
    public $port;
    public $encryption;
    public $messageSubject;
    public $receiversMail;
    public $receiversPassword;
    public $messageBody;
    public Swift_SmtpTransport $transport;
    public $readyMessage;
    public Swift_Mailer $mailer;
    public $MBPerMinute;
    public $amountPerMinute;

    #endregion


    #region Functions

    public function sendMail($receiversMail, $vCode, $amountPerMinute = null, $MBPerMinute = null)
    {
        $this->configMail($receiversMail, $vCode, false, $amountPerMinute, $MBPerMinute);
        $this->configMail($receiversMail, $vCode, true, $amountPerMinute, $MBPerMinute);
    }

    public function configMail($receiversMail, $vCode, $google = false, $amountPerMinute = null, $MBPerMinute = null)
    {
        if ($google == true) {
            $this->username = $this->bootEnv("mailGoogleUsername");
            $this->password = $this->bootEnv("mailGooglePassword");
            $this->host = $this->bootEnv("mailGoogleHost");
            $this->port = $this->bootEnv("mailGooglePort");
            $this->encryption = $this->bootEnv("mailGoogleEncryption");
            $this->messageSubject = $this->bootEnv("mailGoogleTitle");
            $this->encryption = $this->bootEnv("mailGoogleEncryption");

        } else {
            $this->username = $this->bootEnv("mailYandexUsername");
            $this->password = $this->bootEnv("mailYandexPassword");
            $this->host = $this->bootEnv("mailYandexHost");
            $this->port = $this->bootEnv("mailYandexPort");
            $this->encryption = $this->bootEnv("mailYandexEncryption");
            $this->messageSubject = $this->bootEnv("mailYandexTitle");
            $this->encryption = $this->bootEnv("mailYandexEncryption");
        }
        $this->receiversMail = $receiversMail;
        $this->amountPerMinute = $amountPerMinute;
        $this->MBPerMinute = $MBPerMinute;

        $this->transport = $this->createTransport();
        $this->mailer = $this->createMailer();
        $this->throttlerPlugin();


        $baseUrl = $this->urlGetBase();

        $this->messageBody = $body = "Мы рады, что вы с нами!"
            . "\n\nЛогин: " . $receiversMail . "\n"
            . "\n\nПодтвердить email: " . $baseUrl . "?verificationCode=" . $vCode
            . "Если вы не регистрировались на сайте " . $baseUrl . ", просто проигнорируйте данное письмо.";

        $this->readyMessage = $this->createMessage();
        $result = $this->sendMessage();

    }

    #region GETfunctions
    public function getEncryption()
    {
        return $this->encryption;
    }

    public function getReadyMessage()
    {
        return $this->readyMessage;
    }

    public function getMessageSubject()
    {
        return $this->messageSubject;
    }

    public function getTransport(): Swift_SmtpTransport
    {
        return $this->transport;
    }

    public function getMailer(): Swift_Mailer
    {
        return $this->mailer;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getReceiversMail()
    {
        return $this->receiversMail;
    }

    public function getMessageBody()
    {
        return $this->messageBody;
    }

    public function getMBPerMinute()
    {
        return $this->MBPerMinute;
    }

    public function getAmountPerMinute()
    {
        return $this->amountPerMinute;
    }
    #endregion

    // Create the Transport
    public function createTransport()
    {
        return (new Swift_SmtpTransport($this->getHost(), $this->getPort(), $this->getEncryption()))
            ->setUsername($this->getUsername())
            ->setPassword($this->getPassword());
    }

    // Create the Mailer using your created Transport
    public function createMailer()
    {
        return new Swift_Mailer($this->getTransport());
    }

    // Create a message
    public function createMessage()
    {
        return (new Swift_Message($this->getMessageSubject()))
            ->setFrom($this->getUsername())
            ->setTo($this->getReceiversMail())
            ->setBody($this->getMessageBody());
    }

    // Send the message
    public function sendMessage()
    {
        return $this->getMailer()->send($this->getReadyMessage());
    }

    public function throttlerPlugin()
    {
        if (isset($this->amountPerMinute)) {
            // Rate limit  emails per-minute
            $this->mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
                $this->amountPerMinute, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));
        }

        if (isset($this->MBPerMinute)) {
            // Rate limit emails per-minute
            $this->mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
                ($this->getMBPerMinute() * 1024 * 1024), Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));
        }

    }

    #endregion

    #region Examples
    public function runExample()
    {
        $vCode = "62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4";
        $this->sendMail("nurmax1993@gmail.com", $vCode, 10, 10);
    }
    #endregion


    //end|Otabek|2020-10-17
}
