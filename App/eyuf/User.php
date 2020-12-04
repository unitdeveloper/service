<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\App\eyuf;


use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\system\kernels\ZFrame;


class User extends ZFrame
{
    public function test()
    {
        echo 'test';
    }


    public function clean() {
        EyufScholar::deleteAll([
            'email' => ['asror.zk@gmail.com', 'asror.z@yandex.com']
        ]);
        \zetsoft\models\user\User::deleteAll([
            'email' => ['asror.zk@gmail.com', 'asror.z@yandex.com']
        ]);
    }

    public function docList()
    {
        /** @var EyufScholar $scholar */
/*        $scholar = EyufScholar::findOne([
            'core_user_id' => $this->userIdentity()->id
        ]);


        $docs = $scholar->getDocuments();

        $docTypes = DocumentType::findAll([
            'program' => $scholar->program
        ]);*/
/*
        foreach ($docs as $doc) {

        }*/

        $scholars = $this->getScholars();

        $return = [];

        foreach ($scholars as $scholar) {
            $return[] = [
                'name' => $scholar->program,
                'user' => $scholar->core_user_id,
            ];
        }

        return $return;
    }


    private function getScholars() {
        $allScholar = EyufScholar::find()->all();
        return $allScholar;
    }

    public function getAge($birthDate){
        if (empty($birthDate)) return null;
        $birthDate = explode("-", $birthDate);
        if (empty($birthDate[0])||empty($birthDate[1])||empty($birthDate[2])) return null;
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[1], $birthDate[0]))) > date("md")
            ? ((date("Y") - $birthDate[0])-1)
            : (date("Y") - $birthDate[0]));
        if ($age<0) $age = 0;
        return $age;
    }

    public function invoiceStatus($model = null){
          if (empty($model)) return true;
          foreach ($model->status as $value){
               if (!($value)) return true;
          }
          return false;
    }

    public function getMainUrl(){
        $user = $this->userIdentity();
        $role = $user->userRole();

        switch ($role) {
            case 'scholar':
                $url = $this->bootEnv('urlScholarIndex');
                return $url;
                break;

            case 'guest':
            case 'user':
                return "/cores/main/index";
                break;

            default;
                return "/logics/$role/index";
        }
    }
}


