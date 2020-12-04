<?php
/**
 * Author:  Zoirjon Sobirov
 * @license  Zoirjon Sobirov
 * linkedIn: https://www.linkedin.com/in/zoirjon-sobirov/
 * Telegram: https://t.me/zoirjon_sobirov
 * @copyright zhead, zstart, zend
 */

namespace zetsoft\service\acme;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use function Amp\File\exists;

class checkingExpirationDate  extends ZFrame
{
   #region Vars
    public int $checkingDate;
    public int $issuedDate;
    public string $domain_name;
    public string $expirationDate;
    public string $domainValidationPath = Root.'/hoster/domain/validateDateOfSll/';
    #endregion

    #region Validate
    public function validate($domain_name){

        //declaring domain name
        $this->domain_name = $domain_name;

        //checking file inside folder
        if(file_exists($this->domainValidationPath.$this->domain_name.".txt")){

            $this->expirationDate = file_get_contents($this->domainValidationPath.$this->domain_name.'.txt');


            $checkingDate = date("d.m.y");


            if($checkingDate >= $this->expirationDate){

                echo "SSL keys are EXPIRED\n";
                // Az::$app->acme->acmeCoreZoir->updateSSL('final.zetsoft.uz', 'final',false);

            }elseif ($checkingDate <  $this->expirationDate) {
                echo "SSL keys are  still VALID\n";
            }
            echo "Done\n";

        }else{
            echo  $this->domainValidationPath.$this->domain_name."\n";
            echo "Domain file couldn't find !";

        }
}
    #endregion

    #region Register
    public function registerExpirationDate($domain_name){
        //declaring domain name
        $this->domain_name = $domain_name;

        $this->expirationDate = date("d.m.y",strtotime("+89 days"));
         
        //checking file inside folder
        if(!file_exists($this->domainValidationPath.$this->domain_name)) {

            file_put_contents($this->domainValidationPath.$this->domain_name.'.txt',$this->expirationDate);

            echo "file is created";

        }else{
            echo $this->domain_name." named file is exist";
        }
    }
    #endregion
}
