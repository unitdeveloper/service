<?php

/**
 *
 * Author:  Asror Zakirov
 * Date:    16.05.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\service\cores;

use Google\Cloud\Translate\V2\TranslateClient;
use pdima88\uztranslit\UzLatToCyr;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use Yandex\Translate\Translation;
use Yandex\Translate\Translator;
use zetsoft\dbitem\data\FormDb;
use zetsoft\models\lang\Lang;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class Langs extends ZFrame
{
    #region Vars

    public $path;
    public $detect = false;
    public $source;
    public $client = self::clients['google'];
    public $testing;
    public $overwrite = false;

    // public const active = ['ru'];
    public $activeOnly = true;
    public $translate = [];
    public $active = [];

    public const clients = [
        'yandex' => 'yandex',
        'google' => 'google',
    ];

    public const lang = [
        'en' => 'English',
        'uz' => 'Uzbek',
        'ru' => 'Russian',
        'uzk' => 'Uzbek cryllic',
        'lv' => 'Latvian',
        'ro' => 'Romanian',

        'pt' => 'Portuguese',
        'ja' => 'Japanaese',
        'fa' => 'Persian',
        'ur' => 'Urdu',
        'uk' => 'Ukrainian',
        'vi' => 'Vietnamese',
        'jv' => 'Javanese',
        'th' => 'Thai',
        'pl' => 'Polish',
        'hi' => 'Hindi',
        'zh' => 'Chinese',
        'ar' => 'Arabic',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
        'es' => 'Spanish',
        'tr' => 'Turkish',
        'ko' => 'Korean',
    ];

    #endregion

    #region Private
    private $tried;

    private $message;
    /* @var TranslateClient $google */
    private $google;

    /* @var Translator $yandex */
    private $yandex;


    #endregion

    #region Settings

    public function init()
    {
        global $boot;
        parent::init();

//        $this->paramSet(paramNoEvent, true);

        $this->active = $boot->env('activelang');

        /**
         *
         * Remove Actual Value
         */
        $translate = $this->active;
        ZArrayHelper::removeValue($translate, $boot->env('language'));
        $this->translate = $translate;

        $this->testing = $boot->env('langTesting');

        $this->google = new TranslateClient(['key' => 'AIzaSyB_6AvGlk-Y8uSPWZ2gjI1Qt1t4wvTPSmQ']);

        $this->yandex = new Translator('trnsl.1.1.20191230T064628Z.c15a579eeda7f78b.ff5420ef0a8ec945b27c0a102bd1a78f9f62a1a4');

    }

    /**
     * function folders\
     * @author Daho\
     * @since 15.10.2020
     * @lines  15
     */
    public function folders()
    {
        if ($this->testing)
            $this->path = [
                '@zetsoft/widgets',
            ];
        else {
            $this->path = [
                '@zetsoft/models/ALL',
                '@zetsoft/dbdata/ALL',
                '@zetsoft/dbdata/' . App,
                '@zetsoft/dbitem/ALL',
                '@zetsoft/dbitem/' . App,
                '@zetsoft/former/ALL',
                '@zetsoft/former/' . App,
                '@zetsoft/models/' . App,
                '@zetsoft/service',
                '@zetsoft/system',
                '@zetsoft/widgets',
            ];

            $webhtms = Az::$app->cores->buildWeb->folderScan();
            foreach ($webhtms as $webhtm) {
                $folder = '@zetsoft/webhtm' . $webhtm;
                $this->path[] = $folder;
            }

            $webhrest = Az::$app->cores->buildApi->folderScan();
            foreach ($webhrest as $webrest) {
                $folder = '@zetsoft/webrest' . $webrest;
                $this->path[] = $folder;
            }

            $webhrest = Az::$app->smart->migra->category();
            foreach ($webhrest as $webrest) {
                $folder = '@zetsoft/models/' . $webrest;
                $this->path[] = $folder;
            }

        }
    }


    public function langs()
    {

        if (empty($this->source))
            $this->source = Az::$app->language;

        if ($this->activeOnly)
            $langs = $this->active;
        else
            $langs = array_keys(self::lang);

        $return = [];

        foreach ($langs as $lang) {
            if ($lang === $this->source)
                continue;

            $return[] = $lang;
        }

        return $return;

    }

    #endregion

    #region AZL


    public function hasTable()
    {
        return $this->dbHasTable('lang');
    }

    public function azl(string $string)
    {
        $currentLang = Az::$app->language;

        if ($this->message === null && !$this->tried) {
            $this->tried = true;

            $langs = Lang::find()
                ->asArray()
                ->all();

            if (!empty($langs))
                foreach ($langs as $lang)
                    foreach ($this->active as $item) {
                        //    if ($item === 'pt')
                        // vdd($allLang);
                        $this->message[$item][$lang['name']] = $lang[$item];
                    }
        }


        /**
         *
         * Get Value
         */
        if (empty($this->message))
            return $string;


        if (ZArrayHelper::keyExists($currentLang, $this->message)) {
            $data = $this->message[$currentLang];
            $value = ZArrayHelper::getValue($data, $string);

        }
        if (empty($value))
            return $string;


        return $value;
    }

