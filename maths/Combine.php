<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\maths;


use kcfinder\fastImage;
use zetsoft\dbitem\shop\ShopCombineItem;
use zetsoft\models\shop\ShopElement;
use zetsoft\models\shop\ShopProduct;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZFrame;

class Combine extends ZFrame
{

    public function test()
    {
        /*   $this->cecilemullerTest();
           $this->fabiocicerchiaTest();
           $this->permutationsTest();*/
        // $this->combinateProductTest();
        $this->combinateProductElementTest();
        //$this->combinationsTest();
        /*   $this->getCombineTest();
           $this->generateCombineTest();*/
    }

    #region combinations


    public function combinateProductElementTest()
    {
        $data = $this->combinateProductElement(4);
        vd($data);

    }


    public function combinateProductElement($id)
    {
        /** @var ShopCombineItem[] $data */
        $data = $this->combinateProduct($id);
        
        foreach ($data as $item) {

            /** @var ShopElement $element */
            $element = new ShopElement();
            $element->shop_product_id = $id;
            $element->option_combine = $item->combine;
            $element->option_simple = $item->simple;
            if (!$element->save())
                return false;
        }

        return true;

    }


    public function combinateProductTest()
    {
        $data = $this->combinateProduct(4);
        vd($data);

    }

    public function combinateProduct(int $id)
    {
        /** @var ShopProduct $product */
        $product = ShopProduct::findOne($id);

        if ($product === null)
            return [];

        $options = $product->shop_option;

        $combiRaw = [];
        $simpleRaw = [];
        if (is_array($options) && !empty($options))
            foreach ($options as $key => $value) {

                $shop_option_id = ZArrayHelper::getValue($value, 'shop_option_id');

                if (ZArrayHelper::getValue($value, 'is_combination'))
                    $combiRaw[] = $shop_option_id;
                else
                    $simpleRaw[] = $shop_option_id;
            }

        $combiOne = $this->combinate($combiRaw);
        $simpleOne = [];
        foreach ($simpleRaw as $simples) {
            $simpleOne = ZArrayHelper::merge((array)$simpleOne,(array)  $simples);
        }


        $return = [];
        foreach ($combiOne as $combi) {

            $item = new ShopCombineItem();
            $item->simple = $simpleOne;
            $item->combine = $combi;
            
            $return[] = $item;
        }

        return $return;
    }


    public function combinationsTest()
    {

        /*
                print_r(
                    $this->combinations(
                        array(
                            array('A1', 'A2', 'A3'),
                            array('C1', 'C2')
                        )
                    )
                );
        */

        $data = $this->combinate([
            ['A1', 'A2', 'A3'],
            ['C1', 'C2']
        ]);

        /** @var ShopProduct $product */
        $product = ShopProduct::findOne(4);
        $options = $product->shop_option;

        $combi = [];
        $simple = [];
        if (is_array($options))
            foreach ($options as $key => $value) {
                if ($value['is_combination'])
                    $combi[] = $value['shop_option_id'];
                else
                    $simple[] = $value['shop_option_id'];
            }

        //vd($combi);
        $allCombi = $this->combinate($combi);
        vd($allCombi);

        $simpleOne = [];
        foreach ($simple as $simples) {
            $simpleOne = ZArrayHelper::merge($simpleOne, $simples);
        }

        //   vd($simpleOne);

        $return = [];

        foreach ($allCombi as $combies) {
            $return[] = ZArrayHelper::merge($combies, $simpleOne);
        }

        vd($return);
        //  vd($data);

    }

    /**
     *
     * Function  combinations
     * @param $arrays
     * @param int $i
     * @return  array
     *
     * https://stackoverflow.com/a/8567199
     */

    public function combinate($arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return array();
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = $this->combinate($arrays, $i + 1);

        $result = array();

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

    #region generateCombine

    public function generateCombineTest()
    {
        foreach ($this->generateCombine([
                'a' => ['A'],
                'b' => ['B'],
                'c' => ['C', 'D'],
                'd' => ['E', 'F', 'G']]
        ) as $c) {
            var_dump($c);
        }

    }

    public function generateCombine(array $array)
    {
        foreach (array_pop($array) as $value) {
            if (count($array)) {
                foreach ($this->generateCombine($array) as $combination) {
                    yield array_merge([$value], $combination);
                };
            } else {
                yield [$value];
            }
        }
    }



    #endregion


    #region GetCombine

    public function getCombineTest()
    {
        $arrayA = array('A1', 'A2', 'A3');
        $arrayB = array('B1', 'B2', 'B3');
        $arrayC = array('C1', 'C2');

        print_r($this->getCombine($arrayA, $arrayB, $arrayC));
    }

    /**
     *
     * Function  getCombine
     * @param mixed ...$arrays
     * @return  array|array[]
     *
     * https://stackoverflow.com/a/59649054
     */

    public function getCombine(...$arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    #endregion

    #region Permute

    public function permutationsTest()
    {
        $results = $this->permutations(array('foo', 'bar', 'baz'));
        print_r($results);
    }

    /**
     *
     * Function  permutations
     * @param $array
     * @return  array
     *
     * https://stackoverflow.com/questions/10222835/get-all-permutations-of-a-php-array
     */
    public function permutations($array)
    {
        $result = [];

        $recurse = function ($array, $start_i = 0) use (&$result, &$recurse) {
            if ($start_i === count($array) - 1) {
                $result[] = $array;
            }

            for ($i = $start_i, $iMax = count($array); $i < $iMax; $i++) {
                //Swap array value at $i and $start_i
                $t = $array[$i];
                $array[$i] = $array[$start_i];
                $array[$start_i] = $t;

                //Recurse
                $recurse($array, $start_i + 1);

                //Restore old order
                $t = $array[$i];
                $array[$i] = $array[$start_i];
                $array[$start_i] = $t;
            }
        };

        $recurse($array);

        return $result;
    }


    #endregion


    #region fabiocicerchia


    /**
     * Generate all the possible combinations among a set of nested arrays.
     *
     * @param array $data The entrypoint array container.
     * @param array $all The final container (used internally).
     * @param array $group The sub container (used internally).
     * @param mixed $val The value to append (used internally).
     * @param int $i The key index (used internally).
     */
    public function fabiocicerchia(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
    {
        $keys = array_keys($data);
        if (isset($value) === true) {
            array_push($group, $value);
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];
            foreach ($currentElement as $val) {
                $this->fabiocicerchia($data, $all, $group, $val, $i + 1);
            }
        }

        return $all;
    }

    public function fabiocicerchiaTest()
    {
        $data = array(
            array('a', 'b'),
            array('e', 'f', 'g'),
            array('w', 'x', 'y', 'z'),
        );

        $combos = $this->fabiocicerchia($data);
        print_r($combos);
    }


    #endregion


    #region cecilemuller

    /**
     *
     * Function  cecilemuller
     * @param $arrays
     * @return  array|array[]
     *
     * https://gist.github.com/cecilemuller/4688876#file-get_combinations-php-L17
     */

    public function cecilemuller($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }


    public function cecilemullerTest()
    {
        $combinations = $this->cecilemuller(
            array(
                'item1' => array('A', 'B'),
                'item2' => array('C', 'D'),
                'item3' => array('E', 'F'),
            )
        );

        var_dump($combinations);
    }

    #endregion

}
