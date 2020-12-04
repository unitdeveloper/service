<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cores;


use Opis\Closure\SerializableClosure;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use zetsoft\dbitem\core\SessionItem;
use zetsoft\dbitem\data\ALLApp;
use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\models\core\CoreData;
use zetsoft\models\core\CoreSession;
use zetsoft\models\core\CoreSessionUser;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKDatepickerWidget;
use zetsoft\widgets\inputes\ZPeriodPickerWidget;
use function Dash\toArray;


/**
 * Class    Session
 * @package zetsoft\service\cores
 *
 * @property $cookie
 */
class Session extends ZFrame
{
    #region Var


    #endregion

    public function test()
    {
        $this->setObjectTest();
    }

    public function set($key, $value, $time = Auth::duration, $user_id = null)
    {

        $user_id = $user_id ?? $this->userIdentity()->id;
        $exist = $this->exists($key);
        $value = $this->setValue($value);

        if (!$exist) {

            $coreSession = new CoreSession();
            $coreSession->name = $key;
            $coreSession->session = $this->getCookieSession();
            $coreSession->value = $value;
            $coreSession->user_id = $user_id;

            $date = date('Y-m-d H:i:s');
            $currentDate = strtotime($date);
            $futureDate = $currentDate + ($time);
            $formatDate = date('Y-m-d H:i:s', $futureDate);
            $coreSession->expire = $formatDate;

            if ($coreSession->save()) {
                return true;
            }

            return false;
        }

        $model = $this->getSession($key);
        if ($model !== null) {
            $model->value = $value;
            if ($model->save()) {
                return true;
            }
            return false;
        }
    }

    public function get($key)
    {
        if ($key !== 'login') {
            return $this->cache($this->getCookieSession() . $key, Cache::type['array'], function () use ($key) {
                $value = $this->getSession($key);
                if ($value === null)
                    return 0;

                return $this->getValue($value->value);

            });
        }
        $value = $this->getSession($key);

        if ($value === null)
            return 0;

        return $this->getValue($value->value);
    }

    public function getAll($key)
    {
        $model = CoreSession::find()
            ->where([
                'name' => $key,
                'session' => $this->getCookieSession()
            ])
            ->one();

        if ($model)
            return $model;

        return false;
    }

    public function delete($key)
    {
        $query = $this->getQuery($key);

        /** @var Models $model */
        $model = $query->one();
        if ($model !== null) {
            return $model->delete();
        } else
            return false;
    }


    public function setObjectTest()
    {
        $item = new SessionItem();
        $item->class = ALLApp::class;


        $app = new ALLApp();

        $config = new Config();
        $app->configs = $config;

        $column = new Form();
        $column->title = Az::l('Значение 1');
        $column->widget = ZKDatepickerWidget::class;
        $column->rules = [
            [
                'zetsoft\\system\\validate\\ZRequiredValidator',
            ],
        ];

        $columns['value1'] = $column;

        $column = new Form();
        $column->title = Az::l('Значение 2');
        $column->widget = ZKDatepickerWidget::class;
        $column->rules = [
            [
                'zetsoft\\system\\validate\\ZRequiredValidator',
            ],
        ];

        $columns['value2'] = $column;

        $app->columns = $columns;
        $item->data = $app;

        $this->setObject('formRav', $item);

        $formRav = $this->getObject('formRav');
        vdd($formRav);

    }


    public function setObject($key, SessionItem $item, $time = 60 * 60)
    {
        $exist = $this->exists($key);
        $value = $this->setValue($item);
        if (!$exist) {
            $coreSession = new CoreSession();
            $coreSession->name = $key;
            $coreSession->session = $this->getCookieSession();
            $coreSession->value = $value;
            $coreSession->user_id = $this->userIdentity()->id;
            $coreSession->expire = date('Y-m-d H:i:s', time() + $time);
            if ($coreSession->save()) {
                return true;
            }
            return false;
        }
        $model = $this->getSession($key);
        if ($model !== null) {
            $model->value = $value;
            if ($model->save()) {
                return true;
            }
            return false;
        }
    }

    public function getObject($key)
    {
        $value = $this->getSession($key);
        if ($value === null)
            return false;
        $var = $value->value;
        $var = $this->getValue($var);
        if (ZArrayHelper::getValue($var, 'array'))
            $data = Az::$app->utility->mains->data2object(ZArrayHelper::getValue($var, 'class'), ZArrayHelper::getValue($var, 'data'));
        else
            $data = Az::$app->utility->mains->one2object($var['class'], $var['data']);
        return $data;
    }

    public function setCookieSession()
    {
        if (!$this->getCookieSession()) {
            $rand = random_int(1, 9999999);
            $this->cookieSet('session', Az::$app->cores->auth->hashGet($rand));
        }
    }

    public function getCookieSession()
    {
        if ($this->moduleId === 'api') {
            $headers = Az::$app->request->headers;
            if ($headers->has('ZCookie')) {
                $cookie = $headers->get('ZCookie');
                return $cookie;
            }
        }
        if ($this->isCLI()) {
            return "Cli";
        } else
            return $this->cookieGet('session');
    }

    public function clearSession()
    {
        $models = CoreSession::find()->all();
        if ($models) {
            foreach ($models as $model) {
                if (strtotime($model->expire) < time()) {
                    $model->delete();
                    echo 'Model deleted';
                }
            }
        }
    }

    private function setValue($data)
    {
        return ZJsonHelper::encode($data);
    }

    private function getValue($data)
    {
        return ZJsonHelper::decode($data);
    }

    private function getSession($key)
    {
        $query = $this->getQuery($key);

        return $query->one();
    }

    private function exists($key)
    {
        $query = $this->getQuery($key);
        return $query->exists();

    }

    private function getQuery($key)
    {
        $session = $this->getCookieSession();
      
          
        $query = CoreSession::find()
            ->where([
                'session' => $session,
                'name' => $key
            ]);

        return $query;
    }

#region NotNessesary

    private function splitValue($value)
    {
        $type = gettype($value);
        switch ($type) {
            case 'integer':
            case 'boolean':
            case 'string':
                return [
                    'value' => $value,
                    'type' => $type
                ];
                break;
            case 'array':
                $value = $this->jscode($value);
                return [
                    'value' => $value,
                    'type' => $type
                ];
                break;
            case 'object':
                $encoders = [new JsonEncoder()];
                $normalizers = [new ObjectNormalizer()];
                $serializer = new Serializer($normalizers, $encoders);
                $jsonContent = $serializer->serialize($value, 'json');
                return [
                    'value' => $jsonContent,
                    'type' => $value
                ];
                break;
            default:
                return false;
                break;
        }
    }

    private function converToType($value, $type)
    {
        switch ($type) {
            case 'integer':
            case 'boolean':
            case 'string':
                return $value;
                break;
            case 'array':
                return \yii\helpers\Json::decode($value);
                break;
            case 'object':
                $encoders = [new JsonEncoder()];
                $normalizers = [new ObjectNormalizer()];
                $serializer = new Serializer($normalizers, $encoders);
                $value = $serializer->deserialize($value, $type, 'json');

                return $value;
                break;
            default:
                return false;
                break;
        }
    }

#endregion

}