#endregion

#region Core

    public function run()
    {
        $this->file();
        $this->model();
    }

    public function file()
    {
        $this->scan();
        $this->lang();
    }


#endregion

#region Scan

    public function scan()
    {
        //todo:start Daho
        $smartFiles = $this->paramGet('smartFile');
        if (is_array($smartFiles)){
            $this->checkFiles($smartFiles, true);
            return null;
        }
        $smartFolder = $this->paramGet('smartFolder');
        if (is_array($smartFolder))
            $this->path = $smartFolder;
        else
            $this->folders();

        Az::debug('Scanning folders. scan functions is running');
        foreach ($this->path as $path) {
            if (is_array($smartFolder))
                $path = '@zetsoft/' . $path;

            //todo:end 26lines
            $folder = Az::getAlias($path);
            if (!file_exists($folder)) {
                Az::error($folder, 'Folder not found!');
                continue;
            }

            $files = ZFileHelper::scanFiles($folder, true, [
                '*.php'
            ]);

            $this->checkFiles($files);

        }
    }

    /**
     * @param $files
     * @param bool $smartFile
     * @author Daho
     */
    private function checkFiles($files, $smartFile = false)
    {
        foreach ($files as $file) {

            $exclude = ZStringHelper::find($file, '@');

            if ($exclude) {
                Az::debug($file, 'Unrequired Folders are Detected!');
                continue;
            }
            if ($smartFile)
                $file = Root . '\\' . $file;

            if (file_exists($file))
                $this->scanFile($file);
            else
                Az::error('File not found: ' . $file);
        }
    }


    private function scanFile($file)
    {

        $fileName = strtr($file, [
            Az::getAlias('@zetsoft/') => '',
        ]);

        $fileName = ZFileHelper::normLinux($fileName);



        Az::debug($file, 'Scanning file:');

        $contents = file_get_contents($file);
        $list = [];
        $list[] = Az::$app->utility->pregs->pregMatchAll($contents, "Az::l\('(.*)'");
        $list[] = Az::$app->utility->pregs->pregMatchAll($contents, 'Az::l\("(.*)"');
        $list[] = Az::$app->utility->pregs->pregMatchAll($contents, "this->title\('(.*)'");
        $list[] = Az::$app->utility->pregs->pregMatchAll($contents, "Azl\ *?.\ *?\'(.*?)\'|Azl\ *?.\ *?\"(.*?)\"");
        $all = [];
        foreach ($list as $key => $item) {
            unset($list[$key][0]);
            foreach ($item as $key1 => $arr) {
                if ($key1 > 0)
                    foreach ($arr as $key2 => $i) {
                        $all[] = $i;
                    }
            }
        }

        Az::debug($all, 'Found Strings');

        if (!empty($all)) {
            foreach ($all as $key => $text) {
                Az::debug($text, "Creating model with value:");
                if (empty($text))
                    continue;

                $model = Lang::findOne([
                    'name' => $text
                ]);

                if ($model === null) {
                    Az::debug($text, 'Create New lang');
                    $model = new Lang();
                }

                $model->name = $text;
                $model->file = $fileName;
                $model->from = Az::$app->language;

                if ($model->save())
                    Az::debug("Saved {$text}");

            }
        }
    }


#endregion


#region Locales

    public function lang()
    {
        Az::start(__FUNCTION__);

        $all = Lang::find()
            ->all();

        foreach ($all as $model) {
            $this->langItem($model);
        }
        Az::end();
    }


    /**
     *
     * Function  langItem
     * @param Lang $model
     */
    private function langItem($model)
    {
        Az::start(__FUNCTION__);

        Az::trace('Start translate: ' . $model->name);

        foreach ($this->langs() as $lang) {

            if (empty($model->$lang) || $this->overwrite) {
                if ($lang === 'uzk')
                    $trans = $this->cyrill($model->uz);
                else
                    $trans = $this->provider($model->name, $lang);

                $model->$lang = $this->clean($trans);
            }

        }

        if ($model->save()) {
            Az::debug("Saved {$model->name}");
        }
    }


#endregions

