<?php


namespace zetsoft\service\iterate;

use Qaribou\Collection\ImmArray;
use zetsoft\system\kernels\ZFrame;


class Immutable extends ZFrame
{
      public function test()
      {
          $data = $this->loadBigObjects();
          vd($data);
      }

      public function loadBigObjects($data)
      {
            $compressedData = ImmArray::fromArray($data);

            return $compressedData;
      }

}



