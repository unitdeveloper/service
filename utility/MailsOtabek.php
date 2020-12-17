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


require Root . '/vendors/utility/ALL/vendor/autoload.php';


use Swift_SmtpTransport;
use Swift_Message;
use zetsoft\system\kernels\ZFrame;
use Swift_Mailer;
use Swift_Plugins_ThrottlerPlugin;


/**
 * Class MailsOtabek
 * @package zetsoft\service\utility
 *
 * https://swiftmailer.symfony.com/docs/introduction.html
 * https://swiftmailer.symfony.com/docs/plugins.html
 */
class MailsOtabek extends ZFrame
{


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

        $servername = "Yandex";
        if ($google) {
            $servername = "Google";
        }

        if ($google) {

        }

        $this->username = $this->bootEnv("mail".$servername."Username");
        $this->password = $this->bootEnv("mail".$servername."Password");
        $this->host = $this->bootEnv("mail".$servername."Host");
        $this->port = $this->bootEnv("mail".$servername."Port");
        $this->encryption = $this->bootEnv("mail".$servername."Encryption");
        $this->messageSubject = $this->bootEnv("mail".$servername."Title");
        $this->encryption = $this->bootEnv("mail".$servername."Encryption");

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

        #region GETFunctions
        public  function getEncryption()
        {
            return $this->encryption;
        }

        public  function getReadyMessage()
        {
            return $this->readyMessage;
        }

        public  function getMessageSubject()
        {
            return $this->messageSubject;
        }

        public  function getTransport(): Swift_SmtpTransport
        {
            return $this->transport;
        }

        public  function getMailer(): Swift_Mailer
        {
            return $this->mailer;
        }

        public  function getUsername()
        {
            return $this->username;
        }

        public  function getPassword()
        {
            return $this->password;
        }

        public  function getHost()
        {
            return $this->host;
        }

        public  function getPort()
        {
            return $this->port;
        }

        public  function getReceiversMail()
        {
            return $this->receiversMail;
        }

        public  function getMessageBody()
        {
            return $this->messageBody;
        }

        public  function getMBPerMinute()
        {
            return $this->MBPerMinute;
        }

        public  function getAmountPerMinute()
        {
            return $this->amountPerMinute;
        }

        #endregion

        // Create the Transport
        public  function createTransport()
        {
            return (new Swift_SmtpTransport($this->getHost(), $this->getPort(), $this->getEncryption()))
                ->setUsername($this->getUsername())
                ->setPassword($this->getPassword());
        }

        // Create the Mailer using your created Transport
        public  function createMailer()
        {
            return new Swift_Mailer($this->getTransport());
        }

        // Create a message
        public  function createMessage()
        {
            return (new Swift_Message($this->getMessageSubject()))
                ->setFrom($this->getUsername())
                ->setTo($this->getReceiversMail())
                ->setBody($this->getMessageBody());
        }

        // Send the message
        public  function sendMessage()
        {
            return $this->getMailer()->send($this->getReadyMessage());
        }

        public  function throttlerPlugin()
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
        public  function runExample()
        {
            $vCode = "62ffca3230ee975f568a4ed284b9a6115c6c279530ea18c2fb1664f071713dc4";
            $this->sendMail("otabekkarimov95@yandex.com", $vCode);
        }
        #endregion
    }
