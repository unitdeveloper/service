<?


/**
 *
 * Author:  Shakhrizod Nurmukhammadov
 *
 */

namespace zetsoft\service\cores;


use Doctrine\Instantiator\Instantiator;
use http\Exception\BadMethodCallException;
use http\Exception\InvalidArgumentException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\rbac\DbManager;
use yii\rest\Serializer;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use zetsoft\dbcore\ALL\CoreRoleCore;
use zetsoft\dbcore\ALL\UserCore;
use zetsoft\dbitem\core\ServiceItem;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\former\auth\AuthRegisterForm;
use zetsoft\models\core\CoreRole;
use zetsoft\models\core\CoreSession;
use zetsoft\models\core\CoreSessionUser;
use zetsoft\models\shop\ShopOrderItem;
use zetsoft\models\user\User;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;


/**
 * Class ZAuth
 * @package zetsoft\service
 *
 * @property DbManager $auth
 * @property User $identity
 */
class Rest extends ZFrame
{
    public $serializer = Serializer::class;

    public $dataFilter;

    public $prepareDataProvider;

    public $query;

    public $requestParams;

    public $id;

    public $pageSize = 0;

    public function init()
    {
        parent::init();
        $this->requestParams = Yii::$app->request->bodyParams;

        if (empty($this->requestParams)) {
            $this->requestParams = Yii::$app->request->queryParams;
        }
        $this->id = Az::$app->smart->model->httpGet('id');

        $this->modelClass = Az::$app->params['jsonModel'];

        $this->query = $this->modelClass::find();

        if (isset($this->requestParams['with'])) {
            foreach ($this->requestParams['with'] as $key => $item) {
                if (!$this->emptyOrNullable($item)) {
                    $this->query = $this->query->with([
                        lcfirst(ZInflector::camelize($key)) => function (ZActiveQuery $query) use ($item) {
                            $query->select($item);
                        }]);
                } else {
                    $this->query = $this->query->with($key);
                }
            }
        }

        if (isset($this->requestParams['groupBy'])) {
            $this->query = $this->query->groupBy($this->requestParams['groupBy']);
        }
        if (isset($this->requestParams['cache'])) {
            $this->query = $this->query->cache($this->requestParams['cache']);
        }
        if (isset($this->requestParams['select'])) {
            $this->query = $this->query->select($this->requestParams['select']);
        }
        if (isset($this->requestParams['query'])) {
            $query = $this->requestParams['query'];
            $this->query($query);
        }
        if (isset($this->requestParams['sort'])) {
            $explode = explode('|', $this->requestParams['sort']);
            foreach ($explode as $item) {
                $sort = '';
                $column = str_replace('-', '', $item);
                switch ($item) {
                    case ZStringHelper::find($item, '-'):
                        $sort = SORT_DESC;
                        break;
                    case !ZStringHelper::find($item, '-'):
                        $sort = SORT_ASC;
                        break;


                }
                $this->query = $this->query->addOrderBy([$column => $sort]);
            }
        }
        if (isset($this->requestParams['indexBy'])) {
            $this->query = $this->query->indexBy($this->requestParams['indexBy']);
        }
    }

    private function query($query)
    {
        foreach ($query as $key => $item) {
            if ((new $this->modelClass)->columns[$key]->dbType === dbTypeJsonb) {
                $this->query = $this->query->whereJsonIn($key, $item);
                continue;
            }
            $explode = explode('||', $item);

            if (count($explode) >= 1) {
                foreach ($explode as $value) {
                    $val = $value;
                    if ($value === 'null') {
                        $val = null;
                    }
                    switch (true) {
                        case ZStringHelper::find($val, '>='):
                            $val = str_replace('>=', '', $val);
                            $this->query = $this->query->andWhere(['>=', $key, $val]);
                            break;
                        case ZStringHelper::find($val, '<='):
                            $val = str_replace('<=', '', $val);
                            $this->query = $this->query->andWhere(['<=', $key, $val]);
                            break;
                        case ZStringHelper::find($val, '>'):
                            $val = str_replace('>', '', $val);
                            $this->query = $this->query->andWhere(['>', $key, $val]);
                            break;
                        case ZStringHelper::find($val, '<') :
                            $val = str_replace('<', '', $val);
                            $this->query = $this->query->andWhere(['<', $key, $val]);
                            break;
                        case ZStringHelper::find($val, '!='):
                            $val = str_replace('!=', '', $val);
                            if ($val === "null") {
                                $this->query = $this->query->andWhere(['not', [$key => null]]);
                            } else
                                $this->query = $this->query->andWhere(['not', [$key => $val]]);
                            break;
                        case ZStringHelper::find($val, 'like'):
                            $val = str_replace('like', '', $val);
                            $val = explode('|', $val);
                            $this->query = $this->query->andWhere(['like', $key, $val]);
                            break;
                        default:
                            $explode = explode('|', $item);
                            $index = array_search('null', $explode);
                            if ($index !== false) {
                                $explode[$index] = null;
                            }
                            $this->query = $this->query->andWhere([$key => $explode]);
                    }
                }
            }
        }
        unset($this->requestParams['query']);
    }

