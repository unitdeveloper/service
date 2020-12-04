<?php

/**
 * @author  NurbekMakhmudov
 * @todo Join query examples
 *
 */

namespace zetsoft\service\tests;


use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;



class ActiveQueryKeldiyor extends ZFrame
{

    /**
     * @function test
     * @todo shu class function test qilish uchun
     */
    public function test()
    {

        $this->innerJoinWith1();
       // $this->andOnCondition();
        //$this->orOnCondition();
       // $this->orFilterHaving();
       // $this->on();
        //$this->andOnCondition();
       // $this->andFilterCompare();
       // $this->filterHaving();
       // $this->orHaving();
       // $this->having();
       // $this->andFilterWhere();
        //$this->prepare();
        //$this->leftJoin();
        //$this->inverseOf();
        //$this->innerJoin();
        //$this->indexBy();
       // $this->noCache();
        //$this->findFor();
       // $this->filterWhere();
        //$this->createCommand();
        // $this->having();
        //$this->andHawing();
        //$this->andOnCondition();

        /*
        $this->selectAll();
        $this->selectById(152);
        $this->joinsWith();
                $this->addGroupBy();
                $this->selectAll();
                $this->addOrderBy();
                $this->addParams();
                $this->addSelect();
                $this->andFilterCompare();
                $this->andFilterHaving();
                $this->andFilterWhere();





          -     $this->andHaving();
          -     $this->andOnCondition();
                $this->column();
          +     $this->createCommand();
                $this->filterHaving();
          +     $this->filterWhere();
          -     $this->findFor();
                $this->findWith();
          +     $this->noCache();
          -     $this->having();
          +     $this->indexBy();
          +     $this->innerJoin();
                $this->innerJoinWith();
                $this->inverseOf();
                $this->joinWith();
          +     $this->leftJoin();







                $this->offset();
                $this->onCondition();
                $this->on();
                $this->orFilterHavin();
                $this->orFilterWher();
                $this->orHavin();
                $this->orOnConditio();
                $this->params();
                $this->populate();
                $this->prepare();
                $this->rightJoin();
                $this->scalar();
                $this->union();
                $this->via();
                $this->viaTable();
                $this->with();
                $this->withQuery();     */

    }






    public function having()
    {
        $model = ShopOrder::find()
            ->select('shop_order.contact_name')
            ->groupBy("id")
            ->having('id > 1735')
            ->all();
            
        vd($model);
    }

    /*
     * Author Keldiyor
     * https://forum.yiiframework.com/t/createcommand-execute-then-using-findall-from-an-ar-class-throws-error/19889
     *buyruq yaratish, bu yangi jadaval yaratish uchun ishlatiladi
     */

