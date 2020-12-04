<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\vade;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use Hashids\Hashids;

class ZQrCode extends ZFrame
{

    /* @var Code $_model ; */
    //public $code;

    public $_Qr = ['Q','W','E','R','T','Y','U','I','O','P','A','S','D','F','G','H','J','K','L','Z','X','C','V','B','N','M'];
    public $_Ta = ['Q','A','Z','W','S','X','E','D','C','R','F','V','T','Y'];
    public $_tindexes = 0;

    public function getTindexes()
    {
        $_tindexes = $this->_tindexes;
        ++$_tindexes;
        $_tindexes %= 14;

        return $_tindexes;
    }

    public function GetQR($pin) :string
    {
        $tindexes = $this->getTindexes();
        $result = [];
        $result[0] = $this->_Ta[$tindexes];

        $_pin = (string)(999999999-$pin);

        $_pin = substr($_pin, 4, 4) . substr($_pin, 0, 4);
        $__pin = str_split($_pin);

        $this->_TaSwap($__pin, $tindexes);

        $t = $this->_ConvertStringNumberToQR($__pin, 0);
        $result[1] = $t[0];
        $result[2] = $t[1];
        $result[3] = $t[2];

        $t = $this->_ConvertStringNumberToQR($__pin, 4);
        $result[4] = $t[0];
        $result[5] = $t[1];
        $result[6] = $t[2];

        return implode($result);
    }

    public function _Swap($pin, $n)
    {
        $a = $pin[$n * 2];

        $pin[$n * 2] = $pin[$n * 2 + 1];

        $pin[$n * 2 + 1] = $a;
    }

    public function _TaSwap($pin, $ta)
    {
        switch ($ta)
        {
            case 0:
                $this->_Swap($pin, 0);
                break;
            case 13:
                $this->_Swap($pin, 1);
                break;
            case 12:
                $this->_Swap($pin, 2);
                break;
            case 3:
                $this->_Swap($pin, 3);
                break;
            case 4:
                $this->_Swap($pin, 0);
                $this->_Swap($pin, 1);
                break;
            case 5:
                $this->_Swap($pin, 1);
                $this->_Swap($pin, 2);
                break;
            case 6:
                $this->_Swap($pin, 2);
                $this->_Swap($pin, 3);
                break;
            case 7:
                $this->_Swap($pin, 0);
                $this->_Swap($pin, 2);
                break;
            case 8:
                $this->_Swap($pin, 0);
                $this->_Swap($pin, 3);
                break;
            case 9:
                $this->_Swap($pin, 1);
                $this->_Swap($pin, 3);
                break;
            case 10:
                $this->_Swap($pin, 0);
                $this->_Swap($pin, 1);
                $this->_Swap($pin, 2);
                break;
            case 11:
                $this->_Swap($pin, 0);
                $this->_Swap($pin, 2);
                $this->_Swap($pin, 3);
                break;
            case 2:
                $this->_Swap($pin, 1);
                $this->_Swap($pin, 2);
                $this->_Swap($pin, 3);
                break;
            case 1:
                $this->_Swap($pin, 0);
                $this->_Swap($pin, 1);
                $this->_Swap($pin, 2);
                $this->_Swap($pin, 3);
                break;

        }
    }

    public function downgrade($num)
    {
        if ($num > 26) $num = (int)abs($num / 2);

        if ($num > 26) $num = $this->downgrade($num);

        return $num;
    }

    public function _GetQRIndex($c)
    {
        $i = 0;
        foreach ($this->_Qr as $item)
        {
            if ($c == $item) { return $i; }

            $i++;
        }

        throw new \Exception("Invalid QR");
    }

    public function _ConvertStringNumberToQR($sn, $index)
    {
        $_t1 = 1000 * ($sn[$index]-48) + 100 * ($sn[$index+1]-48) + 10 * ($sn[$index + 2]-48) + ($sn[$index + 3]-48);

        $a0 = abs($_t1 % 26);
        $a0_0 = abs($_t1 / 26);
        $a1 = abs($a0_0 % 26);
        $a2 = abs($a0_0 / 26);
        $a1 = $this->downgrade($a1);
        $a2 = $this->downgrade($a2);

        $c = [$this->_Qr[$a2], $this->_Qr[$a1], $this->_Qr[$a0]];

        return $c;
    }

    public function run($iSerialNum = null)
    {
        Az::start(__FUNCTION__);

        $hashids = new Hashids('', 6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'); // pad to
        //return $this->GetQR($iSerialNum);

        return $hashids->encode($iSerialNum);

        //if ($this->model->save())
            // Az::debug($this->model->id, 'OrderStatus::Sgenerirovan');

        Az::end();

        return true;
    }

}