    /**
     *
     * Function  index
     * @return  array|mixed|object
     * @throws \yii\base\InvalidConfigException
     *
     * -- COMMANDS --
     *
     * sort       = <URL>?sort=id
     * @example  http://market.zetsoft.uz/rest/user/index.aspx?sort=-id
     * Multiple   = <URL>?sort=-name|id|-date
     *
     * query      = <URL>?query[id]=5
     * @example  http://market.zetsoft.uz/rest/user/index.aspx?query[id]=5
     * @example  http://market.zetsoft.uz/rest/user/index.aspx?query[id]=2|4
     * @example  http://market.zetsoft.uz/rest/user/index.aspx?query[name]=<operator>||zor
     *
     * OPERATOR LIST
     *
     * ------------------
     * like , => , =< , < , >
     * ------------------
     *
     * pagination = <URL>?per-page=2&page=1 @example  http://market.zetsoft.uz/rest/user/index.aspx?per-page=2&page=1
     *
     * select     = <URL>?select=id,name    @example  http://market.zetsoft.uz/rest/user/index.aspx?select=id,name
     *
     * relation   = <URL>?with[<modelName>]=id,name if you want to get all columns of relation you can use with[<modelName>]
     * @example http://market.zetsoft.uz/rest/user/index.aspx?with[UserCompany]=id,name&query[user_company_id]=2&with[placeRegion]=id,name
     *
     * cache      = <URL>?cache=<bool|int>,
     * @example  http://market.zetsoft.uz/rest/user/index.aspx?cache=true
     *
     * groupBy    = <URL>?orderBy=<string>
     * @example  http://market.zetsoft.uz/rest/user/index.aspx?groupBy=id,name
     *
     * indexBy    = <URL>?indexBy=<string>  @example  http://market.zetsoft.uz/rest/user/index.aspx?indexBy=id
     */
    public function index()
    {

        /* @var $modelClass \yii\db\BaseActiveRecord */
        if (!Az::$app->request->isPost && !Az::$app->request->isGet) {
            return $this->error('Not Allowed Method. Get allowed', 405);
        }

        $this->query = $this->query->asArray();

        if (isset($this->requestParams['per-page'])) {
            $this->pageSize = $this->requestParams['per-page'];
        }

        $pagination = [
            'params' => $this->requestParams,
            'pageSize' => $this->pageSize
        ];

        $provider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => $pagination,
        ]);
//        $provider->prepare();
//        $val = $provider->models;
//        foreach ($val as $i => $items) {
//            foreach ($items as $key => $item) {
//                if (!is_array($item)) {
//                    if (ZStringHelper::find($item, '[')) {
//                        $val[$i][$key] = \Safe\json_decode($item);
//                    }
//                }
//            }
//        }
//        vdd($val);
        $return['data'] = $provider->models;
        $return['meta'] = [
            'totalCount' => $provider->pagination->totalCount,
            'pageCount' => $provider->pagination->pageCount,
            'currentPage' => $provider->pagination->page,
            'perPage' => $provider->pagination->pageSize,
        ];
        return $return;
    }

    public function view()
    {
        if (Az::$app->request->isPost || Az::$app->request->isGet) {
            if ($this->id) {
                $this->query = $this->query->asArray();
                $this->query = $this->query->andWhere(['id' => $this->id])->one();
                if ($this->query !== null) {
                    return $this->query;
                }
                return $this->error('Model not found 404', 404);
            }
            return $this->error('id is missing', 400);
        }
        return $this->error('Not Allowed Method. Post and Get allowed', 405);
    }

    public function attr()
    {
        if (!Az::$app->request->isGet)
            return $this->error('Not Allowed Method. Only Get allowed', 405);


        /** @var Models $model */
        $model = new $this->modelClass();

        return $model->attributeLabels();

    }


    public function data()
    {
        if (!Az::$app->request->isGet)
            return $this->error('Not Allowed Method. Only Get allowed', 405);


        /** @var Models $model */
        $model = new $this->modelClass();

        $return = [];

        foreach ($model->columns as $key => $column) {
            if (!empty($column->data))
                if (is_array($column->data))
                    $return[$key] = $column->data;
        }

        return $return;

    }

    public function type()
    {
        if (!Az::$app->request->isGet)
            return $this->error('Not Allowed Method. Only Get allowed', 405);


        /** @var Models $model */
        $model = new $this->modelClass();

        $return = [];

        foreach ($model->columns as $key => $column) {
            $return[$key] = $column->dbType;
        }

        return $return;

    }

    public function update()
    {
        if (Az::$app->request->isPatch || Az::$app->request->isPut) {
            $id = Az::$app->smart->model->httpGet('id');
            if ($this->id) {
                $model = $this->modelClass::findOne($id);
                if ($model) {
                    $model->load(Az::$app->getRequest()->getBodyParams(), '');
                    if ($model->save() === false && !$model->hasErrors()) {
                        return $this->error();
                    }
                    return $model;
                }
                return $this->error('Model not found 404', 404);
            }
            return $this->error('id is missing', 400);
        }
        return $this->error('Not Allowed Method. Patch and Put allowed', 405);
    }

    public function delete()
    {
        if (Az::$app->request->isDelete) {
            if ($this->id) {
                $model = $this->modelClass::findOne($this->id);
                if ($model) {
                    if ($model->delete() === false) {
                        return $this->error();
                    }
                    return true;
                }
                return $this->error('Model not found 404', 404);
            }
            return $this->error('id is missing', 400);
        }
        return $this->error('Not Allowed Method. Only Delete method allowed', 405);
    }

    public function create()
    {
        if (Az::$app->request->isPost || Az::$app->request->isPut) {
            /* @var $model \yii\db\ActiveRecord */
            $model = new $this->modelClass();


            //start|AsrorZakirov|2020-10-09


            //end|AsrorZakirov|2020-10-09

            $model->load(Az::$app->getRequest()->getBodyParams(), '');
            if ($model->save()) {
                Az::$app->response->setStatusCode(201);
                return $model;
            }

            return $this->error();
        }
        return $this->error('Not Allowed Method. Post and Put allowed', 405);
    }

    private function error($message = 'Something went wrong', int $code = 700)
    {
        Az::$app->response->setStatusCode($code);
        return [
            'error' => true,
            'message' => $message
        ];
    }
}

