<?php

/**
 * Author: Jobir Yusupov
 * Date:    07.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\market;

use zetsoft\models\shop\ShopCategory;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopOption;
use zetsoft\models\shop\ShopOptionType;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\former\aZGrapesJsWidget;
use zetsoft\widgets\incores\ZIRadioGroupWidget;
use function Spatie\array_keys_exist;

class Element extends ZFrame
{
    public $shop_products;
    public $shop_elements;
    public $core_categories;
    public $core_option_types;
    public $core_options;
    public const cart_type = [
        'add' => 'add',
        'set' => 'set',
    ];

    #endregion

#region init

    public function init()
    {

        $this->shop_products = collect(ShopProduct::find()->asArray()->all());
        $this->shop_elements = collect(ShopElement::find()->asArray()->all());
        $this->core_categories = collect(ShopCategory::find()->asArray()->all());
        $this->core_option_types = collect(ShopOptionType::find()->asArray()->all());
        $this->core_options = collect(ShopOption::find()->asArray()->all());
        parent::init();
    }

    public function test()
    {
        //$this->AfterEditCoreCategoryTest();
        // $this->SaveElementsTest();   //Error: $model
        // $this->CombinationsTest();


    }


#endregion


    #region combinate element

    //category edit bo'lganda element name lari boshqatdan generatsiya bo'lishi kerak
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    public function AfterEditCoreCategoryTest()
    {
        $model_id = 97;
        $data = $this->afterEditCorecategory($model_id);
        vd($data);
    }

    public function afterEditCorecategory($model_id)
    {
        Az::start(__FUNCTION__);
        ZArrayHelper::setValue(Az::$app->params, 'paramIsUpdate', true);


        $shop_products = $this->shop_products
            ->where('shop_category_id', $model_id);

        foreach ($shop_products as $shop_product) {
            Az::$app->market->product->saveElements($shop_product);
        }

        return "ok";
    }

    public function saveElementsTest()
    {
        $model = ShopProduct::findOne(38);

        $data = $this->saveElements($model);
        vd($data);
    }

    public function saveElements($model)
    {
        Az::start(__FUNCTION__);
        Az::debug('Rendering elements from product id:' . $model->id);
        $category = $this->core_categories->where('id', $model->shop_category_id)->first();
        $core_option_type_ids = [];
        if ($category !== null && $category['shop_option_type'] !== null)
            $data = $category['shop_option_type'];

        if (!isset($data) || !is_array($data))
            return 0;

        if (count($data) > 0)
            foreach ($data as $item) {
                if (ZArrayHelper::keyExists('is_combination', $item)) {
                    $core_option_type_ids[] = (int)ZArrayHelper::getValue($item, 'shop_option_type_id');
                }
            }

        if (empty($core_option_type_ids)) {
            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {
                /*$element = ShopElement::find()
                    ->where([
                        'shop_product_id' => $model->id
                    ])->one();*/

                $element = $this->shop_elements
                    ->where('shop_product_id', $model->id)
                    ->first();

            }

            if ($this->emptyVar($element))
                $element = new ShopElement();
            if (is_array($element))
                $element = $this->toObject(ShopElement::class, $element);

            $element->shop_product_id = $model->id;

            $element->active = true;
            /*$element->name = '';
            $element->name .= $model->name . ', ' . $category['name'];
            Az::debug('Updated element name is: ' . $element->name);*/
            $element->configs->rules = [[validatorSafe]];
            if ($element->save())
                Az::debug('Element saved successfully!');
            else {
                Az::error('Failed!');
                vd($element->errors());
            }

            return 0;
        }

        $combination_core_option_types = $this->core_option_types
            ->whereIn('id', $core_option_type_ids);
        var_dump($combination_core_option_types);
        $initial_arr = [];
        foreach ($combination_core_option_types as $core_option_type) {
            $options = $this->core_options
                ->where('shop_option_type_id', $core_option_type['id'])->whereIn('id', $model->shop_option_ids);
            $sub_arr = [];
            foreach ($options as $option)
                $sub_arr[] = $option['id'];

            $initial_arr[] = $sub_arr;
        }
