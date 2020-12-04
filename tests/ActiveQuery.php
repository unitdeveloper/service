<?php

/**
 * @todo DB Query examples
 *
 * @author  NurbekMakhmudov
 * @license Keldiyor
 * @license Sukhrob
 */

namespace zetsoft\service\tests;

use app\models\Auto;
use yii\db\Query;
use yii\db\Transaction;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class ActiveQuery extends ZFrame
{

    private $runWith;
    public const runWith = [
        'yii' => 'yii',
        'zet' => 'zet',
        'cmd' => 'cmd',
    ];

    public function init()
    {
        parent::init();
        $this->runWith = self::runWith['cmd'];
    }


    /**
     * @author NurbekMakhmudov
     * @todo ushbu function test qilish uchun
     */
    public function test()
    {
        $this->Transaction();
    }

    #region Normal Query

    /**
     * @author NurbekMakhmudov
     * @todo  birinchi qator ma'lumotlar bilan to'ldirilgan bitta yozuvni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     * https://coderius.biz.ua/blog/article/yii2-formirovanie-zaprosov-dla-vyborki-iz-bd
     */

    function one()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from(['user'])->where(['id' => 152])->one();   //successful
                break;

            case 'zet':
                $res = User::find()->where(['id' => 152])->one();   //successful
                break;

            case 'cmd':
                $user = '"user"';
                $id = '"id"';
                $num = 152;
                $sql = <<<SQL
SELECT * FROM {$user} WHERE {$id} = $num;
SQL;
                $res = Az::$app->db->createCommand($sql)->queryAll();   //successful
                break;
        }
        vdd($res);
