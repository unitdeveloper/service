<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\tests;


use zetsoft\dbitem\ALL\ZAppItem;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\dbitem\core\NormServiceItem;
use zetsoft\models\shop\ShopCategory;
use zetsoft\service\cores\Category;
use zetsoft\service\utility\File;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\navigat\ZNestable2Widget;

class Nestable2 extends ZFrame
{
    private $data;
    private $rootDir;

    public function getVal()
    {
        //$strdata = '[';

        $data = [];
        /** @var ShopCategory[] $coreCategory */
        $coreCategory = ShopCategory::find()->orderBy(['sort' => SORT_ASC])->all();
        //$notCategory = CoreCategory::find()->where('parent_id=:id', [':id' => null])->orderBy(['sort' => SORT_ASC])->all();
        $k = 0;
        $list3 = [];
        foreach ($coreCategory as $item) {
            if ($item->parent_id == 0 || $item->parent_id == null || $item->parent_id == '') {
                $list2 = [];
                $j=0;
                for ($i = 1; $i < count($coreCategory); $i++) {
                    if ($item->id == $coreCategory[$i]->parent_id) {
                        $list3[] =  array('id' => $coreCategory[$i]->id,
                            'content' => $coreCategory[$i]->name,);
                    //$j++;
                    }
                }
                $list2['id'] = $item->id;
                $list2['content'] = $item->name;
                if ($list3 != null)
                    $list2['children'] = $list3;
                $data[$k] = $list2;
                $k++;
            }
        }

        $myJSON = json_encode($data);
        return $myJSON;
    }

    public function setNestable($data)
    {
        $result_nestable = $data;
        $i = 1;

        foreach ($data as $item) {
            $id = $item->id;

            // where('parent_id=:id', [':id' => null])
            $model = ShopCategory::find()->where('id=:id', [':id' => $id])->one();
            $model->parent_id = null;
            $model->sort = $i;
            $model->save();

            //if ($item->children != null) {
            // return    vd(isset($item->children));
            if (isset($item->children)) {
                foreach ($item->children as $list) {
                    $i++;
                    // return    vd($list->id);
                    $model = ShopCategory::find()->where('id=:id', [':id' => $list->id])->one();
                    $model->parent_id = $id;
                    $model->sort = $i;
                    $model->save();
                }
            } else {
                $i++;
            }
        }
        /* $model = CoreCategory::find()->where(['id' => 35])->one();
         $model->child = $data;
         $model->save();  */
        // return $result_nestable;
    }

    /**
     * @param ShopCategory $parent_category
     */
    public function getItems($parent_category)
    {
        $items = [];
        if (!empty($parent_category->child)) {
            $core_categories = ShopCategory::find()->where([
                'id' => $parent_category->child
            ])->orderBy(['sort' => SORT_ASC])->all();
            foreach ($core_categories as $core_category) {
                $menuItem = new MenuItem();
                $menuItem->title = $core_category->name;
                $menuItem->icon = $parent_category->icon;
                $menuItem->url = $core_category->url;
                $menuItem->target = $core_category->target;
                $menuItem->extra = $this->normalizeExtra($core_category);
                $menuItem->items = $this->getItems($core_category);

                $items[] = $menuItem;
            }
        }

        return $items;
    }

    public function buildMenu($menu, $parentid = 0)
    {
        $result = null;
        $lies = [];
        foreach ($menu as $item) {
            $li = new \stdClass();
            if ($item->parent_id == $parentid) {
                $li->order = $item->order;
                $li->content = "<li class='dd-item nested-list-item' data-order='{$item->order}' data-id='{$item->id}'>
       <div class='dd-handle nested-list-handle'>
         <span class='glyphicon glyphicon-move'></span>
       </div>
       <div class='nested-list-content'>{$item->name}
         <div class='pull-right'>
          
         </div>
       </div>" . $this->buildMenu($menu, $item->id) . "</li>";
                $lies[] = $li;
            }
        }
        for ($i = 0; $i < $lies->count(); $i++) {
            for ($j = $i; $j < $lies->count(); $j++) {
                if ($lies[$i] > $lies[$j]) {
                    $tmp = $lies[$i];
                    $lies[$i] = $lies[$j];
                    $lies[$j] = $tmp;
                }
            }
        }
        foreach ($lies as $li) {
            $result .= $li->content;
        }
        return $result ? "\n<ol class=\"dd-list\">\n$result</ol>\n" : null;
    }

