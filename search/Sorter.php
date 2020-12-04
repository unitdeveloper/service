<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\search;


use dosamigos\fileupload\FileUploadUI;
use Spatie\Typed\Collection;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;

class Sorter extends ZFrame
{

    public $data;
    public $return;

    private $sort;

    public function test(){
          vdd($this->testPagination());
    }

    private function testItem(){
        $this->data = Az::$app->market->product->allProducts();
        $this->sort = 'name,-min_price';
        return $this->item();
    }

    /**
     *
     * Function  testPagination
     * For test spatie pagination
     * @throws \yii\base\ErrorException
     * @return Collection
     */
    private function testPagination(){
        $this->data = Az::$app->market->product->allProducts();
        $collection = new Collection($this->data);
        $a = $collection->simplePagination(5);

        return $a;

    }


    /**
     *
     * Function  item
     * Ixtiyoriy class obyektlari arrayini sortlaydi
     * @return  array|\Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     * Array servicedagi $data ga beriladi
     */

    public function item()
    {
        $this->return = null;
        $sort = $this->httpGet('sort');

        if ($this->isCLI())
            $sort = $this->sort;

        $collection = collect($this->data);

        if (empty($sort)) return $collection;
        $sorts = explode(',', $sort);
        $a = '';
        foreach ($sorts as $sort) {

            if (ZStringHelper::startsWith($sort, '-')) {
                $sort = ltrim($sort, $sort[0]);
                $collection = $collection->sortByDesc($sort);
            }     else
                $collection = $collection->sortBy($sort);

            //$collection = $sorted->all();

        }
        $a = '';
        /*foreach ($sorts as $sort) {
            if (ZStringHelper::startsWith($sort, '-')) {
                $sort = ltrim($sort, $sort[0]);
                $collection->sortByDesc($sort);
            } else {
                $collection->sortBy($sort);
            }
        }*/

        //$sorted = $collection->sortDesc();

        //$this->return = $collection->values()->all();
      
        return $collection->all();
    }

    public function form()
    {
        $sort = $this->httpGet('sort');
        $sorts = implode('|', $sort);

        $collection = collect($this->data);

        $sorted = $collection->sortDesc();

        $this->return = $sorted->values()->all();

        return null;
    }

    public function model()
    {
        $sort = $this->httpGet('sort');
        $sorts = implode('|', $sort);

        $collection = collect($this->data);

        $sorted = $collection->sortDesc();

        $this->return = $sorted->values()->all();

        return null;
    }

}
