<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use Closure;
use InvalidArgumentException;
use Opis\Closure\SerializableClosure;
use ReflectionFunction;
use rmrevin\yii\fontawesome\FAS;
use stdClass;
use \Doctrine\Instantiator\Instantiator;
use zetsoft\assets\nohtm\ZAnimateAsset;
use zetsoft\dbdata\ALL\action\ActionWebData;
use zetsoft\dbitem\data\CommentItem;
use zetsoft\dbitem\core\SessionItem;
use zetsoft\dbitem\data\ConfigDB;
use zetsoft\models\page\PageAction;
use zetsoft\models\shop\ShopCategory;
use zetsoft\service\cores\Cache;
use zetsoft\system\Az;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZFormatter;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use kartik\growl\Growl;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use zetsoft\system\kernels\ZWidget;
use zetsoft\system\module\Controls;
use zetsoft\widgets\animo\ZAnimateCssWidget;
use zetsoft\widgets\themes\ZCardWidget;
use function Dash\Curry\result;

class Mains extends ZFrame
{

    #region Vars

    public const icon = [
        'fal fa-area-chart',
        'fal fa-bar-chart',
        'fal fa-line-chart',
        'fal fa-pie-chart',
        'fal fa-laptop',
        'fal fa-birthday-cake',
        'fal fa-calendar',
        'fal fa-credit-card',
        'fal fa-crop',
        'fal fa-crosshairs',
        'fal fa-cube',
        'fal fa-cubes',
        'fal fa-database',
        'fal fa-desktop',
        'fal fa-gears',
        'fal fa-gift',
        'fal fa-industry',
        'fal fa-wifi',
        'fal fa-gift',
        'fal fa-institution',
        'fal fa-film',
        'fal fa-graduation-cap',
        'fal fa-globe',
        'fal fa-address-book',
        'fal fa-address-card',
        'fal fa-plus',
        'fal fa-bell-plus',
        'fal fa-album-collection',
        'fal fa-bullhorn',
        'fal fa-camcorder',
        'fal fa-desktop',
        'fal fa-download',
        'fal fa-envelope',
        'fal fa-calendar-alt',
        'fal fa-calendar-check',
        'fal fa-calendar-edit',
        'fal fa-cart-plus',
        'fal fa-comment-alt',
        'fal fa-eye',
        'fal fa-folder-open',
        'fal fa-laptop-house',
        'fal fa-keyboard',
        'fal fa-map-marker-alt',
        'fal fa-microphone',
        'fal fa-phone-alt',
        'fal fa-phone-slash',
        'fal fa-plus-circle',
        'fal fa-power-off',
        'fal fa-print',
        'fal fa-print-slash',
        'fal fa-router',
        'fal fa-save',
        'fal fa-barcode',
        'fal fa-barcode-alt',
        'fal fa-barcode-read',
        'fal fa-cash-register',
        'fal fa-gift-card',
        'fal fa-shopping-basket',
        'fal fa-truck-container',
        'fal fa-chart-pie-alt',
        'fal fa-globe',
        'fal fa-landmark',
        'fal fa-money-check-edit-alt',
        'fal fa-paperclip',
        'fal fa-bags-shopping',
        'fal fa-user',
        'fal fa-certificate',
        'fal fa-thumbs-up',
        'fal fa-atlas',
        'fal fa-balance-scale',
        'fal fa-lock',
        'fal fa-lock-open-alt',
        'fal fa-map-marker-alt',
        'fal fa-shield',
        'fal fa-tools',
        'fal fa-calculator',
        'fal fa-cogs',
        'fal fa-fal fa-paper-plane',
        'fal fa-images'
    ];

    #endregion

    #region Test


    public function test()
    {
        //   $this->testCast();
        // $this->testRecast();
        // $this->testJsonObject();
        // $this->testCastArray();
        $this->testData2Object();
        // $this->testCast();

    }


