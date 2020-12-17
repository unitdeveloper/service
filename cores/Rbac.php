<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11/1/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\cores;

use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageApi;
use zetsoft\models\page\PageApiType;
use zetsoft\models\page\PageControl;
use zetsoft\models\page\PageModule;
use zetsoft\models\page\PageView;
use zetsoft\models\page\PageViewType;
use zetsoft\models\user\UserRbac;
use zetsoft\models\core\CoreRole;
use zetsoft\models\user\User;
use zetsoft\models\user\UserRbacApi;
use zetsoft\models\user\UserRbacCrud;
use zetsoft\models\user\UserRbacRest;
use zetsoft\models\user\UserRbacView;
use zetsoft\system\Az;
use zetsoft\system\except\ZDBException;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;


class Rbac extends ZFrame
{

    #region Vars

    public bool $allow = false;

    public const target = [
        'read' => 'read',
        'crud' => 'crud',
        'view' => 'view',
        'rest' => 'rest',
        'api' => 'api'
    ];


    public const gods = [
        'admin',
        'dev',
    ];

    public $target = self::target['view'];

    public string $authType = self::authType['none'];

    public const authType = [
        'none' => 'none',
        'param' => 'param',
        'bearer' => 'bearer',
        'basic' => 'basic',
        'basicParam' => 'basicParam',
    ];

    public array $roles = [];

    private $rbacs;

    public array $exclude = [
        '/shop/user',
        '/shop/core',
        '/core',
        '/api/core'
    ];
    #endregion

    #region Core


    /**
     *
     * Function  run
     * @param string $target
     * @return  bool
     * @throws \Exception
     */
    public function run()
    {
        global $boot;
        $this->allow = false;

        if (!$boot->env('rbac'))
            return true;

        if ($this->hasRoles(self::gods))
            return true;

        if (\in_array($this->userRole(), $this->urlArray, true))
            if ($this->urlData(1) !== 'user')
                return true;

        if (\in_array('core', $this->urlArray, true))
            return true;

        if (\in_array('auth', $this->urlArray, true))
            return true;

        if (\in_array('cores', $this->urlArray, true))
            return true;

        $ident = $this->userIdentity();
        if ($ident === null) {
            return null;
        }

        foreach ($this->exclude as $link) {
            if (ZStringHelper::startsWith($this->modifyUrl(), $link)) {
                return true;
            }
            if ($this->modifyUrl() === '/')
                return true;
        }
        $userRole = $this->getRole();


        $value = ZVarDumper::search($userRole);
        switch ($this->target) {
            case self::target['view']:
                $this->rbacView($value);
                break;

            case self::target['api']:
                $this->rbacApi($value);
                break;

            case self::target['rest']:
                $this->rbacRest($value);
                break;

            case self::target['crud']:
                $this->allow = true;
                $this->rbacCrud($value);
                break;
        }
        return $this->allow;

    }

    public function rbacRest($value)
    {
        $model = $this->paramGet('jsonModel');

        $this->rbacs = UserRbacRest::find()
            ->where("roles @> $value")
            ->andWhere([
                'active' => true
            ])
            ->all();

        foreach ($this->rbacs as $rbac) {
            if (ZArrayHelper::isIn($model, $rbac->models)) {
                $this->allow = true;
            }
        }
    }

    public function rbacApi($value)
    {
        $url = $this->modifyUrl(0);

        $this->rbacs = UserRbacApi::find()
            ->where("roles @> $value")
            ->andWhere([
                'active' => true
            ])
            ->all();
        foreach ($this->rbacs as $rbac) {
            if (!$this->emptyOrNullable($rbac->page_api_ids)) {
                $pageView = PageApi::findAll(['id' => $rbac->page_api_ids]);
                foreach ($pageView as $page) {
                    if ($url === $page->name) {
                        $this->allow = true;
                    }
                }
            }
        }
    }


    public function rbacView($value)
    {
        $this->rbacs = UserRbacView::find()
            ->where("roles @> $value")
            ->andWhere([
                'active' => true
            ])
            ->all();

        foreach ($this->rbacs as $rbac) {

            if (!$this->emptyOrNullable($rbac->page_view_type_ids)) {
                $pageView = PageViewType::findAll(['id' => $rbac->page_view_type_ids]);
                foreach ($pageView as $page) {
                    if (ZStringHelper::startsWith($this->modifyUrl(), $page->name)) {
                        $this->allow = true;
                    }
                }
            } else if (!$this->emptyOrNullable($rbac->page_view_ids)) {
                $pageView = PageView::findAll(['id' => $rbac->page_view_ids]);
                foreach ($pageView as $page) {
                    if ($this->modifyUrl() === $page->name) {
                        $this->allow = true;
                    }
                }
            }
        }
    }
    #endregion

    #region Crud


    public function rbacCrud($value)
    {

        if ($this->hasRole('user'))
        {
            $this->allow = false;
            return false;
        }

        $this->rbacs = UserRbacCrud::find()
            ->where("roles @> $value")
            ->andWhere([
                'active' => true
            ])
            ->all();


        foreach ($this->rbacs as $rbac) {

            /** @var UserRbacCrud $rbac */
            /*["DynaChessItem", "DynaChessQuery", "DynaDynagrid"]*/

            if (!$this->emptyOrNullable($rbac->models)) {
                $modelClass = $this->bootFullUrl();
                $modelClass = basename($modelClass);

                if (in_array($modelClass, $rbac->models))
                    $this->allow = true;
            }
        }
    }



    #endregion


    #region App


    #endregion


    #region Can


    public const isMainActive = false;
    public const isALLActive = false;


    public function checkRole($roleName)
    {
        $return = false;

        if ($this->checkAll())
            $return = true;

        $role = $this->getRole();

        if ($roleName === $role)
            $return = true;


        return $return;

    }


    public function checkRoles($roles)
    {

        if (empty($roles))
            return false;

        $return = false;

        if ($this->checkAll())
            $return = true;

        $userRole = $this->getRole();
        foreach ($roles as $role)
            if ($userRole === $role)
                $return = true;

        return $return;

    }

    private function checkAll()
    {
        global $boot;

        if (self::isALLActive)
            return true;

        if (self::isMainActive)
            if ($boot->userMain())
                return true;

        if (!$this->isCLI())
            if ($this->userIsGuest())
                return false;

        return false;
    }


    #endregion


    #region Role

    public function getRole()
    {

        global $boot;

        if ($boot->isCLI())
            return $boot->env('userRole');

        if ($this->userIsGuest())
            return 'user';

        $zoft = $this->thisGet();

        if ($zoft instanceof User)
            $identity = $zoft;
        else
            $identity = $this->userIdentity();

        if (empty($identity->role))
            return 'user';

        return $identity->role;
    }


    #endregion


}
