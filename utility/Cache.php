<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use yii\caching\TagDependency;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\kernels\ZFrame;

class Cache extends ZFrame
{

    #region Flush

    public function flush()
    {
        $this->flushDbCache();
        $this->flushOpcache();
        $this->flushProvider();
        $this->flushSchema();
    }

    public function flushProvider()
    {
        Az::$app->cache->flush();
        Az::$app->redis->flush();
        Az::$app->file->flush();
        Az::$app->array->flush();
    }

    public function flushSchema()
    {
        Az::$app->db->schema->getTableSchemas();
        Az::$app->db->schema->refresh();
    }

    public function flushDbCache()
    {
        $model = Az::$app->smart->migra->scan();
        if (!empty($model)) {
            $table = ZInflector::underscore(bname($model[0]));
            Az::$app->db->schema->getTableSchema($table, true);
            TagDependency::invalidate(Az::$app->cache, $model[0]);
            return true;
        }

        return false;
    }

    public function flushOpcache()
    {

        if (extension_loaded('Zend OPcache'))
        opcache_reset();
    }


    #endregion


    public function opcacheConfigs()
    {
        return opcache_get_configuration();
    }
}