    public function testJsonObject()
    {
        $code = <<<JS
{
      "dbase":"db",
      "lang":"ru",
      "addID":true,
      "addBy":true,
      "addDel":false,
      "faker":false,
      "menu":true,
      "edit":true,
      "edits":[

      ],
      "editsEx":[
         "created_at"
      ],
      "depend":[

      ],
      "hasOne":{
         "UserCompany":{
            "parent_id":"id"
         },
         "UserCompanyType":{
            "shop_company_type_id":"id"
         },
         "CoreAdress":{
            "core_address_ids":"id"
         },
         "User":{
            "created_by":"id",
            "modified_by":"id"
         }
      },
      "hasMulti":[

      ],
      "hasMany":{
         "ShopCatalog":{
            "user_company_id":"id"
         },
         "UserCompany":{
            "parent_id":"id"
         },
         "User":{
            "user_company_id":"id"
         }
      },
      "name":"name",
      "title":"\u041e\u0440\u0433\u0430\u043d\u0438\u0437\u0430\u0446\u0438\u044f",
      "tooltip":null,
      "icon":null,
      "relationBtn":true,
      "relationWidth":"100px",
      "extraConfig":false,
      "extraColumn":false,
      "makeMenu":true,
      "makeInsert":true,
      "genName":false,
      "columnCount":2,
      "order":{
         "id":3
      },
      "nameOn":[

      ],
      "nameOff":[

      ],
      "nameShowEx":[
         "id",
         "modified_at",
         "created_by",
         "modified_by",
         "created_at",
         "deleted_at",
         "deleted_by"
      ],
      "before":null,
      "after":null,
      "column":[

      ],
      "replace":[

      ],
      "filters":[

      ],
      "filtersEx":[

      ],
      "pageSummaries":[

      ],
      "pageSummariesEx":[

      ],
      "roleShow":null,
      "roleDenyEdit":null,
      "roleDenyFilter":null,
      "fakerCount":30,
      "filter":true,
      "summary":true,
      "readonly":null,
      "denyDB":null,
      "rulesAll":null,
      "rules":[

      ],
      "options":[

      ],
      "widget":[

      ],
      "valueWidget":[

      ],
      "filterWidget":[

      ],
      "valueOptions":[

      ],
      "filterOptions":[

      ],
      "showDyna":[

      ],
      "showForm":[

      ],
      "showDetail":[

      ],
      "showView":[

      ]
   }
JS;

        $object = $this->jsonObject(ConfigDB::class, $code);
        vd($object);


        $object2 = new ConfigDB();
        $object2->name = 'asdfads';
        $object2->nameOn = ['asdfads'];

        $code2 = ZJsonHelper::encode($object2);

        $object = $this->jsonObject(ConfigDB::class, $code2);

        vd($object);
    }


    public function testCast()
    {

    }

    private function testCastArray()
    {
        $models = ShopCategory::find()->all();

    }

    private function testData2Object()
    {

        $models = ShopCategory::find()->all();

        $item = new SessionItem();
        $item->data = $models;
        $item->array = true;
        $item->type = ShopCategory::class;
        $code = ZJsonHelper::encode($models);

        $data = $this->data2object($item->type, $item->data);
        vd($data);

    }

    private function testRecast()
    {


    }
    #endregion


    #region Casting


    /**
     * recast stdClass object to an object with type
     *
     * @param string $className
     * @param stdClass $object
     * @return mixed new, typed object
     * @throws InvalidArgumentException
     */

    public function recast($className, stdClass &$object)
    {
        if (!class_exists($className))
            return Az::error($className, 'Inexistant class');

        $new = $this->instant($className);

        foreach ($object as $property => $value) {
            $new->$property = $value;
            unset($object->$property);
        }
        unset($value);
        $object = null;

        return $new;
    }


    public function convert($className, $array)
    {
        if ($this->checkModel($className)) {
            $model = new $className;
            $return = null;
            foreach ($array as $key => $item) {
                if (ZArrayHelper::keyExists($key, $model->columns)) {
                    $dbType = $model->columns[$key]->dbType;
                    $valueType = gettype($item);
                    if ($dbType !== $valueType) {
                        $item = ZVarDumper::dbTypeCast($item, $dbType);
                    }
                    $return[$key] = $item;
                }
            }
            return $return;
        }
        return $array;
    }


    public function array2object($className, array &$array, bool $isArray = false)
    {
        $result = null;
        if ($isArray) {
            foreach ($array as $key => $item) {
                $item = $this->convert($className, $item);
                $stdClass = (object)$item;
                $object = $this->recast($className, $stdClass);
                $result[] = $object;
            }
            return $result;
        }
        $array = $this->convert($className, $array);
        $stdClass = (object)$array;
        return $this->recast($className, $stdClass);
    }


    public function bosya($provider = null, $exportType = 'html', $configs)
    {
        $data = ZArrayHelper::getValue($provider, 'allModels');

        $columns = ZArrayHelper::getValue($provider, 'columns');

        $results = [];
        $models = [];

        foreach ($data as $attributes) {

            $newAttributes = [];
            foreach ($attributes as $key => $value) {

                if (ZArrayHelper::keyExists($key, $columns)) {

                    $column = $columns[$key];

                    $title = ZArrayHelper::getValue($column, 'title');
                    $newAttributes[$title] = $value;
                }
            }

            $models[] = $newAttributes;
        }

        $results[$configs['title']] = $models;

        $now = date('d.m.Y_H-i-s');

        $filePath = Root . '/upload/uploaz/eyuf/bosya/' . "ZDynamicModel-$now" . '.' . $exportType;
        $res = [];
        foreach ($results as $result) {
            $res = $result;

        }
        file_put_contents($filePath, $res);

        return $res;
    }

    public function data2object($className, $code)
    {
        $return = [];

//        $array = ZJsonHelper::decode($code, true);

        foreach ($code as $item) {
            $return[] = $this->array2object($className, $item);
        }

        return $return;
    }

