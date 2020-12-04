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
use zetsoft\models\page\PageControl;
use zetsoft\models\page\PageModule;
use zetsoft\models\user\UserRbac;
use zetsoft\models\core\CoreRole;
use zetsoft\models\user\User;
use zetsoft\system\Az;
use zetsoft\system\except\ZDBException;
use zetsoft\system\except\ZException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;


class RbacApi extends ZFrame
{

    #region Vars

    public $allow = true;


    public $roles = [];


    /* @var UserRbac[] $rbacs */
    private $rbacs;

    #endregion

    #region Core


    public function run()
    {
        $userRole = $this->getRole();

        $value = ZVarDumper::search($userRole);

        $this->rbacs = UserRbac::find()
            ->where("roles @> $value")
            ->andWhere([
                'active' => true
            ])
            ->all();


        foreach ($this->rbacs as $rbac) {

            switch ($rbac->target) {

                case UserRbac::target['module']:

                    /** @var PageModule[] $data */
                    $data = $rbac->getPageModulesFromPageModuleIds();
                    if (!empty($data))
                        foreach ($data as $target) {
                            if ($target->name === $this->urlMain)
                                $this->allow = false;

                        }

                    break;


                case UserRbac::target['control']:
                    $data = $rbac->getCoreControlsFromCoreControlIds();

                    if (!empty($data))
                        foreach ($data as $target) {
                            if ($target->name === $this->controlName)
                                $this->allow = false;
                        }


                    break;

                case UserRbac::target[paramAction]:
                    $data = $rbac->getPageActionsFromPageActionIds();

                    if (!empty($data))
                        foreach ($data as $target) {
                            if ($target->name === $this->urlMain)
                                $this->allow = false;
                        }
                    break;

                case UserRbac::target['common']:
                    if (ZArrayHelper::isIn($this->urlMain, $rbac->common))
                        $this->allow = false;

                    break;

            }

        }


        return $this->allow;
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
