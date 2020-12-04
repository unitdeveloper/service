<?

namespace zetsoft\service\cores;


use Illuminate\Support\Collection;
use yii\data\ArrayDataProvider;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\models\dyna\DynaChess;
use zetsoft\models\dyna\DynaChessItem;
use zetsoft\models\dyna\DynaChessQuery;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\actives\ZDynamicModel;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\widgets\charts\ZChartFormWidget;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\values\ZHtmlWidget;
use function GuzzleHttp\json_decode;


/**
 *
 *
 * Class Chess
 * @package zetsoft\service\cores
 *
 *
 * @author Daho
 *
 *
 */
class Chess extends ZFrame
{
    #region Vars
    public int $id = 0;
    public $query = null;
    public ?DynaChess $chess = null;
    public array $filter = [];

    public ?ZDynamicModel $dynamicModel = null;
    public ?ZDynamicModel $filterModel = null;
    public ?ALLApp $filterApp = null;
    public array $data = [];
    public ?ArrayDataProvider $provider = null;
    public array $attributes = [];
    public bool $chart = false;

    public ?Collection $chess_items = null;

    public int $recordsCount = 0;
    public ?ZDynamicModel $summary = null;
    public ?string $summaryCode = null;
    #endregion

    #region Layout
    public $layout = [
        'footer' => <<<HTML
    <table class="kv-grid-table table table-hover table-striped table-sm">    
    <tr class="tr-dynawidget w95">
        <td class="kv-align-center kv-align-middle" style="width:50px;">
        {summarytext}
        </td>
        {summarycode}
    </tr>
    </table>
HTML,
        'td' => <<<HTML
     <td class="kv-align-center kv-align-middle" style="width:{width};">{data}</td>
HTML,
    ];
    #endregion


    #region Run
    /**
     *
     * Function  run
     * @throws \Exception
     * @author Daho
     */
    public function run()
    {
        $this->paramSet('chess_id', $this->id);
        $this->chess = DynaChess::findOne($this->id);

        if (empty($this->chess))
            throw new \RuntimeException(Az::l('Отчет не найден.'), 500);

        $this->chess_items = collect(DynaChessItem::find()
            ->where([
                'dyna_chess_id' => $this->id
            ])
            ->orderBy('sort')
            ->asArray()
            ->all()
        );


        $this->createModel();
        $this->summary = clone $this->dynamicModel;
        //vdd('lk');
        $this->generateData();
        $this->summaryCode();
        $this->modifiteFilterModel();


    }
    #endregion
    #region dynamicModel

    public function createModelTest()
    {
        $this->id = 2;
        vdd($this->createModel()->columns);
    }