    public function actionRequest()
    {
        $request = \Yii::$app->request->get();
//        return $request->nestable_array;
        $nestable_array = json_decode($request->nestable_array);
        $this->change_order_parent($nestable_array);
        $notification = ['type' => 'success', 'title' => 'Категория изменена', 'body' => ''];
        // \Session::flash('notification',$notification);
        // return redirect()->back();
    }

    #region Core


    private function load($app)
    {
        $isTest = true;

        if ($isTest)
            $this->rootDir = Root . '\storing\testing';
        else $this->rootDir = Root;

        $items[] = function (ZAppItem $item) use ($app) {

            $item->templatePath = "/excmd/";
            $item->generate = "/excmd/$app/asrorz.php";
            $item->generatePath = "\\execut\\cmd\\$app";
            $item->replace = [
                '{app}' => $app,
            ];

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->templatePath = "/exrest/";
            $item->generate = "/exrest/$app/index_product.php";
            $item->generatePath = "/exrest/$app";
            $item->replace = [
                '{app}' => $app,
            ];

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/exweb/$app/.gitkeep";
            $item->generatePath = "/exweb/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->templatePath = "/configs/cmd/";
            $item->generate = "/configs/cmd/$app.php";
            $item->generatePath = "/configs/cmd";

            $item->affectFileOnly = true;

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->templatePath = "/configs/data/";
            $item->generate = "/configs/data/$app.php";
            $item->generatePath = "/configs/data";
            $item->replace = [
                '{app}' => $app,
            ];

            $item->affectFileOnly = true;

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->template = "Azk.env";
            $item->templatePath = "/configs/env/";
            $item->generate = "/configs/env/$app.env";
            $item->generatePath = "/configs/env";
            $item->replace = [
                '{app}' => $app,
            ];

            $item->affectFileOnly = true;

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->templatePath = "/configs/api/core/";
            $item->generate = "/configs/api/core/$app.php";
            $item->generatePath = "/configs/rest";

            $item->affectFileOnly = true;

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->templatePath = "/configs/web/";
            $item->generate = "/configs/web/$app.php";
            $item->generatePath = "/configs/web";

            $item->affectFileOnly = true;

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/control/env/$app/.gitkeep";
            $item->generatePath = "/control/env/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/control/api/core/$app/.gitkeep";
            $item->generatePath = "/control/api/core/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/cnweb/$app/.gitkeep";
            $item->generatePath = "/cnweb/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/former/$app/.gitkeep";
            $item->generatePath = "/former/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/models/$app/.gitkeep";
            $item->generatePath = "/models/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/layouts/$app/.gitkeep";
            $item->generatePath = "/layouts/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/inserts/$app/.gitkeep";
            $item->generatePath = "/inserts/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/project/$app/.gitkeep";
            $item->generatePath = "/project/$app";

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/service/App/$app/Test.php";
            $item->generatePath = "/service/App/$app";

            $item->template = "Test.php";
            $item->templatePath = "/service/App/Azk/";
            $item->replace = [
                'Azk' => $app,
            ];

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $class = ucfirst($app);
            $item->generate = "/service/App/ALL/$class.php"; //"/service/App/ALL/$app.php", // register new service in ALL/App.php as property
            $item->generatePath = "/service/App/ALL";

            $item->templatePath = "/service/App/ALL/";
            $item->replace = [
                'ZApp' => $app,
                'Azk' => $class,
            ];

            $item->affectFileOnly = true;

            // init with cruds/norms/service --class=App

            return $item;
        };

        $items[] = function (ZAppItem $item) use ($app) {

            $item->generate = "/webhtm/$app/.gitkeep";
            $item->generatePath = "/webhtm/$app";

            return $item;
        };

        foreach ($items as $item) {
            $this->data[] = $item(new ZAppItem());
        }
    }

    private function generatePath()
    {

    }

