<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\ALL;

use zetsoft\service\bot\Botman;
use zetsoft\service\bot\Telegrambot;
use zetsoft\service\bot\TgBotman;
use zetsoft\service\bot\UserStorage;
use yii\base\Component;



/**
 *
* @property Botman $botman
* @property Telegrambot $telegrambot
* @property TgBotman $tgBotman
* @property UserStorage $userStorage

 */

class Bot extends Component
{

    
    private $_botman;
    private $_telegrambot;
    private $_tgBotman;
    private $_userStorage;

    
    public function getBotman()
    {
        if ($this->_botman === null)
            $this->_botman = new Botman();

        return $this->_botman;
    }
    

    public function getTelegrambot()
    {
        if ($this->_telegrambot === null)
            $this->_telegrambot = new Telegrambot();

        return $this->_telegrambot;
    }
    

    public function getTgBotman()
    {
        if ($this->_tgBotman === null)
            $this->_tgBotman = new TgBotman();

        return $this->_tgBotman;
    }
    

    public function getUserStorage()
    {
        if ($this->_userStorage === null)
            $this->_userStorage = new UserStorage();

        return $this->_userStorage;
    }
    


}