    /**
     *
     * Function  createModel generates dynamic model for report
     * @return bool
     * @throws \Exception
     * @author Daho
     */
    public function createModel()
    {
        $app = new ALLApp();

        $filter_app = new ALLApp();
        $items = $this->chess_items;

        if ($items->isEmpty())
            throw new \Exception('Вы должны добавить хотя бы одно поле для информации.');
        foreach ($items as $item) {
            if (!$item['active'])
                continue;

            $depend_class = $this->bootFull($item['models']);

            switch (true) {

                case $item['type'] === DynaChessItem::type['native']:

                    /** @var ZActiveRecord $depend */
                    $depend_class = $this->bootFull($this->chess->models);

                    $depend = new $depend_class();

                    $this->paramSet(paramFull, true);

                    $depend->columns();

                    /** @var FormDb $dep_column */
                    $dep_column = ZArrayHelper::getValue($depend->columns, $item['attrib']);
                    if ($dep_column === null)
                        break;
                    $column = new Form();
                    $column->title = $dep_column->title;
                    $column->data = $dep_column->data;
                    $column->ajax = false;

                    if (!empty($item['column_data']))
                        $column->data = $item['column_data'];


                    if (!empty($item['filter_widget']))
                        $column->filterWidget = $item['filter_widget'];


                    $column->dbType = $this->getDbType($dep_column, $item['attrib']);

                    if (!empty($dep_column->widget))
                        $column->widget = $dep_column->widget;

                    $column->group = $item['group'];
                    $column->groupedRow = $item['grouped_row'];

                    if (!empty($item['title']))
                        $column->title = $item['title'];

                    $column->dbType = $dep_column->dbType;
                    if ($column !== null) {
                        $app->columns[$item['name']] = $column;
                        $this->attributes[$item['name']] = ZStringHelper::endsWith($item['attrib'], '_history');
                    }

                    if ($item['filter']) {

                        $filter_column = new Form();
                        $filter_column->title = $column->title;
                        $filter_column->dbType = $column->dbType;
                        $column->data = $dep_column->data;
                        $column->widget = $dep_column->widget;
                        if (ZStringHelper::endsWith($item['attrib'], '_history'))
                            $filter_column->dbType = dbTypeDate;
                        $filter_column = $this->modifiteFilterColumn($filter_column, $dep_column, $item);

                        if (ZStringHelper::endsWith($item['attrib'], '_id')) {
                            $fkTable = strtr($item['attrib'], [
                                '_id' => ''
                            ]);

                            Az::$app->forms->wiData->clean();
                            Az::$app->forms->wiData->model = $depend;
                            Az::$app->forms->wiData->attributes = $item['attrib'];
                            Az::$app->forms->wiData->fkTable = $fkTable;
                            $filter_column->data = Az::$app->forms->wiData->related();
                        }
                        $filter_app->columns[$item['name']] = $filter_column;
                    }
                    break;
                case $item['type'] === DynaChessItem::type['hasOne']:

                    $depend = new $depend_class();
                    /** @var FormDb $dep_column */
                    $dep_column = ZArrayHelper::getValue($depend->columns, $item['attrib']);
                    if ($dep_column === null)
                        break;
                    $column = new Form();
                    $column->title = $dep_column->title;
                    $column->data = $dep_column->data;
                    $column->ajax = false;
                    if (!empty($item['column_data']))
                        $column->data = $item['column_data'];
                    if (!empty($item['filter_widget']))
                        $column->filterWidget = $item['filter_widget'];

                    $column->widget = $dep_column->widget;
                    $column->dbType = $this->getDbType($dep_column, $item['attrib']);
                    $column->widget = $dep_column->widget;
                    $column->group = $item['group'];

                    if (!empty($item['title']))
                        $column->title = $item['title'];

                    $column->dbType = $dep_column->dbType;

                    $column->groupedRow = $item['grouped_row'];
                    $app->columns[$item['name']] = $column;
                    $this->attributes[$item['name']] = ZStringHelper::endsWith($item['attrib'], '_history');
                    if ($item['filter']) {
                        $filter_column = new Form();
                        $filter_column->title = $column->title;
                        $filter_column->dbType = $column->dbType;
                        $filter_column = $this->modifiteFilterColumn($filter_column, $dep_column, $item);
                        if (ZStringHelper::endsWith($item['attrib'], '_history'))
                            $filter_column->dbType = dbTypeDate;
                        $filter_app->columns[$item['name']] = $filter_column;
                    }
                    break;
                case $item['type'] === DynaChessItem::type['hasMany']:
                case $item['type'] === DynaChessItem::type['hasMulti']:
                    $depend = new $depend_class();
                    /** @var FormDb $dep_column */

                    $dep_column = ZArrayHelper::getValue($depend->columns, $item['attrib']);
                    if ($dep_column === null)
                        break;
                    $column = new Form();
                    $column->title = $dep_column->title;
                    $column->dbType = $dep_column->dbType;


                    $column->widget = ZHtmlWidget::class;
                    $column->valueWidget = ZHtmlWidget::class;
                    $column->filterWidget = ZHInputWidget::class;

                    if (!empty($item['filter_widget'])) {
                        $column->filterWidget = $item['filter_widget'];
                    }

                    $column->group = $item['group'];
                    $column->groupedRow = $item['grouped_row'];

                    if (!empty($item['title']))
                        $column->title = $item['title'];

                    $column->dbType = dbTypeInteger;
                    $app->columns[$item['name']] = $column;

                    if ($item['filter']) {
                        $filter_column = new Form();
                        $filter_column->title = $column->title;
                        $filter_column->dbType = $column->dbType;
                        $filter_column = $this->modifiteFilterColumn($filter_column, $dep_column, $item);
                        if (ZStringHelper::endsWith($item['attrib'], '_history'))
                            $filter_column->dbType = dbTypeDate;
                        $filter_app->columns[$item['name']] = $filter_column;
                    }
                    break;
                default:
                    #todo clear
                    #region need clear
                    $column = new Form();
                    $column->title = $item['name'];
                    $column->group = $item['group'];
                    $column->groupedRow = $item['grouped_row'];
                    $column->widget = ZHtmlWidget::class;
                    $column->filterWidget = ZHInputWidget::class;
                    if (!empty($item['filter_widget']) && class_exists($item['filter_widget']))
                        $column->filterWidget = $item['filter_widget'];

                    if (!empty($item['title']))
                        $column->title = $item['title'];
                    #endregion
                    $app->columns[$item['name']] = $column;
                    $this->attributes[$item['name']] = false;
                    if ($item['filter']) {
                        $filter_column = new Form();
                        $filter_column->title = $column->title;
                        $filter_column->dbType = $column->dbType;
                        $filter_column = $this->modifiteFilterColumn($filter_column, null, $item);
                        $filter_app->columns[$item['name']] = $filter_column;
                    }
            }
        }
        $this->dynamicModel = Az::$app->forms->former->model($app);
        $this->filterApp = $filter_app;


    }

