<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\utility;


use Closure;
use ReflectionFunction;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZFrame;

class Pregs extends ZFrame
{


    #region Reflection

    public function refClosure(Closure $c)
    {
        $reflect = new ReflectionFunction($c);
        return $this->refContent($reflect);
    }


    /**
     *
     * Function  content
     * @param \ReflectionClass $reflect
     * @return  string
     */
    public function refContent($reflect)
    {
        $lines = file($reflect->getFileName());
        $return = '';
        for ($l = $reflect->getStartLine() - 1; $l < $reflect->getEndLine(); $l++) {
            try {
                //$return .= $lines[$l];
                $return .= ZArrayHelper::getValue($lines, $l);

            } catch (\Exception $exception) {
                vdd($exception);
            }
        }

        return $return;
    }


    /**
     *
     * Function  uses
     * @param \ReflectionClass $reflect
     */
    public function refUses($class)
    {

        if (!class_exists($class)) {
            Az::error($class, 'Class does not exist');
            return null;
        }


        $reflect = new \ReflectionClass($class);
        $string = file_get_contents($reflect->getFileName());

        return $this->regUses($string);
    }


    public function refClass($class)
    {
        if (!class_exists($class))
            return null;

        $reflect = new \ReflectionClass($class);
        $methods = $reflect->getMethods();
        foreach ($methods as $method) {
            echo $method->getName();
        }

    }

    public function refMethod($class, $method)
    {
        if (!class_exists($class))
            return null;

        $reflect = new \ReflectionClass($class);
        $method = $reflect->getMethod($method);

        return $this->refContent($method);
    }

    public function refConstant($class, $name)
    {
        if (!class_exists($class))
            return null;

        $reflect = new \ReflectionClass($class);
        $const = $reflect->getConstant($name);


        return $const;
    }


    #endregion

    #region Relfection List

    public function refMethodList($class)
    {
        if (!class_exists($class))
            return null;

        $reflect = new \ReflectionClass($class);
        $methods = $reflect->getMethods();

        return $methods;
    }

    public function refMethodListTest($class)
    {
        $folder = ZArrayHelper::getValue($this->paramGet('smartFolder'), [0]);
        $list = null;
        $testList = null;
        foreach ($class as $item) {
            $className = 'zetsoft\service\\' . $folder . '\\';

            if (!empty($this->paramGet('smartClass'))) {
                if (ZArrayHelper::isIn($item, $this->paramGet('smartClass'))) {
                    $className .= $item;
                    $list = $this->refMethodList($className);
                    foreach ($list as $functions) {
                        if (ZStringHelper::endsWith($functions->name, 'Test', true)) {
                            $testList[$item][] = $functions;
                        }
                        if ($functions->name === 'test') {
                            unset($testList);
                            $testList[$item][] = $functions;
                            break 1;
                        }
                    }
                }
            } else {
                $className .= $item;
                $list = $this->refMethodList($className);
                foreach ($list as $functions) {
                    if ($functions->name === 'test') {
                        $testList[$item][] = $functions;
                        break 1;
                    }
                    if (ZStringHelper::endsWith($functions->name, 'Test', true) && !ZStringHelper::find($functions->name, 'Zframe')) {
                        $testList[$item][] = $functions;
                    }

                }
            }
        }
        return $testList;
    }


    public function refConstantList($class)
    {
        if (!class_exists($class))
            return null;

        $reflect = new \ReflectionClass($class);
        $const = $reflect->getConstants();

        return $const;
    }
    #endregion


    #region Multi


    public function multiIndexTest()
    {
        echo $this->multiIndex();
    }

    public function multiIndex($id = null)
    {
        if ($id === null)
            $id = 'placeadressthree-home-0-text';
            
        $index = $this->pregMatch($id, '\w*-\w*-(\d*)-\w*');
        return $index;

    }

    #endregion

    #region Native

    /**
     *
     * Function  pregMatch
     * @param $string
     * @param $match
     * @return  mixed
     *
     * $a = Az::$app->utility->pregs->pregMatch('[1]formsa', '\[.+\](.*)');
     */
    public function pregMatch($string, $match, $default = null)
    {
        preg_match("/$match/", $string, $returnALL);
        $return = ZArrayHelper::getValue($returnALL, 1);

        if ($return === null)
            $return = $default;

        return $return;
    }


    /**
     *
     * Function  pregMatchAll
     * @param $string
     * @param $match
     * @return  array
     *
     * $aB = Az::$app->utility->pregs->pregMatchAll('[1]formsa[1]formsa', '\[.+\](.*)');
     *
     */
    public function pregMatchAll($string, $match)
    {
        preg_match_all("/$match/", $string, $returnALL);
        return $returnALL;

    }

    /**
     *
     * Function  pregReplace
     * @param $string
     * @param $match
     * @param string $replace
     * @return  string|string[]|null
     *
     *  $string = 'CoreSetting[[1]value][usd]';
     * $match = '(\w*)\[\[(\d+)\](\w+)\]\[(\w+)\]';
     * $result = Az::$app->utility->pregs->pregReplace($string, $match, '$1[$2][$3][$4] ');
     *
     */
    public function pregReplace($string, $match, $replace = '')
    {
        return preg_replace("/$match/", $replace, $string);
    }
    #endregion

    #region Regular

    /**
     *
     * Function  regUses
     * @param $string
     * @return  array
     */
    public function regUses($string)
    {
        $usesPreg = $this->pregMatchAll($string, 'use (.*);\r\n');

        return ZArrayHelper::getValue($usesPreg, 1);
    }

    public function regProperty($string, $val = 1)
    {
        $propsPreg = $this->pregMatchAll($string, '@property (.*) \$(.*)');
        return ZArrayHelper::getValue($propsPreg, $val);
    }

    public function regAttributeAll($attribute)
    {
        return $this->pregMatch($attribute, '\[.+\](.*)');
    }

    #endregion

}