//        vdd($res->attributes);
    }

    /**
     * @author NurbekMakhmudov
     * @todo  so'rov natijalari asosida barcha yozuvlarni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function all()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from(['user'])->all();  // successful
                break;

            case 'zet':
                $res = User::find()->all();   //successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $res = Az::$app->db->createCommand("SELECT * FROM {$shop_order}")->queryAll();    //successful
                break;
        }
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo  yozuvlar sonini qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function count()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from(['shop_order'])->count();  // successful
                break;

            case 'zet':
                $res = ShopOrder::find()->count();   // successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $res = Az::$app->db->createCommand("SELECT COUNT(*) FROM {$shop_order}")->queryAll();    // successful
                break;
        }
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo summani belgilangan ustun ustiga qaytaradi.
     * @sqlExample  SELECT SUM(price) FROM shop_order;
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     * https://www.postgresqltutorial.com/postgresql-sum-function/
     * https://stackoverflow.com/questions/26852453/yii2-getting-sum-of-a-column
     */

    function sum()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from('shop_order')->sum('price');   // successful
                break;

            case 'zet':
                $res = ShopOrder::find()->sum('price');   //  successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $price = 'price';
                $res = Az::$app->db->createCommand("SELECT sum({$price}) FROM {$shop_order}")->queryScalar();    // successful
                break;
        }
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo  belgilangan ustun ustidagi o'rtacha qiymatni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function average()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from('shop_order')->max('price');   // successful
                break;

            case 'zet':
                $res = ShopOrder::find()->max('price');   // successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $price = 'price';
                $res = Az::$app->db->createCommand("SELECT max({$price}) FROM {$shop_order}")->queryScalar();    // successful
                break;
        }
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo belgilangan ustundagi minmal qiyatni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function min()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from('shop_order')->min('id');   // successful
                break;

            case 'zet':
                $res = ShopOrder::find()->min('id');   // successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $id = 'id';
                $res = Az::$app->db->createCommand("SELECT min({$id}) FROM {$shop_order}")->queryScalar();    // successful
                break;
        }
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo belgilangan ustundagi maxsimal qiymatni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function max()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from('shop_order')->max('id');   // successful
                break;

            case 'zet':
                $res = ShopOrder::find()->max('id');   // successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $id = 'id';
                $res = Az::$app->db->createCommand("SELECT max({$id}) FROM {$shop_order}")->queryScalar();    // successful
                break;
        }
        vd($res);
    }


    /**
     * @author NurbekMakhmudov
     * @todo  so'rov natijasining birinchi qatoridagi birinchi ustun qiymatini qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function scalar()
    {
//        $res = User::find()->select(['name'])->scalar();
//        vd($res);

        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->from('shop_order')->max('id');   // successful
                break;

            case 'zet':
                $res = ShopOrder::find()->max('id');   // successful
                break;

            case 'cmd':
                $shop_order = 'shop_order';
                $id = 'id';
                $res = Az::$app->db->createCommand("SELECT max({$id}) FROM {$shop_order}")->queryScalar();    // successful
                break;
        }
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo so'rov natijasidagi birinchi ustun qiymatini qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function column()
    {
        $res = User::find()->column();
        vd($res);
    }

    /**
     * @author NurbekMakhmudov
     * @todo so'rov natijalarida ma'lumotlar mavjudligini yoki yo'qligini ko'rsatadigan qiymatni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    function exists()
    {       //1573
        $res = ShopOrder::find()->select(['id'])->where(['id' => 1573])->exists();
        vd($res);
    }


    /**
     * @author NurbekMakhmudov
     * @todo ushbu so'rov bajarilishi kerak bo'lgan munosabatlar ro'yxati.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequerytrait#with()-detail
     * https://coderius.biz.ua/blog/article/yii2-formirovanie-zaprosov-dla-vyborki-iz-bd
     */

    function with()
    {               // tushunmadim

//        $user = User::find()->where(['id' => 125])->all();
//        vd($user);

//        $user = User::find()
//            ->with('title')
//            ->all();
//        vd($user);


        $user = User::find()->with([
            'id' => function ($query) {
                $query->andWhere(['>', 'id', 100]);
            },
        ])->all();
        vd($user);

    }


    /**
     * @author NurbekMakhmudov
     * @todo  Mavjud holatga qo'shimcha HAVING shartini qo'shadi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     * https://coderius.biz.ua/blog/article/yii2-formirovanie-zaprosov-dla-vyborki-iz-bd
     */

    function andHaving()
    {                       //  tushunmadim
//        $user = User::find()->select(['*', '' => 'COUNT(*)'])->groupBy('id')->having(['<=','cnt', 1])->all();
//        vd($user);

        $user = User::find()->having(['<', 'id', 125])
            ->andHaving(['title' => 'user2357']);
        vd($user);
    }


    /**
     * @author NurbekMakhmudov
     * @todo Mavjud holatga qo'shimcha HAVING shartini qo'shadi, ammo bo'sh operandlarni e'tiborsiz qoldiradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */

    public function andFilterHaving(array $condition)
    {
        $condition = User::find()->filterCondition($condition);
        if ($condition !== []) {
            $this->andHaving($condition);
        }
        return $this;
    }


    #endregion


    #region Successful functions

    /**
     * @author NurbekMakhmudov
     * @todo  show all records from shop_order table
     * @sqlExample  SELECT * FROM shop_order
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     * https://yiiframework.com.ua/ru/doc/guide/2/db-active-record/
     */

    function selectAll()
    {

//        $orders = ShopOrder::find()->all();
//        foreach ($orders as $order)
//            vd($order);

        #region Az example

//        $shop_order = 'shop_order';
//        $rows = Az::$app->db->createCommand('SELECT * FROM ' . $shop_order);
//        $data = $rows->queryAll();
//        vd($data);

//        $order = ShopOrder::find()
//            ->select([
//                'id',
//            ])
//            ->orderBy([
//
//            ])
//            ->where([
//                'status_logistics' => 'new'
//            ])
//            ->orWhere([
//
//            ])
//            ->all();

//        $res = ShopOrder::find()->where(['id' => 1587])->all();
//        1587

//        vd($res);


        #endregion

        #region Yii2 example

        $rows = (new \yii\db\Query())
            ->from('shop_order')
            ->all();
        vd($rows);

        #endregion

    }


    /**
     * @author NurbekMakhmudov
     * @todo Bir vaqtning o'zida bir nechta jadvaldan ma'lumotlarni olishingiz mumkin
     * @sqlExample  SELECT "shop_order_item".* FROM "shop_order_item"
     *              INNER JOIN "shop_order" ON "shop_order_item"."shop_order_id" = "shop_order"."id"
     */