    private function createModelColumn($item, string $type)
    {
        $column = new Form();
        $column->title = $item['name'];
        $column->group = $item['group'];
        $column->groupedRow = $item['grouped_row'];
        $column->widget = ZHtmlWidget::class;
        $column->filterWidget = ZHInputWidget::class;

        if (!empty($item['title']))
            $column->title = $item['title'];
    }

    /**
     *
     * Function  modifiteFilterModel
     * @author Daho
     */
    public function modifiteFilterModel()
    {
        if ($this->chart) {
            $column = new Form();
            $column->title = Az::l('Типовая диаграмма');
            $column->widget = ZKSelect2Widget::class;
            $column->data = [
                'line' => Az::l('Линейный график'),
                'lineStack' => Az::l('Линейный стек'),
                'bar' => Az::l('Гистограмма'),
                'pie' => Az::l('Круговая диаграмма'),
            ];
            $this->filterApp->columns['type_chart'] = $column;

            $column = new Form();
            $column->title = Az::l('Тема диаграмма');
            $column->widget = ZKSelect2Widget::class;
            $column->data = [
                'macarons' => Az::l('Тема макаронс'),
                'infographic' => Az::l('Инфографическая тема'),
                'roma' => Az::l('Ромская тема'),
                'shine' => Az::l('Блеск тема'),
                'dark' => Az::l('Темная тема'),
                'vintage' => Az::l('Винтажная тема'),
            ];
            $this->filterApp->columns['theme_chart'] = $column;
        }

        if (!empty($this->filterApp->columns))
            $this->filterModel = Az::$app->forms->former->model($this->filterApp);
    }

    /**
     *
     * Function  modifiteFilterColumn
     * @param Form $filter_column
     * @param FormDb $dep_column
     * @return  Form
     * @author Daho
     */
    private function modifiteFilterColumn(Form $filter_column, ?FormDb $dep_column, array $item)
    {
        $filter_column->widget = $dep_column->widget ?? ZHInputWidget::class;
        $filter_column->filterWidget = $dep_column->widget ?? ZHInputWidget::class;
        $filter_column->options = $dep_column->options ?? [];
        $filter_column->filterOptions = $dep_column->options ?? [];


        if (!empty($dep_column->filterWidget)) {
            $filter_column->widget = $dep_column->filterWidget;
            $filter_column->options = $dep_column->filterOptions;
        }


        if (empty($filter_column->filterWidget))
            $filter_column->filterWidget = ZHInputWidget::class;

        if (!empty($item['filter_widget'])) {
            $filter_column->widget = $item['filter_widget'];
        }

        if (!empty($item['column_data']))
            $filter_column->data = $item['column_data'];

        return $filter_column;

    }

