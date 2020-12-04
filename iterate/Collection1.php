<?php


namespace zetsoft\service\iterate;

use Qaribou\Collection\ImmArray;
use Spatie\CollectionMacros\CollectionMacroServiceProvider;
use Spatie\Typed\Collection;
use zetsoft\models\place\PlaceAdress;
use zetsoft\models\user\UserCompany;
use zetsoft\models\place\PlaceCountry;
use zetsoft\models\shop\ShopOrder;
use zetsoft\models\place\PlaceRegion;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;


class Collection1 extends ZFrame
{
    public $item = 2;
    public $array;
    public $collection;


    public function afterArray()
    {
        $this->collection = collect($this->array);
        $currentItem = $this->collection->after($this->item);
        $this->collection->after($currentItem);
    }


}