//    public function innerJoin()
//    {
//        #region Az sql example
//
//        $all = "shop_order_item.*";
//        $shop_order_item = "shop_order_item";
//        $shop_order = "shop_order";
//        $shop_order_id = '"shop_order_item"."shop_order_id"';
//        $id = '"shop_order"."id"';
//
//        $sql = <<<SQL
//SELECT {$all} FROM {$shop_order_item} INNER JOIN {$shop_order} ON  {$shop_order_id} = {$id}
//SQL;
//        $rows = Az::$app->db->createCommand($sql);
//        $data = $rows->queryAll();
//        vd($data);
//
//        #endregion
//
//        #region Model example
//        /*
//        $sql = ShopOrderItem::find()->joinsWith(ShopOrder::class);
//        $res = $sql->all();
//        vdd($res);
//        */
//        #endregion
//
//        #region Yii2 example   have error
//        /*
//        $model = ShopProduct::find()
//            ->joinWith([
//                'shop_order' => function ($query) {
//                    $query->onCondition(['shop_order_item.shop_order_id' => 230]);
//                },
//            ])->all();
//
//        vd($model->attributes);
//        */
//        #endregion
//
//    }


    /**
     * @author NurbekMakhmudov
     * @todo  user jadvalidan malumotlarini id bo'yicha chiqarib beradi
     * @sqlExample  SELECT * FROM "user" WHERE "id" = 152;
     */

    function selectById($id)
    {
        #region Az example

        $user = '"user"';
        $w = '"id"';
        $sql = <<<SQL
SELECT * FROM {$user} WHERE {$w} = {$id};
SQL;
        $rows = Az::$app->db->createCommand($sql);
        $data = $rows->queryAll();
        vd($data);

        #endregion

        #region Model example for result need run a not debug
        /*
        $user = User::find()
            ->where(['id' => $id])
            ->one();
        vdd($user->attributes);
        */
        #endregion

        #region Yii2 example  have error
        /*
        $user = User::find()
            ->from('user')
            ->where('id=:id', array(':id'=>$id))
            ->one();
        vd($user-$this->attribute);
        */
        #endregion
    }

    /**
     * @author NurbekMakhmudov
     * @todo ikkita jadvallarini bir-biriga bog'liq id bo'yicha birlashtirib chiqarib beradi
     * @sqlExample  SELECT "shop_order_item".* FROM "shop_order_item" LEFT JOIN "shop_order"
     *              ON "shop_order_item"."shop_order_id" = "shop_order"."id"
     */