    public function createCommand()
    {


        $model = ShopOrder::find()->select('shop_order.*');
        $add = Az::$app->db->createCommand()->createTable('post', [
            'id' => 'pk',
            'title' => 'string',
            'text' => 'text',
        ]);
        
        vd($add);

    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     * bu where shartining barcah shartlarini bitta joyda ishlatish
     */

    public function filterWhere(){
        $model = ShopOrder::find()
        ->select('shop_order.*')
        ->filterWhere([
                'contact_name' => 'salah333',
                'user_id' => 1,
            ])
        ->all();
        
        vd($model);

    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/guide/2.0/en/caching-data
     * bu keskdagi ma'lumot true yoki false ekanligini aniqladi
     */

    public function noCache(){
        $model = ShopOrder::find()
        ->select('shop-order.*');
        $var =  Az::$app->db->createCommand('SELECT * FROM shop_order WHERE user_id=1')->noCache()->queryOne();
        
        vd($var);
}

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     * jadvalni index bo'yicha tanlash
     */

    public function indexBy(){
        $model = ShopOrder::find()
        ->select('shop_order.*')
        ->from('shop_order')
        ->limit(1)
        ->indexBy('user_id')
        ->all();
        
        vd($model);
    }

    /*
     * Author by Keldiyor
     * https://stackoverflow.com/questions/32480792/yii2-innerjoin/50619410
     * bu tablisalarni berilgan shart asosida yangi jadvalga qo'shib beradi
     */



    /*
     * Author by Keldiyor
     * https://yiiframework.com.ua/ru/doc/guide/2/db-query-builder/
     * bu metod chap tomondan jadvallarni qo'shish uchun xizmat qiladi
     */

//    public function leftJoin(){
//        $sql = ShopOrderItem::find()->select('shop_order')->joinsLeft(ShopOrder::class)->all();
//        vdd($sql);
//    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#prepare()-detail
     * A prepared query instance which will be used by yii\db\QueryBuilder to build the SQL
     */

    public function prepare(){
        $model = ShopOrder::find()
        ->prepare('yii\db\QueryBuilder');
        vd($model);
    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-queryinterface#andFilterWhere()-detail
     * The new condition and the existing one will be joined using the 'AND' operator.
     */

    public function andFilterWhere(){
        $model = ShopOrder::find()
            ->select('shop_order.*')
            ->filterWhere([
                'contact_name' => 'salah333',])
            ->andFilterWhere(['user_id' => '1'])
            ->all();
        vd($model);

    }

    /*
 * Author by Keldiyor
 * https://www.yiiframework.com/doc/api/2.0/yii-db-queryinterface#orFilterWhere()-detail
 * The new condition and the existing one will be joined using the 'OR' operator.
 */

    public function orFilterWhere(){
        $model = ShopOrder::find()
            ->select('shop_order.*')
            ->filterWhere([
                'contact_name' => 'salah333',])
            ->orFilterWhere(['user_id' => '1'])
            ->all();
        vd($model);

    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#viaTable()-detail
     * Yii \ db \ ActiveRecord sinfidagi munosabatlarni e'lon qilishda birlashma jadvalini ko'rsatish uchun foydalaniladi
     */

    public function viaTable(){
         $model = ShopOrder::find()
            ->viaTable('shop_order', ['user_id' => 'id'])->all();
         vd($model);
    }


    /*
     * Author by Keldiyor
     * https://yiiframework.com.ua/ru/doc/guide/2/db-query-builder/
     * having metodini orHaving shaklida ishlatilishi
     * bu yerda  mos id larga teng bo'lgan qatorlar kelib chiqadi
     */


    public function  orHaving(){
        $model = ShopOrder::find()
        ->select('shop_order.contact_name')
        ->groupBy('id')
        ->having('id = 1750')
        ->orHaving('id = 1751')
        ->all();
        vd($model);
    }


    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#filterHaving()-detail
     * havingni filter qilib massiv qilib ishlatishi, bunda ikkita uchta shartni berish mumkin
     */

    public function  filterHaving(){
        $model = ShopOrder::find()
            ->select('shop_order.contact_name')
            ->groupBy('id')

            ->filterHaving(['id' => 1750, 'id' => 1751])
            ->all();
        vd($model);
    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     * Muayyan ustun uchun filtrlash shartini qo'shadi va foydalanuvchiga filtr operatorini tanlashga imkon beradi.
     */

    public function andFilterCompare(){

        $model = ShopOrder::find()
        ->select('shop_order.*')
        ->andFilterCompare( 'id', '1750' )
        ->all();
        vd($model);

    }


    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#onCondition()-detail
     *Yii \ db \ ActiveQuery :: joinWith () chaqirilganda ON holatida shart ishlatiladi.
     *Aks holda, shart so'rovning WHERE qismida ishlatiladi.
     */

    public function OnCondition(){
        $model = ShopOrder::find()
        ->select('shop_order.contact_name')
        ->onCondition(['id' => '1751'])
        ->all();
        vd($model);
}

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activerelationtrait
     * model  qaytarib beradi
     */

    public function findFor(){

        // return model
        $model = ShopOrder::find()
        ->findFor('PlaceRegion','ShopOrder');
        vd($model);

    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#$on-detail
     * Ushbu so'rov relyatsion kontekstda ishlatilganda foydalaniladigan qo'shilish sharti
     * return model
     */

    public function on(){
        $model = ShopOrder::find()->on('PlaceRegion','shop_order');
        vd($model);
}

    /*
     * Author by Keldiyor
     *https://www.yiiframework.com/doc/api/2.0/yii-db-query#orFilterHaving()-detail
     *  Mavjud holatga qo'shimcha HAVING shartini qo'shadi, ammo bo'sh operandlarni e'tiborsiz qoldiradi.
     */

    public function orFilterHaving(){
        $model = ShopOrder::find()
            ->select('shop_order.id')
            ->groupBy('id')
            ->filterHaving([
                'contact_name' => 'salah333',])
            ->orFilterHaving(['user_id' => '1'])
            ->all();
        vd($model);


    }


    /*
 * Author by Keldiyor
 * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#orOnCondition()-detail
 *   Mavjud holatga qo'shimcha ON holatini qo'shadi.
 */

    public function orOnCondition(){
        $model = ShopOrder::find()
            ->select('shop_order.contact_name')
            ->onCondition(['id' => '1751'])
            ->orOnCondition(['id' => '1752'])
            ->all();
        vd($model);
    }

    /*
* Author by Keldiyor
* https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#andOnCondition()-detail
*   Mavjud holatga qo'shimcha AND holatini qo'shadi.
     * return array
*/

    public function andOnCondition(){
        $model = ShopOrder::find()
            ->select('shop_order.contact_name')
            ->onCondition(['id' => '1751'])
            ->andOnCondition(['id' => '1752'])
            ->all();
        vd($model);
    }

   /* public function join(){

        $sql = ShopOrderItem::find()
            ->select('shop_order_item.*')
            ->innerJoin(ShopOrder::class);
           vdd($sql);
    }*/

    public function innerJoin(){

       $sql = ShopOrderItem::find()
            ->select('shop_order_item.id')
            ->innerJoin(['ShopOrder::class' => 'user_id']);
        vdd($sql);
    }


    public function leftJoin(){

        $sql = ShopOrder::find()
            ->select('shop_order.*')
            ->leftJoin('shop_order_item','shop_order_item.shop_order_id = shop_order.id')
            ->all();
        vdd($sql);
    }


    public function joinWith(){
        $model = ShopOrder::find()
        ->joinWith('shop_order_item')
        ->where(['shop_order_item.shop_order_id' => 'shop_order.user_id']);
        vd($model);
    }

    public function rightJoin(){

        $sql = ShopOrder::find()
            ->select('shop_order.*')
            ->rightJoin('shop_order_item','shop_order_item.shop_order_id = shop_order.id')
            ->all();
        vdd($sql);
    }



    public function innerJoinWith(){

        $sql = ShopOrderItem::find()
            ->select('shop_order_item.id')
            ->innerJoinWith(['ShopOrder::class' => 'user_id']);
        vdd($sql);
    }

    public function innerJoinWith1(){
       $model = ShopOrder::find()->innerJoinWith('shop_order_item.ware_id')
       ->where(['ware_id' => '12']);
       vd($model);
    }

}
