<?php

/**
 * @author  NurbekMakhmudov
 * @todo DB Query examples
 */

namespace zetsoft\service\tests;


use zetsoft\models\shop\ShopOrder;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\shop\ShopProduct;
use zetsoft\models\shop\ShopShipment;
use zetsoft\models\user\User;
use zetsoft\models\ware\WareAccept;
use zetsoft\system\Az;
use yii\db\Query;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class ActiveQueryNurbek extends ZFrame
{
    /**
     * @author NurbekMakhmudov
     * @todo ushbu function test qilish uchun
     */
    public function  test()
    {
        $this->scalar();
    }

    #region Normal Query

    /**
     * @author NurbekMakhmudov
     * @todo  birinchi qator ma'lumotlar bilan to'ldirilgan bitta yozuvni qaytaradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     * https://coderius.biz.ua/blog/article/yii2-formirovanie-zaprosov-dla-vyborki-iz-bd
     */
    public function one()
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
    public function sum()
    {
        switch ($this->runWith) {
            case 'yii':
                $res = (new \yii\db\Query())
                    ->from('shop_order')
                    ->sum('price');   // successful
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
                $res = (new \yii\db\Query())
                    ->from('shop_order')
                    ->max('price');   // successful
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

    public function asArrayTest()
    {
       $user = User::find()->asArray()->all();
       vd(ZArrayHelper::getValue($user, 'name'));
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
                $res = (new \yii\db\Query())
                    ->from('shop_order')
                    ->indexBy(function ($row) {
                        return $row['id'] . $row['name'];
                    })->all();                                 //  successful
                break;

            case 'zet':
                $res = ShopOrder::find()->scalar(['name']);   // error
                break;

            case 'cmd':
//                $shop_order = 'shop_order';
//                $id = 'id';
//                $res = Az::$app->db->createCommand("SELECT max({$id}) FROM {$shop_order}")->queryScalar();    // successful
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
            'name' => function ($query) {
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

        $user = User::find()
            ->having(['<', 'id', 125])
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

    /**
     * @author NurbekMakhmudov
     * @todo Mavjud holatga qo'shimcha WHERE shartini qo'shadi, ammo bo'sh operandlarni e'tiborsiz qoldiradi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */
    function orFilterWhere()
    {


    }

    /**
     * @author NurbekMakhmudov
     * @todo
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */
    function leftJoin()
    {
        $rows = (new \yii\db\Query())
            ->from('shop_order')
            ->all();
        vd($rows);
    }

    /**
     * @author NurbekMakhmudov
     * @todo
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */
    function t()
    {

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

        $res = ShopOrder::find()->where(['id' => 1587])->all();
//        1587

        vd($res);


        #endregion

        #region Yii2 example
        /*
        $rows = (new \yii\db\Query())
            ->from('shop_order')
            ->all();
        vd($rows);
        */
        #endregion

    }


    /**
     * @author NurbekMakhmudov
     * @todo Bir vaqtning o'zida bir nechta jadvaldan ma'lumotlarni olishingiz mumkin
     * @sqlExample  SELECT "shop_order_item".* FROM "shop_order_item"
     *              INNER JOIN "shop_order" ON "shop_order_item"."shop_order_id" = "shop_order"."id"
     */
    public function innerJoin()
    {
        #region Az sql example

        $all = "shop_order_item.*";
        $shop_order_item = "shop_order_item";
        $shop_order = "shop_order";
        $shop_order_id = '"shop_order_item"."shop_order_id"';
        $id = '"shop_order"."id"';

        $sql = <<<SQL
SELECT {$all} FROM {$shop_order_item} INNER JOIN {$shop_order} ON  {$shop_order_id} = {$id} 
SQL;
        $rows = Az::$app->db->createCommand($sql);
        $data = $rows->queryAll();
        vd($data);

        #endregion

        #region Model example
        /*
        $sql = ShopOrderItem::find()->joinsWith(ShopOrder::class);
        $res = $sql->all();
        vdd($res);
        */
        #endregion

        #region Yii2 example   have error
        /*
        $model = ShopProduct::find()
            ->joinWith([
                'shop_order' => function ($query) {
                    $query->onCondition(['shop_order_item.shop_order_id' => 230]);
                },
            ])->all();

        vd($model->attributes);
        */
        #endregion

    }


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
    public function leftJoinn()
    {

        $s = self::m['sql'];
        vd($s);

        #region Azz example

        /*$all = "shop_order_item.*";
        $shop_order_item = "shop_order_item";
        $shop_order = "shop_order";
        $shop_order_id = '"shop_order_item"."shop_order_id"';
        $id = '"shop_order"."id"';

        $sql = <<<SQL
SELECT {$all} FROM {$shop_order_item} LEFT JOIN {$shop_order} ON  {$shop_order_id} = {$id}
SQL;
        $rows = Az::$app->db->createCommand($sql);
        $data = $rows->queryAll();
        vd($data);*/

        #endregion

        #region Model example

//        $candidate = EyufScholar::findOne(['user_id' => $user->id]);
//        $country = PlaceCountry::find()->where(['id' => $candidate['place_country_id']])->one();

//        $id = ShopOrder::findOne(['id'=>1670]);  //1670
//         vd($shop_order_ids);
//         $name = ShopOrderItem::find()->select(['id'])->where(['shop_order_id'=>$id])->one();
//         vd($name);

        #endregion

        #region Yii2 example  have error
        /*
                $sql = (new \yii\db\Query())
                    ->select(['shop_order_item.name', 'shop_order.contact_name'])
                    ->from(['shop_order_item' => 'id'])
                    ->innerJoin('t1', 't1.child = aic.parent');

                */
        #endregion
    }


    /**
     * @author NurbekMakhmudov
     * @todo ikkita jadvallarini bir-biriga bog'liq id bo'yicha birlashtirib chiqarib beradi
     * @sqlExample  SELECT "shop_order_item".* FROM "shop_order_item" RIGHT JOIN "shop_order"
     *              ON "shop_order_item"."shop_order_id" = "shop_order"."id"
     */
    public function rightJoin()
    {

        #region Azz example
        $all = "shop_order_item.*";
        $shop_order_item = "shop_order_item";
        $shop_order = "shop_order";
        $shop_order_id = '"shop_order_item"."shop_order_id"';
        $id = '"shop_order"."id"';

        $sql = <<<SQL
SELECT {$all} FROM {$shop_order_item} RIGHT JOIN {$shop_order} ON  {$shop_order_id} = {$id} 
SQL;
        $rows = Az::$app->db->createCommand($sql);
        $data = $rows->queryAll();
        vd($data);

        #endregion

        #region Yii2 example  have error
        /*
                $sql = (new \yii\db\Query())
                    ->select(['shop_order_item.name', 'shop_order.contact_name'])
                    ->from(['shop_order_item' => 'id'])
                    ->innerJoin('t1', 't1.child = aic.parent');
        */
        #endregion
    }


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


    public function innerJoinWith()
    {

//        $model = ShopProduct::find()
//            ->innerJoinWith('t', 'Product.id = T.productId')
//            ->andWhere(['T.productFeatureValueId' => ''])
//            ->innerJoinWith('t1', 'Product.id = T1.productId')
//            ->andWhere(['T1.productFeatureValueId' => '5'])
//            ->all();


        /** @var Models $model */
        $model = ShopProduct::find()
            ->joinWith([
                'shop_order' => function ($query) {
                    $query->onCondition(['shop_order.user_id' => ShopOrder::SCENARIO_DEFAULT]);
                },
            ])->all();

        vdd($model->attributes);

    }


    /**
     * You can use multiple joins at once
     * You can use joinLeft() and joinRight()
     * @return  array|\zetsoft\system\actives\ZActiveQuery[]
     * @throws \Exception
     */

    public function joinsInner()
    {

        WareAccept::find()->all();

        ShopShipment::find()->all();

        ShopOrder::find()->all();

        $models = ShopOrderItem::find()
            ->select('shop_order.total_price')
            ->joinsInner(ShopOrder::class)
            ->all();

        vdd($models);
    }

    public function joinWith()
    {
        $model = User::find()
            ->select('user.name')
            ->joinWith('company')
            ->one();
        vdd($model);

    }

    public function queryJoin()
    {
        return ShopOrderItem::find()->join('INNER JOIN', ShopOrder::class)->all();
    }

    public function queryJoinWith()
    {
        return ShopOrderItem::find()->joinWith(ShopOrder::class)->all();
    }

    public function andFilterWhere()
    {
        // TODO: Implement andFilterWhere() method.
    }

    public function andOnCondition()
    {
        // TODO: Implement andOnCondition() method.
    }


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

    public function filterHaving()
    {
        // TODO: Implement filterHaving() method.
    }

    public function filterWhere()
    {
        // TODO: Implement filterWhere() method.
    }

    public function findFor()
    {
        // TODO: Implement findFor() method.
    }

    public function findWith()
    {
        // TODO: Implement findWith() method.
    }

    public function noCache()
    {
        // TODO: Implement noCache() method.
    }

    public function inverseOf()
    {
        // TODO: Implement inverseOf() method.
    }

    public function offset()
    {
        // TODO: Implement offset() method.
    }

    public function onCondition()
    {
        // TODO: Implement onCondition() method.
    }

    public function on()
    {
        // TODO: Implement on() method.
    }

    public function orFilterHavin()
    {
        // TODO: Implement orFilterHavin() method.
    }

    public function orFilterWher()
    {
        // TODO: Implement orFilterWher() method.
    }

    public function orHavin()
    {
        // TODO: Implement orHavin() method.
    }

    public function orOnConditio()
    {
        // TODO: Implement orOnConditio() method.
    }

    public function params()
    {
        // TODO: Implement params() method.
    }

    public function populate()
    {
        // TODO: Implement populate() method.
    }

    public function prepare()
    {
        // TODO: Implement prepare() method.
    }

    public function union()
    {
        // TODO: Implement union() method.
    }

    public function via()
    {
        // TODO: Implement via() method.
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


    /**
     * @author NurbekMakhmudov
     * @todo Relyatsion so'rov uchun birlashma jadvalini belgilaydi.
     * https://www.yiiframework.com/doc/api/2.0/yii-db-activequery
     */
    public function viaTable()
    {
        /**
         *  many-to-many ikkita jadval asosida bitta jadval tuzilgan bo'lsa
         *  ushbu jadvallardagi malumotlar olish uchun ishlatiladi
         *  https://yiiframework.com.ua/ru/doc/guide/2/db-active-record/
         *
         * ishlamadi
         */
        return $this->hasMany(Item::className(), ['id' => 'item_id'])
            ->viaTable('order_item', ['order_id' => 'id']);
    }

    public function withQuery()
    {
        // TODO: Implement withQuery() method.
    }


    public function indexBy()
    {
        // TODO: Implement indexBy() method.
    }



    /**
     * @author NurbekMakhmudov
     *  array examples
     */

    public function getDataFromArray()
    {


    }

    public function something($id)
    {
        // SELECT * FROM user;
        $users = User::find()
            ->where([
                'id' => $id,
                'status' => 'online'
            ])->all();


        foreach ($users as $user){
            echo $user['name'] = 'toshmat';

            $user->save();

        }
    }


}