//    public function leftJoin()
//    {
//
//        $s = self::m['sql'];
//        vd($s);
//
//        #region Azz example
//
//        /*$all = "shop_order_item.*";
//        $shop_order_item = "shop_order_item";
//        $shop_order = "shop_order";
//        $shop_order_id = '"shop_order_item"."shop_order_id"';
//        $id = '"shop_order"."id"';
//
//        $sql = <<<SQL
//SELECT {$all} FROM {$shop_order_item} LEFT JOIN {$shop_order} ON  {$shop_order_id} = {$id}
//SQL;
//        $rows = Az::$app->db->createCommand($sql);
//        $data = $rows->queryAll();
//        vd($data);*/
//
//        #endregion
//
//        #region Model example
//
////        $candidate = EyufScholar::findOne(['user_id' => $user->id]);
////        $country = PlaceCountry::find()->where(['id' => $candidate['place_country_id']])->one();
//
////        $id = ShopOrder::findOne(['id'=>1670]);  //1670
////         vd($shop_order_ids);
////         $name = ShopOrderItem::find()->select(['id'])->where(['shop_order_id'=>$id])->one();
////         vd($name);
//
//        #endregion
//
//        #region Yii2 example  have error
//        /*
//                $sql = (new \yii\db\Query())
//                    ->select(['shop_order_item.name', 'shop_order.contact_name'])
//                    ->from(['shop_order_item' => 'id'])
//                    ->innerJoin('t1', 't1.child = aic.parent');
//
//                */
//        #endregion
//    }


    /**
     * @author NurbekMakhmudov
     * @todo ikkita jadvallarini bir-biriga bog'liq id bo'yicha birlashtirib chiqarib beradi
     * @sqlExample  SELECT "shop_order_item".* FROM "shop_order_item" RIGHT JOIN "shop_order"
     *              ON "shop_order_item"."shop_order_id" = "shop_order"."id"
     */

//    public function rightJoin()
//    {
//
//        #region Azz example
//        $all = "shop_order_item.*";
//        $shop_order_item = "shop_order_item";
//        $shop_order = "shop_order";
//        $shop_order_id = '"shop_order_item"."shop_order_id"';
//        $id = '"shop_order"."id"';
//
//        $sql = <<<SQL
//SELECT {$all} FROM {$shop_order_item} RIGHT JOIN {$shop_order} ON  {$shop_order_id} = {$id}
//SQL;
//        $rows = Az::$app->db->createCommand($sql);
//        $data = $rows->queryAll();
//        vd($data);
//
//        #endregion
//
//        #region Yii2 example  have error
//        /*
//                $sql = (new \yii\db\Query())
//                    ->select(['shop_order_item.name', 'shop_order.contact_name'])
//                    ->from(['shop_order_item' => 'id'])
//                    ->innerJoin('t1', 't1.child = aic.parent');
//        */
//        #endregion
//    }


    /**
     * @author NurbekMakhmudov
     * @todo Qo'shimcha ustunlarni tanlash uchun addSelect() ishlatishingiz mumkin
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder#select
     */

    public function addSelect()
    {
        #region Yii2 example

        $rows = (new \yii\db\Query())
            ->select(['id', 'name'])
            ->addSelect('date')
            ->from('shop_order')
            ->all();
        vdd($rows);

        #endregion
    }

    /**
     * @author NurbekMakhmudov
     * @todo ORDER BY so'roviga qo'shimcha ustunlar qo'shish uchun addOrderBy() ishlatishingiz mumkin
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder#order-by
     */


    public function addOrderBy()
    {
        #region Yii2 example

        $rows = (new \yii\db\Query())
            ->select(['id', 'name', 'contact_name'])
            ->from('shop_order')
            ->orderBy('id DESC')
            ->addOrderBy('contact_name ASC')
            ->all();
        vdd($rows);

        #endregion
    }

    #endregion

    /**
     * @author NurbekMakhmudov
     * @todo So'rovni bajarish uchun ishlatilishi mumkin bo'lgan DB buyrug'ini yaratadi.
     * @sqlExample  SELECT * FROM shop_order
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery#createCommand()-detail
     * https://github.com/yiisoft/yii2/blob/master/docs/guide/db-dao.md#basic-sql-queries
     */

    public function createCommandN()
    {
        $res = null;
        $shopOrder = 'shop_order';
        $sql = <<<SQL
SELECT * FROM {$shopOrder};
SQL;
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())->createCommand($sql)->queryAll();  // have error
                break;

            case 'zet':
                $res = ShopOrder::find()->all();
                break;

            case 'cmd':
                $res = Az::$app->db->createCommand($sql)->queryAll();
                break;
        }
        vdd($res);
    }


    /**
     * @author NurbekMakhmudov
     * @todo So'rovga bog'lanish uchun qo'shimcha parametrlarni qo'shadi.
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder#where
     */

    public function addParams()
    {

        #region Yii2 example  not worked
        $id = 230;
        $rows = (new \yii\db\Query())
            ->from('shop_order')
            ->where('id=:id')
            ->addParams([':id' => $id])
            ->all();
        vd($rows);

        /*        $rows = (new \yii\db\Query())
                    ->from('shop_order')
                    ->where('id=:id')
                    ->addParams([':id' => 230])
                    ->all();
                vdd($rows);*/

        #endregion
    }


    /**
     * @author NurbekMakhmudov
     * GROUP BY so'roviga  qo'shimcha ustunlar qo'shish uchun addGroupBy() qo'shishingiz mumkin.
     * https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder
     * https://yiiframework.com.ua/ru/doc/guide/2/db-query-builder/
     */

    public function addGroupBy()
    {

        $rows = (new \yii\db\Query())
            ->from('user')
            ->groupBy(['id'])
            ->addGroupBy('status')
            ->limit(10)
            ->all();

        vdd($rows);
    }


