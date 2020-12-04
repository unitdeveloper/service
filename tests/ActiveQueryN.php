<?php


namespace zetsoft\service\tests;

use yii\db\ActiveQuery as ActiveQueryAlias;
use zetsoft\system\helpers\ZVarDumper;

/**
 * CachedActiveQuery represents a ActiveQuery with cache.
 */
class ActiveQueryN extends ActiveQueryAlias
{

    public function whereJsonOrIn(string $column, $value)
    {
        $data = false;
        if (is_array($value) && !empty($value)) {
            foreach ($value as $key => $item) {
                $data = $this->andWhere("{$column} ?| '{\"{$key}\": \"{$item}\"}'");
            }
        } else {
            $value = ZVarDumper::search($value);
            $data = $this->andWhere("$column ?| $value");
        }
        return $data;
    }

    public function whereJsonAndIn(string $column, array $value)
    {

    }


}