#region Model


    public function model()
    {
        Az::start(__FUNCTION__);
        $classes = Az::$app->smart->migra->scan();

        foreach ($classes as $class) {
            Az::debug('Starting translating class: ' . $class);
            /** @var Models $model */
            $model = new $class();
            if (!$model->dbHasTable())
            {
                Az::debug($class, 'Table Not Exists');
                continue;
            }
            
            $model->columns();
            $this->source = $model->configs->lang;
            $columnList = $model->columnsList();
            foreach ($model->columns as $key => $column) {
                Az::debug('Checking attribute is ' . $key . ' of ' . $class);
                if (ZStringHelper::endsWith($key, '_lang'))
                    continue;


                if (!$column->lang)
                    continue;

                $attribute_lang = $key . '_lang';
                // vdd(ZArrayHelper::keyExists($attribute_lang, $model->columns));
                if (!ZArrayHelper::keyExists($attribute_lang, $model->columns))
                    continue;

                Az::debug("Translating {$key} column of {$model::classname()} model");


                $this->column($model, $attribute_lang, $key);
            }

            /* foreach ($columnList as $attribute) {
                 if ($attribute === 'id')
                     continue;

                 if ($attribute === 'code')
                     continue;

                 if (ZStringHelper::endsWith($attribute, '_lang'))
                     continue;


                 if (!$model->$attribute->lang)
                     continue;

                 $attribute_lang = $attribute  . '_lang';

                 if (!ZArrayHelper::isIn($attribute_lang, $columnList))
                     continue;



                     Az::debug("Translating {$attribute} column of {$model::classname()} model");

                     $this->column($model, $attribute_lang, $attribute);

             }*/
        }

        Az::end();

        return true;
    }


    private function column($modelClass, $langColumn, $textColumn)
    {

        Az::start(__FUNCTION__);

        /** @var Models[] $models */
        $models = $modelClass::find()->all();
        $className = $modelClass::className();

        if (empty($models))
            return Az::warning('Not found data! Class: ' . $className);


        foreach ($models as $model) {
            $model->columns();
            if (!isset($model->$langColumn)) {
                continue;
            }
            if (!is_array($model->$langColumn)) $model->$langColumn = null;

            $trans = $model->$langColumn;
            $texts = $model->$textColumn;


            if (empty($texts))
                Az::debug("Not found data! Name: {$textColumn} | Column:  {$modelClass} | ID: {$model->id}");

            Az::debug($texts, "Translating in:  $className  - $textColumn");

            $this->source = $model->configs->lang;


            foreach ($this->langs() as $lang) {


                if (empty($model->$langColumn[$lang]) || $this->overwrite) {

                    if ($lang === 'uzk')
                        $trans[$lang] = $this->cyrill($trans['uz']);
                    else
                        $trans[$lang] = $this->provider($texts, $lang, $model->configs->lang);

                    $trans[$lang] = $this->clean($trans[$lang]);
                }
            }
            $model->$langColumn = $trans;

            if ($model->save()) {

            };


        }


    }

    public function langColumns()
    {
        $cols = [];
        foreach ($this->active as $lang) {
            $title = \Locale::getDisplayLanguage($lang) . ' - ' . \Locale::getDisplayLanguage($lang, $lang);
            if ($lang === 'uzk')
                $title = \Locale::getDisplayLanguage('uz') . ' - Ўзбек';

            $column = function (FormDb $column) use ($title) {

                $column->title = $title;
                $column->dbType = dbTypeText;
                $column->pageSummary = true;

                return $column;
            };

            $cols[$lang] = $column;
        }

        return $cols;
    }

#endregions

#region Provider


    private function provider($string, $lang, $source = null)
    {

        if ($source === null)
            $source = Az::$app->language;


        $googleOptions = [];
        if (!$this->detect)
            $googleOptions = [
                'target' => $lang,
                'source' => $source
            ];
        else
            $googleOptions = [
                'target' => $lang
            ];

        switch ($this->client) {

            case 'google':
                Az::debug("Google | {$string}  -  {$lang}");
                $result = $this->google->translate($string, $googleOptions);
                return $result['text'];
                break;

            case 'yandex':
                /** @var Translation $translation */
                $yandexLang = $lang;
                Az::debug("Google | {$string}  -  {$lang}");
                if (!$this->detect)
                    $yandexLang = Az::$app->language . '-' . $yandexLang;
                $translation = $this->yandex->translate($string, $yandexLang);
                return $translation->getResult()[0];

                break;

            default:
                return $string;
        }
    }


#endregion

#region Uzbek-krill

    private
    function cyrill($text)
    {
        return UzLatToCyr::translit($text);
    }

    private
    function clean($text)
    {
        return strtr($text, [
            '&#39;' => "'",
            '&quot;' => '"'
        ]);
    }

#endregion


}

