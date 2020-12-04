<?php
/**
 * Class    Booboo
 * @package zetsoft\service\league
 *
 * @author DilshodKhudoyarov
 *
 * Documentation - https://booboo.thephpleague.com/
 * Class file file lani manage qilish uchun va error lani topib berish uchun
 * for the execution of handlers and formatters for viewing and managing errors
 */

namespace zetsoft\service\league;
use League\BooBoo\Formatter\CommandLineFormatter;
use League\BooBoo\Formatter\HtmlFormatter;
use League\BooBoo\Formatter\HtmlTableFormatter;
use League\BooBoo\Formatter\JsonFormatter;
use League\BooBoo\Formatter\NullFormatter;
use zetsoft\system\kernels\ZFrame;

class Booboo extends ZFrame
{
    public $booboo;
    public $setErrorLimit = 'E_ERROR | E_WARNING | E_USER_ERROR | E_USER_WARNING';
    public $setErrorLimitAll = 'E_ALL';

    public function test_case(){
        $this->testing();
        $this->command_line_formatterTest();
        $this->html_formatterTest();
        $this->json_formatterTest();
        $this->html_table_formatterTest();
    }

    public function testing() {
        $bob = $this->booboo = new JsonFormatter();
        vd($bob);
        //$booboo->pushFormatter(new HtmlFormatter());
        //$booboo->register(); // Registers the handlers
    }

   public function html_formatterTest(){
       $formatter ='Enter an array for formatter!';
       $handler ='Enter an array for handler or give null!';
       $result=$this->html_formatter($formatter, $handler);
       vd($result);
   }

   public  function html_formatter($formatters, $handlers) {
       $bob = $this->booboo = new \League\BooBoo\BooBoo($formatters,$handlers);

       $html = new HtmlFormatter();
       $null = new NullFormatter();

       $html->setErrorLimit($this->setErrorLimit);
       $null->setErrorLimit($this->setErrorLimitAll);

       $bob->pushFormatter($null);
       $bob->pushFormatter($html);
       $bob->register(); // Registers the handlers
   }

    public function json_formatterTest(){
        $formatter ='Enter an array for formatter!';
        $handler ='Enter an array for handler or give null!';
        $result=$this->json_formatter($formatter, $handler);
        vd($result);
    }


    /**
     *
     * Function  json_formatter
     * @param $formatters
     * @param $handlers
     * @throws \League\BooBoo\Exception\NoFormattersRegisteredException
     * @author ShaxrizodNurmuxamedov
     */
    public  function json_formatter($formatters, $handlers) {
        $bob = $this->booboo = new \League\BooBoo\BooBoo($formatters,$handlers);



        $json = new JsonFormatter ();
        $null = new NullFormatter();

/*
        $json->setErrorLimit($this->setErrorLimit);
        $null->setErrorLimit($this->setErrorLimitAll);
*/

        // start:ShaxrizodNurmuxamedov
        
        $json->setErrorLimit($this->setErrorLimit);
        $null->setErrorLimit($this->setErrorLimitAll);

        // end:ShaxrizodNurmuxamedov

        $bob->pushFormatter($null);
        $bob->pushFormatter($json);
        $bob->register(); // Registers the handlers
    }

    public function html_table_formatterTest(){
        $formatter ='Enter an array for formatter!';
        $handler ='Enter an array for handler or give null!';
        $result=$this->html_table_formatter($formatter, $handler);
        vd($result);
    }

    public  function html_table_formatter($formatters, $handlers) {
        $bob = $this->booboo = new \League\BooBoo\BooBoo($formatters,$handlers);

        $html = new HtmlTableFormatter();
        $null = new NullFormatter();

        $html->setErrorLimit($this->setErrorLimit);
        $null->setErrorLimit($this->setErrorLimitAll);

        $bob->pushFormatter($null);
        $bob->pushFormatter($html);
        $bob->register(); // Registers the handlers
    }

    public function command_line_formatterTest(){
        $formatter ='Enter an array for formatter!';
        $handler ='Enter an array for handler or give null!';
        $result = $this->command_line_formatter($formatter, $handler);
        vd($result);
    }

    public  function command_line_formatter($formatters, $handlers) {
        $bob = $this->booboo = new \League\BooBoo\BooBoo($formatters,$handlers);

        $html = new CommandLineFormatter();
        $null = new NullFormatter();

        $html->setErrorLimit($this->setErrorLimit);
        $null->setErrorLimit($this->setErrorLimitAll);

        $bob->pushFormatter($null);
        $bob->pushFormatter($html);
        $bob->register(); // Registers the handlers
    }
}
