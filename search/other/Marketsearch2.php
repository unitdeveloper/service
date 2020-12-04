<?php
/**
 * Author:  Maxamadjonov Jaxongir
 **/
namespace zetsoft\service\search;

use zetsoft\system\kernels\ZFrame;
use yii\sphinx\Query;

class Marketsearch2 extends ZFrame
{

     public function test2($class){

         $query = $this->getQuery();
         vdd($query);
         $model = $class::find()->select([
         'shop_product_id' => 'id',
         'shop_option_ids' => 'id',
         ]);
        return $model;
     }
     public function getQuery2(){
         $query = $this->httpGet(['q']);
         return $query;
     }

     private function parseQuery2(){

     }

}
