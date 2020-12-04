<?php



namespace zetsoft\service\market;

use zetsoft\service\market\phpSerial;

use zetsoft\system\kernels\ZFrame;


class Fetch extends ZFrame
{

    public function test(){

        $serial = new phpSerial();

        $serial->phpSerial();

        // First we must specify the device. This works on both linux and windows (if
        // your linux serial device is /dev/ttyS0 for COM1, etc)
          $serial->deviceSet("COM4");
          $serial->confBaudRate("9600");

        // Then we need to open it
        $serial->deviceOpen();
       // $read = $serial->readPort();

         // vdd($read);

        // To write into
        $serial->sendMessage("Hello !");

        // Or to read from


        // If you want to change the configuration, the device must be closed

         // $serial->deviceClose();


    }

    public function test1()
    {

        // Let's start the class
        $serial = new PhpSerial;

     // First we must specify the device. This works on both linux and windows (if
    // your linux serial device is /dev/ttyS0 for COM1, etc)
            $serial->deviceSet("COM4");

    // We can change the baud rate, parity, length, stop bits, flow control
            $serial->confBaudRate(9600);
            $serial->confParity("none");
            $serial->confCharacterLength(8);
            $serial->confStopBits(1);
            $serial->confFlowControl("none");
    
    // Then we need to open it
            $serial->deviceOpen();

    // To write into
            $serial->sendMessage("Hello !");
            
          

    }
}



