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

use zetsoft\service\ratchet\Chat;
use yii\base\Component;



/**
 *
* @property Chat $chat

 */

class Ratchet extends Component
{

    
    private $_chat;

    
    public function getChat()
    {
        if ($this->_chat === null)
            $this->_chat = new Chat();

        return $this->_chat;
    }
    


}