    /**
     *
     * Function  getDbType
     * @param FormDb $column
     * @param string $attribute
     * @return  array|string
     * @author Daho
     */
    private function getDbType(FormDb $column, string $attribute)
    {
        if (!ZStringHelper::endsWith($attribute, '_history'))
            return $column->dbType;

        return dbTypeDateTime;

    }

    public function generateDataTest()
    {

        $this->id = 2;

        $this->chess = DynaChess::findOne($this->id);
        $this->run();
        Az::$app->cores->chess->run();
        $model = Az::$app->cores->chess->dynamicModel;

        $data = Az::$app->cores->chess->data;
        /*vdd($data);*/

    }

    /**
     *
     * Function  generateData
     * @throws \Exception
     * @author Daho
     */
    public function generateData()
    {
        $class = $this->bootFull($this->chess->models);

        /** @var ZActiveRecord[] $records */
        /** @var ZActiveQuery $Q */
        $Q = $class::find();
        /* ->where([
             'deleted_at' => null
         ]);*/


        //vdd('Shu yerda qotib qolyapti');
        $queries = DynaChessQuery::find()
            ->where([
                'dyna_chess_id' => $this->id
            ])
            ->all();

        /** @var DynaChessQuery $query */
        $filters = Az::$app->market->filterForm->getFiltersChess($queries);

        foreach ($filters as $filter) {

            $operator = ZArrayHelper::getValue($filter, 'query');

            switch ($operator) {
                case 'or':
                    ZArrayHelper::remove($filter, 'query');
                    $Q->orWhere($filter);
                    break;
                case 'not':
                    ZArrayHelper::remove($filter, 'query');
                    $Q->andWhere(['not', $filter]);
                    break;

                default:
                    ZArrayHelper::remove($filter, 'query');
                    $Q->andWhere($filter);
                    break;

            }
        }
        // vdd($Q->sql());
        $records = $Q->asArray()->all();

        $data = [];
        foreach ($records as $record) {
            $result = $this->getDataRow($record);
            if ($result instanceof ZDynamicModel)
                $data[] = $result;
        }

        $formulas = $this->chess_items->where('type', 'formula')->all();

        $return = [];


        foreach ($formulas as $formula) {

            foreach ($data as $item) {

                $arr = [];
                foreach ($item->columns as $key => $column) {
                    $val = $item->$key ?? 0;
                    $arr['$' . $key] = (int)$val;

                }
                $result = strtr($formula['formula'], $arr);

                $name = $formula['name'];
                //$result = (int)strip_tags($result);


                $value = Az::$app->maths->mathExecutor->run($result);
                $item->$name = $value;
                $this->summary($formula, (int)$value);


            }

        }
        $this->data = $data;
    }

    /**
     *
     * Function  summary
     * @param array $item
     * @param int $value
     * @author Daho
     */
    private function summary(array $item, int $value)
    {
        if (!$item['summary'])
            return null;
        $key = $item['name'];

        switch ($item['summary_type']) {
            case 'f_sum':
                $this->summary->$key += $value;
                break;
            case 'f_min':
                if ($this->summary->$key > $value)
                    $this->summary->$key = $value;
                break;
            case 'f_max':
                if ($this->summary->$key < $value)
                    $this->summary->$key = $value;
                break;
            case 'f_avg':
                $current = $this->summary->$key;
                $this->summary->$key = ($current * $this->recordsCount + $value) / ($this->recordsCount + 1);
                break;
        }
        ++$this->recordsCount;

    }