//    public function innerJoinWith()
//    {
//
////        $model = ShopProduct::find()
////            ->innerJoinWith('t', 'Product.id = T.productId')
////            ->andWhere(['T.productFeatureValueId' => ''])
////            ->innerJoinWith('t1', 'Product.id = T1.productId')
////            ->andWhere(['T1.productFeatureValueId' => '5'])
////            ->all();
//
//
//        /** @var Models $model */
//        $model = ShopProduct::find()
//            ->joinWith([
//                'shop_order' => function ($query) {
//                    $query->onCondition(['shop_order.user_id' => ShopOrder::SCENARIO_DEFAULT]);
//                },
//            ])->all();
//
//        vdd($model->attributes);
//
//    }


    /**
     * You can use multiple joins at once
     * You can use joinLeft() and joinRight()
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     */

    public function joinsInner()
    {
        $models = ShopOrderItem::find()
            ->select('shop_order.total_price')
            ->joinsInner(ShopOrder::class)
            ->all();

        vdd($models);
    }

//    public function joinWith()
//    {
//        $model = User::find()
//            ->select('user.name')
//            ->joinWith('company')
//            ->one();
//        vdd($model);
//
//    }

    public function queryJoin()
    {
        return ShopOrderItem::find()->join('INNER JOIN', ShopOrder::class)->all();
    }

    public function queryJoinWith()
    {
        return ShopOrderItem::find()->joinWith(ShopOrder::class)->all();
    }

    /*
     * Sets the OFFSET part of the query.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-querytrait#offset()-detail
     */

    public function offset()
    {
        $query = (new Query())
            ->select('name')
            ->from('place_country')
            ->limit(3)
            ->offset(20)
            ->all();

        vdd($query);
    }

        /*
     * Returns the query result as a scalar value.
     * The value returned will be the first column in the first row of the query results.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#scalar()-detail
     */

    public function scalar1()
    {
        $query = (new Query())
            ->select('name')
            ->from('place_country')
            ->scalar();

        vdd($query);
    }

    /*
 * Appends a SQL statement using UNION operator.
 * @author SukhrobNuraliev
 * https://www.yiiframework.com/doc/api/2.0/yii-db-query#union()-detail
 */

    public function union()
    {
        $query1 = (new \yii\db\Query())
            ->select("name")
            ->from('place_country')
            ->limit(10);

        $query2 = (new \yii\db\Query())
            ->select('title')
            ->from('place_region')
            ->limit(10);

        vdd($query1->union($query2));
    }

    /*
     * Sets the parameters to be bound to the query.
     * @author SukhrobNuraliev
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#params()-detail
     */

    public function params()
    {
        $query = (new Query())
            ->params([':name' => 'Гонконг', ':alpha2' => 'HK']);

        vdd($query);
    }


    #region Keldiyor


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
        $var =  Az::$app->db->createCommand('SELECT * FROM shop_order WHERE id=1651')->noCache()->queryOne();
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

    public function innerJoin1(){

        $sql = ShopOrderItem::find()->select('shop_order')->joinsInner(ShopOrder::class)->all();
        vdd($sql);


    }

    /*
     * Author by Keldiyor
     * https://yiiframework.com.ua/ru/doc/guide/2/db-query-builder/
     * bu metod chap tomondan jadvallarni qo'shish uchun xizmat qiladi
     */

    public function leftJoin1(){
        $sql = ShopOrderItem::find()->select('shop_order')->joinsLeft(ShopOrder::class)->all();
        vdd($sql);
    }

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
     * https://qna.habr.com/q/378221
     * saralangan ustunga where o'rnida shart berish
     */

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
     * return array
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


    /*
     * Author by keldiyor
     * https://stackoverflow.com/questions/32480792/yii2-innerjoin/50619410
     */

    public function innerJoin(){

        $sql = ShopOrderItem::find()
            ->select('shop_order_item.id')
            ->innerJoin(['ShopOrder::class' => 'user_id']);
        vdd($sql);
    }


    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/guide/2.0/en/db-active-record
     */

    public function leftJoin(){
        $sql = ShopOrder::find()
            ->select('shop_order.*')
            ->leftJoin('shop_order_item','shop_order_item.shop_order_id = shop_order.id')
            ->all();
        vdd($sql);
    }

    /*
     * Author by Keldiyor
     * https://yiiframework.com.ua/ru/doc/guide/2/db-active-record/
     */

    public function joinWith(){
        $model = ShopOrder::find()
            ->joinWith('shop_order_item')
            ->where(['shop_order_item.shop_order_id' => 'shop_order.user_id']);
        vd($model);
    }

    /*
     * Author by Keldiyor
     * https://www.yiiframework.com/doc/api/2.0/yii-db-query#rightJoin()-detail
     */

    public function rightJoin(){

        $sql = ShopOrder::find()
            ->select('shop_order.*')
            ->rightJoin('shop_order_item','shop_order_item.shop_order_id = shop_order.id')
            ->all();
        vdd($sql);
    }

    /*
     * Author by Keldiyor
     * https://coderius.biz.ua/blog/article/yii2-formirovanie-zaprosov-dla-vyborki-iz-bd
     */

    public function innerJoinWith(){

        $sql = ShopOrderItem::find()
            ->select('shop_order_item.id')
            ->innerJoinWith(['ShopOrder::class' => 'user_id']);
        vdd($sql);
    }

    /*
    * Author by Keldiyor
    * https://coderius.biz.ua/blog/article/yii2-formirovanie-zaprosov-dla-vyborki-iz-bd
    */

    public function innerJoinWith1(){
        $model = ShopOrder::find()->innerJoinWith('shop_order_item.ware_id')
            ->where(['ware_id' => '12']);
        vd($model);
    }


    #endregion

    /*
     * Author by Keldiyor
     *
     * http://www.bsourcecode.com/yiiframework2/transaction/#
     * :~:text=Yii%20Framework%202%20%3A%20Transaction,beginTransaction()
     *
     * Transaction: A transaction is used to run a group of operations in single process.
     * When you we run multiple query in single process,
     * It will be used. If you get any problem,
     * It will not complete successfully and also rollback the executed query. beginTransaction()
     */

    public function Transaction(){

        $connection = \Yii::$app->db;
       // vd($connection);

        $transaction = $connection->beginTransaction();
        try {
            $user_model=$connection->createCommand()
                ->insert('shop_order', [
                    'contact_name' => 'yii',
                    'status_accept' => 1,
                ])->execute();


            //.....
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }

        vd($connection);
        vd($user_model);
        vd($connection);

}




}