    private function move($app, $delete = false, $useTheSamePath = false, $pathToCopy = null)
    {
        $boot = new \Boot();
        foreach ($this->data as $n => $data) {

            $path = $this->rootDir;

            if (!empty($pathToCopy))
                $destination = $pathToCopy;
            else {
                if ($useTheSamePath)
                    $destination = '';
                else $destination = Root . $data->trashPath;
            }
            $destination .= '\\' . $app;

            $p = 0;

            if ($data->affectFileOnly) {

                $pathToMove = $destination . $data->generatePath;
                $boot->mkdir($pathToMove);

                $path .= $data->generate;
                $destination .= $data->generate;
                $c = 0;
                copy($path, $destination);

                if ($delete)
                    ZFileHelper::unlink($path);

            } else {
                $path .= $data->generatePath;
                $destination .= $data->generatePath;
                ZFileHelper::copyDirectory($path, $destination);

                if ($delete)
                    ZFileHelper::removeDir($path);

            }

            Az::debug($n, '$n');
            $p = 1;
            //$file = $this->rootDir . $data->generate;

        }
    }

    #endregion


    #region Create

    public function create($app, $appN = null)
    {

        if (strlen($app) > 5) vdd('App name must be less than 5 chars');

        $boot = new \Boot();

        $boot->eol(1);
        $boot->echo('Adding Project: ' . $app);

        $this->load($app); // fills this->datas array

        foreach ($this->data as $n => $data) {

            $path = $this->rootDir . $data->generatePath;
            $file = $this->rootDir . $data->generate;

            $content = '';
            if (!empty($data->templatePath)) {

                $content = file_get_contents($this->rootDir . $data->templatePath . $data->template);

                if (!empty($data->replace)) {
                    $content = strtr($content, $data->replace);
                }
            }


            $t = 1;
            if (!is_dir($path))
                $boot->mkdir($path);

            file_put_contents($file, $content);
        }

        // updating /service/ALL/App.php and
        // launch norms
        Az::$app->smart->norms->serviceAdd($app);

        // updating /scripts/initer/App.txt
        $appTxt = $this->rootDir . '/scripts/initer/App.txt';
        file_put_contents($appTxt, "$app\r\n", FILE_APPEND);
    }

    #endregion


    #region Remove

    public function remove($app)
    {
        $this->load($app);

        if (strlen($app) > 5) vdd('App name must be less than 5 chars');

        $boot = new \Boot();
        $boot->eol(1);
        $boot->echo('Removing Project: ' . $app);

        $this->move($app, true,);

        Az::$app->smart->norms->serviceRemove($app);

        // updating /scripts/initer/App.txt
        $appTxt = $this->rootDir . '/scripts/initer/App.txt';
        $appNames = Az::$app->utility->file->readByLine($appTxt);
        $appNames = Az::$app->utility->file->removeString($app, $appNames, true, true);
        Az::$app->utility->file->arrToFile($appTxt, $appNames);
    }

    #endregion


    #region Clone

    private function appCopy($app, $newApp)
    {

    }

    public function clone($app, $newApp)
    {
        $this->load($app);
        $this->appCopy($app, $newApp);

        // updating /service/ALL/App.php and
        // launch norms
        Az::$app->smart->norms->serviceAdd($newApp);

        // updating /scripts/initer/App.txt
        $appTxt = $this->rootDir . '/scripts/initer/App.txt';
        file_put_contents($appTxt, "$app\r\n", FILE_APPEND);
    }

    #endregion


    #region Extract

    public function extract($app, $destination)
    {

    }

    #endregion

    #region Test
//    public function test($app)
//    {
//        $this->load($app);
//        $this->testCreate();
//        //$this->testClone();
//        // $this->testRemove();
//    }


    public function testCreate()
    {
        Az::debug('checking created files', ' Process: ');

        foreach ($this->data as $n => $item) {

            $file = $this->rootDir . $item->generate;
            $fileExists = file_exists($file);

            if ($fileExists)
                Az::debug($file, $n . ' Ok ');
            else
                Az::warning($file, $n . ' Not found ');
        }
        //$this->testCreateApp();
    }

    private function testCreateApp()
    {

    }


    public function testRemove()
    {
        $this->testRemoveApp('test');
    }


    public function testClone()
    {
        $this->testCloneApp('test');
    }


    #endregion
}
