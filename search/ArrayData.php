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


use zetsoft\system\actives\ZActiveData;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\kernels\ZFrame;

class ArrayData extends ZFrame
{

    #region Vars

    /* @var ZActiveQuery $query */
    public $query;

    /* @var ZActiveData $provider */
    public $provider;

    public $page;
    public $skip;

    #endregion

    #region Main

    public function run()
    {
        $this->provider = new ZActiveData([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

// get the posts in the current page
        return $this->provider->getModels();
    }

    #endregion

}