//       vdd($initial_arr);
        $result_arr = $this->combinations($initial_arr);

        foreach ($result_arr as $item) {

            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {

                $elements = $this->shop_elements
                    ->where('shop_product_id', $model->id);
                Az::debug('Updating element\'s id:' . $element->id);

                foreach ($elements as $e) {
                    $bool = true;
                    //if (is_array($e->shop_option_ids))
                    //vdd($e);
//                    $shop_option_ids = json_decode($e['shop_option_ids'], true);

                    foreach ($e['shop_option_ids'] as $option_id) {
                        if (!in_array($option_id, $item)) {
                            $bool = false;
                            continue;
                        }
                    }
                    if ($bool) {
                        $element = $e;
                        break;
                    }

                }
                if ($element == null) {
                    vdd(ZArrayHelper::getValue($item, 'id') . "li shunaqa element topilmadi bazadan");
                }
            }

            $element->shop_option_ids = $item;

            $element->active = true;

            $element->name = '';

            $ops = $this->core_options
                ->whereIn('id', $item);

            $element->name .= $model->name . ' ';
            echo 'name = ' . $model->name . ' ';
            $key = 0;
            foreach ($ops as $op) {
                $element->name .= $op['name'];
                if ($ops->count() - 1 !== $key)
                    $element->name .= ', ';

                $key++;
            }

            $element->name .= ', ' . $category['name'];
            Az::debug('Generated element name: ' . $element->name);
            $element->shop_product_id = $model->id;
            $element->configs->rules = [[validatorSafe]];
            if ($element->save()) {
                Az::debug('Element saved successfully!');
                Az::debug('Element id: ' . $element->id);
                Az::debug('Element name: ' . $element->name);
            } else {
                Az::error('Failed!');
                vd($element->errors);
            }
        }
    }


    public function saveElementNewTest()
    {
        $model = ShopProduct::findOne([
            'id' => 4
        ]);

        if ($model === null)
            $model = new ShopProduct();


        vd($model->shop_option);



        if (!empty($model->shop_option))

        foreach ($model->shop_option as $array) {
          //  if ((int)$array['is_combination'] === 1)
                
                
        }

        
        $model->shop_brand_id = 49;
        $model->save();
    }

    /**
     *
     * Function  saveElementNew
     * @param ShopProduct $model
     * @throws \Exception
     */
    public function saveElementNew($model)
    {
        Az::start(__FUNCTION__);
        Az::debug('Rendering elements from product id:' . $model->id);

        $shop_option = $model->shop_option;

        /*
         *
         *
        $combination_core_option_types = $this->core_option_types
            ->whereIn('id', $core_option_type_ids);
        var_dump($combination_core_option_types);
        $initial_arr = [];
        foreach ($combination_core_option_types as $core_option_type) {
            $options = $this->core_options
                ->where('shop_option_type_id', $core_option_type['id'])->whereIn('id', $model->shop_option_ids);
            $sub_arr = [];
            foreach ($options as $option)
                $sub_arr[] = $option['id'];

            $initial_arr[] = $sub_arr;
        }
        $result_arr = $this->combinations($initial_arr);

        foreach ($result_arr as $item) {

            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {

                $elements = $this->shop_elements
                    ->where('shop_product_id', $model->id);
                Az::debug('Updating element\'s id:' . $element->id);

                foreach ($elements as $e) {
                    $bool = true;
                    foreach ($e['shop_option_ids'] as $option_id) {
                        if (!in_array($option_id, $item)) {
                            $bool = false;
                            continue;
                        }
                    }
                    if ($bool) {
                        $element = $e;
                        break;
                    }

                }
                if ($element == null) {
                    vdd(ZArrayHelper::getValue($item, 'id') . "li shunaqa element topilmadi bazadan");
                }
            }

            $element->shop_option_ids = $item;

            $element->active = true;

            $element->name = '';

            $ops = $this->core_options
                ->whereIn('id', $item);

            $element->name .= $model->name . ' ';
            echo 'name = ' . $model->name . ' ';
            $key = 0;
            foreach ($ops as $op) {
                $element->name .= $op['name'];
                if ($ops->count() - 1 !== $key)
                    $element->name .= ', ';

                $key++;
            }

            $element->name .= ', ' . $category['name'];
            Az::debug('Generated element name: ' . $element->name);
            $element->shop_product_id = $model->id;
            $element->configs->rules = [[validatorSafe]];
            if ($element->save()) {
                Az::debug('Element saved successfully!');
                Az::debug('Element id: ' . $element->id);
                Az::debug('Element name: ' . $element->name);
            } else {
                Az::error('Failed!');
                vd($element->errors);
            }
        }
         * */
    }


    public function test_element($model)
    {
        $this->saveElements_U($model);
    }

    public function saveElements_U($model)
    {
        Az::start(__FUNCTION__);
        Az::debug('Rendering elements from product id:' . $model->id);
        $category = $this->core_categories->where('id', $model->shop_category_id)->first();
        $core_option_type_ids = [];
        if ($category !== null && $category['shop_option_type'] !== null)
            $data = json_decode($category['shop_option_type'], true);

        if (!isset($data) || !is_array($data))
            return 0;

        if (count($data) > 0)
            foreach ($data as $item) {
                if (ZArrayHelper::keyExists('is_combination', $item)) {
                    $core_option_type_ids[] = (int)$item['shop_option_type_id'];
                }
            }

        if (empty($core_option_type_ids)) {
            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {
                /*$element = ShopElement::find()
                    ->where([
                        'shop_product_id' => $model->id
                    ])->one();*/


                $element = $this->shop_elements
                    ->where('shop_product_id', $model->id)
                    ->first();

            }

            if ($this->emptyVar($element))
                $element = new ShopElement();
            if (is_array($element))
                $element = $this->toObject(ShopElement::class, $element);

            $element->shop_product_id = $model->id;

            $element->active = true;
            /*$element->name = '';
            $element->name .= $model->name . ', ' . $category['name'];
            Az::debug('Updated element name is: ' . $element->name);*/
            $element->configs->rules = [[validatorSafe]];
            if ($element->save())
                Az::debug('Element saved successfully!');
            else {
                Az::error('Failed!');
                vd($element->errors());
            }

            return 0;
        }

        $combination_core_option_types = $this->core_option_types
            ->whereIn('id', $core_option_type_ids);
        var_dump($combination_core_option_types);
        $initial_arr = [];
        foreach ($combination_core_option_types as $core_option_type) {
            $options = $this->core_options
                ->where('shop_option_type_id', $core_option_type['id'])->whereIn('id', $model->shop_option_ids);
            $sub_arr = [];
            foreach ($options as $option)
                $sub_arr[] = $option['id'];

            $initial_arr[] = $sub_arr;
        }
//       vdd($initial_arr);
        $result_arr = $this->combinations($initial_arr);

        foreach ($result_arr as $item) {

            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {

                $elements = $this->shop_elements
                    ->where('shop_product_id', $model->id);
                Az::debug('Updating element\'s id:' . $element->id);

                foreach ($elements as $e) {
                    $bool = true;
                    //if (is_array($e->shop_option_ids))
                    //vdd($e);
//                    $shop_option_ids = json_decode($e['shop_option_ids'], true);

                    foreach ($e['shop_option_ids'] as $option_id) {
                        if (!in_array($option_id, $item)) {
                            $bool = false;
                            continue;
                        }
                    }
                    if ($bool) {
                        $element = $e;
                        break;
                    }

                }
                if ($element == null) {
                    vdd(ZArrayHelper::getValue($item, 'id') . "li shunaqa element topilmadi bazadan");
                }
            }

            $element->shop_option_ids = $item;

            $element->active = true;

            $element->name = '';

            $ops = $this->core_options
                ->whereIn('id', $item);

            $element->name .= $model->name . ' ';
            echo 'name = ' . $model->name . ' ';
            $key = 0;
            foreach ($ops as $op) {
                $element->name .= $op['name'];
                if ($ops->count() - 1 !== $key)
                    $element->name .= ', ';

                $key++;
            }

            $element->name .= ', ' . $category['name'];
            Az::debug('Generated element name: ' . $element->name);
            $element->shop_product_id = $model->id;
            $element->configs->rules = [[validatorSafe]];
            if ($element->save()) {
                Az::debug('Element saved successfully!');
                Az::debug('Element id: ' . $element->id);
                Az::debug('Element name: ' . $element->name);
            } else {
                Az::error('Failed!');
                vd($element->errors);
            }
        }
    }


    public function saveElements_old($model)
    {
//        Az::start(__FUNCTION__);
//        Az::debug('Rendering elements from product id:' . $model->id);
        $category = $this->core_categories->where('id', $model->shop_category_id)->first();
        $core_option_type_ids = [];
        if ($category !== null && $category['shop_option_type'] !== null)
            $data = json_decode($category['shop_option_type'], true);

        if (!isset($data) || !is_array($data))
            return 0;

        if (count($data) > 0)
            foreach ($data as $item) {
                if (ZArrayHelper::keyExists('is_combination', $item)) {
                    $core_option_type_ids[] = (int)$item['shop_option_type_id'];
                }
            }

        if (empty($core_option_type_ids)) {
            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {
                /*$element = CoreElement::find()
                    ->where([
                        'shop_product_id' => $model->id
                    ])->one();*/


                $element = $this->shop_elements
                    ->where('shop_product_id', $model->id)
                    ->first();

                /*   Az::debug('Updating element\'s id:' . $element->id);*/
            }

            if ($this->emptyVar($element))
                return 0;
            if (is_array($element))
                $element = $this->toObject(ShopElement::class, $element);

            $element->shop_product_id = $model->id;

            $element->active = true;
            /*$element->name = '';
            $element->name .= $model->name . ', ' . $category['name'];
            Az::debug('Updated element name is: ' . $element->name);*/
            $element->configs->rules = [[validatorSafe]];
            if ($element->save())
                Az::debug('Element saved successfully!');
            else {
                Az::error('Failed!');
                vd($element->errors());
            }

            return 0;
        }

        $combination_core_option_types = $this->core_option_types
            ->whereIn('id', $core_option_type_ids);
        foreach ($combination_core_option_types as $core_option_type) {
            $options = $this->core_options
                ->where('shop_option_type_id', $core_option_type['id'])->whereIn('id', $model->shop_option_ids);
            $sub_arr = [];
            foreach ($options as $option)
                $sub_arr[] = $option['id'];

            $initial_arr[] = $sub_arr;
        }

        $result_arr = $this->combinations($initial_arr);

        foreach ($result_arr as $item) {

            $element = new ShopElement();
            if (ZArrayHelper::getValue(Az::$app->params, 'paramIsUpdate')) {

                $elements = $this->shop_elements
                    ->where('shop_product_id', $model->id);
                Az::debug('Updating element\'s id:' . $element->id);

                foreach ($elements as $e) {
                    $bool = true;
                    //if (is_array($e->shop_option_ids))
                    //vdd($e);
//                    $shop_option_ids = json_decode($e['shop_option_ids'], true);

                    foreach ($e['shop_option_ids'] as $option_id) {
                        if (!in_array($option_id, $item)) {
                            $bool = false;
                            continue;
                        }
                    }
                    if ($bool) {
                        $element = $e;
                        break;
                    }

                }
                if ($element == null) {
                    vdd(ZArrayHelper::getValue($item, 'id') . "li shunaqa element topilmadi bazadan");
                }
            }

            $element->shop_option_ids = $item;

            $element->active = true;

            $element->name = '';

            $ops = $this->core_options
                ->whereIn('id', $item);

            $element->name .= $model->name . ' ';
            echo 'name = ' . $model->name . ' ';
            $key = 0;
            foreach ($ops as $op) {
                $element->name .= $op['name'];
                if ($ops->count() - 1 !== $key)
                    $element->name .= ', ';

                $key++;
            }

            $element->name .= ', ' . $category['name'];
            Az::debug('Generated element name: ' . $element->name);
            $element->shop_product_id = $model->id;
            $element->configs->rules = [[validatorSafe]];
            if ($element->save()) {
                Az::debug('Element saved successfully!');
                Az::debug('Element id: ' . $element->id);
                Az::debug('Element name: ' . $element->name);
            } else {
                Az::error('Failed!');
                vd($element->errors);
            }
        }
    }
    
    //Ravshanov Sardor
    //telegram=@SardorRavshanov
    //this function combinations
    public function combinationsTest()
    {
        $arrays = ["1", '2', 3];
        $i = 0;
        $data = $this->combinations($arrays, $i);
        vd($data);
    }

    public function combinations($arrays, $i = 0)
    {
        Az::start(__FUNCTION__);
        if (!isset($arrays[$i])) {
            return array();
        }
        if (empty($arrays[$i])) {
            $i++;
            if (!isset($arrays[$i])) {
                return array();
            }
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = $this->combinations($arrays, $i + 1);
        $result = [];

        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) {

            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }

        return $result;
    }

#endregion

    #region Fix table

    public function clearElement()
    {
        $elements = ShopElement::find()->all();
        /** @var ShopElement $element */
        foreach ($elements as $element) {
            $element->configs->showDeleted = true;
            $element->columns();
            $element->delete();
        }


    }

    public function createElements()
    {
        $products = ShopProduct::find()->all();

        foreach ($products as $product) {
            if (empty($product))
                continue;
            if (empty($product->shop_category_id))
                continue;
            $category = ShopCategory::findOne($product->shop_category_id);
            if ($category === null) {
                $product->delete();
                continue;
            }

            $this->saveElements($product);
        }
    }

    #endregion

}