    /**
     *
     * Function  summaryCode
     * @author Daho
     */
    private function summaryCode()
    {
        $code = null;
        foreach ($this->summary->attributes as $attribute => $value)
            $code .= strtr($this->layout['td'], [
                '{data}' => $value,
                '{width}' => $this->summary->columns[$attribute]->width
            ]);

        $this->summaryCode = strtr($this->layout['footer'], [
            '{summarytext}' => Az::l('Всего:'),
            '{summarycode}' => $code,

        ]);
    }

    /**
     *
     * Function  getDataRow
     * @param array $record
     * @return  ZDynamicModel|null|bool
     * @throws \Exception
     * @author Daho
     */
    public function getDataRow(array $record)
    {

        $model = clone $this->dynamicModel;
        foreach ($model->columns as $attr => $column) {


            $chess_item = $this->chess_items->where('name', $attr)->first();
            $atrib = $chess_item['attrib'];

            if ($chess_item === null)
                continue;

            $service = $chess_item['service'];
            if (!empty($chess_item['service']) && !is_array($chess_item['service'])) {
                $service = json_decode($chess_item['service']);
            }

            switch (true) {

                case !empty(ZArrayHelper::getValue($service, 'namespace')):

                    $namespace = ZArrayHelper::getValue($service, 'namespace');
                    $service_name = ZArrayHelper::getValue($service, 'service');
                    $method = ZArrayHelper::getValue($service, 'method');
                    if (empty($service_name) || empty($method))
                        break;

//vdd($this->filter);
                    Az::$app->$namespace->$service_name->data($this->filter);
                    $model->$attr = Az::$app->$namespace->$service_name->$method($record, $this->filter);
                    $result = Az::$app->$namespace->$service_name->$method($record, $this->filter);

                    $value = ZArrayHelper::getValue($result, 'value');
                    $valueShow = ZArrayHelper::getValue($result, 'valueShow');
                    if ($valueShow === null)
                        $valueShow = $value;

                    $model->configs->valueShow[$attr] = $valueShow;

                    $model->$attr = $value;
                    $this->summary($chess_item, (int)$value);
                    if ($chess_item['hide_null'] && $value === null)
                        return false;

                    break;

                case $chess_item['type'] === DynaChessItem::type['native']:

                    $model = $this->getCellData($record, $atrib, $attr, $model);
                    $this->summary($chess_item, (int)$model->$attr);
                    if ($chess_item['hide_null'] && $model->$attr === null)
                        return false;
                    break;

                case $chess_item['type'] === DynaChessItem::type['hasOne']:

                    $className = $this->bootFull($chess_item['models']);

                    $rel_items = $className::find()->where([
                        'id' => $record[$chess_item['relate_attr']]
                    ])->one();

                    $value = null;

                    if (!empty($rel_items))
                        $value = $rel_items->$atrib;

                    $this->summary($chess_item, (int)$value);
                    $model->$attr = $value;
                    $itemClass = ZArrayHelper::getValue($chess_item, 'item_class');
                    $itemAttribute = ZArrayHelper::getValue($chess_item, 'item_attr');
                    if (!empty($value)) {
                        $model->$attr = $value;
                        $model->configs->valueShow[$attr] = <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$rel_items->id}&modelClass={$chess_item['models']}&id={$this->id}&itemClass={$itemClass}&itemAttribute={$itemAttribute}" target="_blank">{$value}</a>
HTML;
                    }

                    if ($chess_item['hide_null'] && $model->$attr === null)
                        return false;
                    break;

                case $chess_item['type'] === DynaChessItem::type['hasMany']:

                    $className = $this->bootFull($chess_item['models']);

                    $rel_items = $className::find()->where([
                        $chess_item['relate_attr'] => $record['id']
                    ]);
                    $oper = $chess_item['process'];
                    $value = $this->modifieValueWithProccess($rel_items, $oper, $chess_item['attrib']);
                    $ids = $rel_items->select('id')->asArray()->all();

                    $ids = ZArrayHelper::map($ids, 'id', 'id');

                    $ids = implode('|', $ids);


                    $itemClass = ZArrayHelper::getValue($chess_item, 'item_class');
                    $itemAttribute = ZArrayHelper::getValue($chess_item, 'item_attr');

                    $this->summary($chess_item, (int)$value);

                    if (!empty($value)) {
                        $model->$attr = $value;
                        $model->configs->valueShow[$attr] = <<<HTML
         <a href="/core/dynagrid/details.aspx?ids={$ids}&modelClass={$chess_item['models']}&id={$this->id}&itemClass={$itemClass}&itemAttribute={$itemAttribute}" target="_blank">{$value}</a>
HTML;
                    }

                    if ($chess_item['hide_null'] && $model->$attr === null)
                        return false;
                    break;

                case $chess_item['type'] === DynaChessItem::type['hasMulti']:

                    $className = $this->bootFull($chess_item['models']);

                    $rel_items = $className::find()->where([
                        $chess_item['relate_attr'] => $record['id']
                    ]);

                    $oper = $chess_item['process'];
                    $value = $this->modifieValueWithProccess($rel_items, $oper, $chess_item['attrib']);
                    $ids = $rel_items->select('id')->asArray()->all();

                    $ids = ZArrayHelper::map($ids, 'id', 'id');

                    $ids = implode('|', $ids);


                    $itemClass = ZArrayHelper::getValue($chess_item, 'item_class');
                    $itemAttribute = ZArrayHelper::getValue($chess_item, 'item_attr');

                    $this->summary($chess_item, (int)$value);

                    if (!empty($value)) {
                        $model->$attr = $value;
                        $model->configs->valueShow[$attr] = <<<HTML
                 <a href="/core/dynagrid/details.aspx?ids={$ids}&modelClass={$chess_item['models']}&id={$this->id}&itemClass={$itemClass}&itemAttribute={$itemAttribute}" target="_blank">{$value}</a> 
        HTML;
                    }

                    if ($chess_item['hide_null'] && $model->$attr === null)
                        return false;
                    break;
            }
        }