    public function one2object($className, $code)
    {

//        $array = ZJsonHelper::decode($code, true);

        return $this->array2object($className, $code);
    }



    #endregion


    #region GetWidget
    //start: MurodovMirbosit
    /*
     * barcha filelarni scan qiladi ohiri Widget blan tugagalarini olib beradi
     * */
    public function getWidget($files, $bool = true)
    {

        $items = [];
        $grapes = [];

        foreach ($files as $file) {

            $content = file_get_contents($file);

            $file = str_replace(['.php', '/'], ['', '\\'], $file);
            $class = str_replace(Az::getAlias('@zetsoft'), 'zetsoft', $file);

            if (!ZStringHelper::endsWith($class, 'Widget'))
                continue;

            if ($bool)
                if (ZStringHelper::find($content, "'enable' => true"))
                    $grapes[$class] = $class;

            $items[$class] = $class;

        }


        if ($bool)
            return $grapes;

        return $items;

    }

    public function getAllWidget($files)
    {

        $items = [];
        foreach ($files as $file) {

            $file = str_replace(['.php', '/'], ['', '\\'], $file);
            $class = str_replace(Az::getAlias('@zetsoft'), 'zetsoft', $file);

            if (!ZStringHelper::endsWith($class, 'Widget'))
                continue;

            $items[$class] = bname($class);

        }

        return $items;

    }

    //end

    public function actionsCheck($actionName)
    {

        $actions = (new ActionWebData())->main();

        $PageAction = $this->cache($actionName, Cache::type['array'], function () use ($actionName) {
            return PageAction::find()
                ->where(['name' => $actionName])
                ->limit(1)
                ->one();
        });

        $excludes = [
            'save'
        ];

        $b1 = $PageAction === null;
        $b2 = !ZArrayHelper::keyExists($this->actionId, $actions);
        $b3 = !Az::$app->utility->urlApp->isMain();
        $b4 = !ZArrayHelper::isIn($this->actionId, $excludes);

        if ($b1 && $b2 && $b3 && $b4)
            throw new ZException('Необходим корректный роутинг');

        return $PageAction;

    }

    #endregion

    #region Json


    public function jsonObject(string $className, string $comments)
    {
        if (!$this->isJSON($comments)) {
            Az::error($comments, 'Incorrect Json Data');
            return new $className();
        }

        $item = (object)ZJsonHelper::decode($comments);
        $item = $this->recast($className, $item);

        return $item;
    }


    public function isJSON($string)
    {
        return (is_string($string) && is_array(json_decode($string, true, 512)));
    }


    public function serial($value)
    {
        $return = null;
        switch (true) {

            case is_callable($value):
                $wrapper = new SerializableClosure($value);
                $return = serialize($wrapper);
                break;

            default:
                $return = serialize($value);

        }

        return $return;
    }


    #endregion


    #region Random

    public function icon(bool $isSRand = false)
    {
        $data = self::icon;

        $icon = $this->random('icon', $data, $isSRand);
        return (string)$icon;
    }


    public function animation(bool $isSRand = false)
    {
        $data = ZAnimateCssWidget::types();
        $return = $this->random('animate', $data, $isSRand);

        return " animated {$return}";

    }


    public function random(string $id, array $data, bool $isSRand = true)
    {

        global $boot;

        if (!$boot->isCLI()) {
            if ($isSRand) {
                if (empty(Az::$app->params[$id]))
                    Az::$app->params[$id] = crc32(Az::$app->request->absoluteUrl . Az::$app->cores->date->date());
                else
                    Az::$app->params[$id]++;
            } else
                Az::$app->params[$id] = random_int(0, 100);
        }


        Az::$app->params[$id] = 4;

        mt_srand(Az::$app->params[$id]);

        $count = \count($data);
        $iIndex = random_int(0, $count - 1);

        return $data[$iIndex];
    }


    #endregion


    #region Classes
    public function control()
    {
        $controlClass = sprintf("zetsoft\cnweb\%sController", $name);
        $controlClass = ZFileHelper::normLinux($controlClass);

        /** @var Controls $controlObject */

        if (class_exists($controlClass) && property_exists($controlClass, 'model')) {
            $controlObject = new $controlClass('id', 'module');
            $entity = $controlObject->modelClass;
            //    $control->entity = bname($entity);
        }

    }

    #endregion

    #region Cache
    public function dbCache()
    {
    }

    public function cacheMethod()
    {
    }

    public function cacheModel()
    {
        /**
         *
         * Checking for cache
         */
        global $cacheIgnoreMain;
        $cacheIgnore = ZArrayHelper::merge($cacheIgnoreMain, cacheIgnore);
        $cacheModel = !ArrayHelper::isIn($this->modelClass, $cacheIgnore);

        if ($cacheModel) {
            Az::trace("DB_Cache | TagDependency::invalidate | ON | {$this::className()}");
            return true;
        }
    }
    #endregion
}