        return $model;
    }

    /**
     *
     * Function  modifieValueWithProccess
     * @param ZActiveQuery $query
     * @param string $operator
     * @param string $attribute
     * @return  string
     * @throws \Exception
     * @author Daho
     */
    public function modifieValueWithProccess(ZActiveQuery $query, string $operator, string $attribute)
    {
        $value = '';
        switch ($operator) {
            case 'concat':
                $records = $query->all();
                $array = ZArrayHelper::map($records, 'id', function ($record) use ($attribute) {
                    Az::$app->forms->wiData->clean();
                    Az::$app->forms->wiData->model = $record;
                    Az::$app->forms->wiData->attribute = $attribute;
                    return Az::$app->forms->wiData->value();
                });

                $value = implode(', ', $array);
                break;
            default:
                $value = $query->$operator($attribute);
        }

        return (string)$value;
    }

    /**
     *
     * Function  getCellData
     * @param array $record
     * @param string $attribute
     * @param string $name
     * @param ZDynamicModel $model
     * @return  ZDynamicModel
     * @throws \Exception
     * @author Daho
     */

    private function getCellData(array $record, string $attribute, string $name, ZDynamicModel $model)
    {
        switch (true) {
            case $record === null:
                return $model;
                break;

            case ZStringHelper::endsWith($attribute, '_history'):
                $filter_value = ZArrayHelper::getValue($this->filter, $name);
                if ($filter_value === null)
                    return $model;

                if ($filter_value === Az::$app->cores->date->date()) {
                    $attr = strtr($attribute, [
                        '_history' => ''
                    ]);

                    $model->$name = ZArrayHelper::getValue($record, $attr);
                    return $model;
                }

                $val = $record[$attribute];
                if ($val !== null && !is_array($val))
                    $val = \json_decode($val, true, 512);
                $collection = collect($val);
                if ($collection->isEmpty())
                    return $model;
                $date = $collection->where('date', '<', $filter_value)->max('date');
                $val = $collection->where('date', $date);

                $model->$name = (string)ZArrayHelper::getValue($val->first(), 'name');
                break;

            case ZStringHelper::endsWith($attribute, '_id') :
                $model->$name = ZArrayHelper::getValue($record, $attribute);
                $fkTable = strtr($attribute, [
                    '_id' => ''
                ]);
                $fkClass = ZInflector::camelize($fkTable);
                $fkTableFull = $this->bootFull($fkClass);
                /** @var ZActiveRecord $rel_model */
                $rel_model = new $fkTableFull();
                Az::$app->forms->wiData->clean();
                Az::$app->forms->wiData->fkTable = $fkTable;
                Az::$app->forms->wiData->fkAttr = $rel_model->configs->name;

                $model->columns[$name]->data = Az::$app->forms->wiData->related();
                break;
            default:
                $model->$name = ZArrayHelper::getValue($record, $attribute);
        }
        return $model;
    }
    #endregion

    #region DepDrop
    /**
     *
     * Function  depAttribs
     * @param null $id
     * @param null $type
     * @param null $models
     * @return  array
     * @author Daho
     */
    public function depAttribs($id = null, $type = null, $models = null)
    {
        if (empty($type))
            return [];


        if (empty($id))
            return [];


        $chess = DynaChess::findOne($id);
        $dep_class = $this->bootFull($chess->models);

        if ($type === 'native') {
            return $this->getAttributesData($dep_class);
        }

        if ($models === null)
            return [];

        $data = $this->getAttributesData($this->bootFull($models));
        return $data;

    }

    /**
     *
     * Function  getAttributesData
     * @param $class
     * @return  array
     * @author Daho
     */
    private function getAttributesData($class)
    {

        /** @var ZActiveRecord $model */
        $model = new $class();
        $this->paramSet(paramMigration, true);
        $model->columns();
        $data = [];
        foreach ($model->columns as $attr => $column) {
            $data[$attr] = $column->title;
        }

        return $data;
    }

    /**
     *
     * Function  depModels
     * @param null $id
     * @param null $type
     * @return  array
     * @author Daho
     */
    public function depModels($id = null, $type = null)
    {
        $return = [];

        if (empty($id))
            return [];


        if (empty($type))
            return [];

        if ($type === 'native')
            return [];

        $chess = DynaChess::findOne($id);
        $dep_class = $this->bootFull($chess->models);
        $model = new $dep_class();

        foreach ($model->configs->$type as $className => $arr) {
            $hClass = $this->bootFull($className);
            $object = new $hClass();
            $return[$className] = $object->configs->title;
        }

        return $return;
    }


    /**
     *
     * Function  getRelateAttr
     * @param null $id
     * @param null $type
     * @param null $models
     * @return  array
     * @throws \Exception
     * @author Daho
     */
    public function getRelateAttr($id = null, $type = null, $models = null)
    {

        $return = [];

        if (empty($id))
            return [];

        if (empty($type))
            return [];

        if ($type === 'native')
            return [];

        if (empty($models))
            return [];


        $chess = DynaChess::findOne($id);

        $dep_class = $this->bootFull($chess->models);
        $rel_class = $this->bootFull($models);

        /** @var ZActiveRecord $model */
        $model = new $dep_class();

        $rel_model = new $rel_class();

        $attrs = ZArrayHelper::getValue($model->configs->$type, $models);


        if (empty($attrs))
            return [];


        foreach ($attrs as $attr => $key) {
            $column = ZArrayHelper::getValue($rel_model->columns, $key);
            $k = $key;

            switch ($type) {
                case 'hasOne':
                    $column = ZArrayHelper::getValue($model->columns, $attr);
                    $k = $attr;
                    break;
                case 'hasMany':
                    $column = ZArrayHelper::getValue($rel_model->columns, $attr);
                    $k = $attr;
                    break;
            }

            if ($column === null) {
                continue;
            }
            $return[$k] = $column->title;
        }
        return $return;
    }

    #endregion


}

