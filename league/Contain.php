<?php

/**
 * class Contain
 * @package zetsoft/service/league
 * @author UzakbaevAxmet
 * class bog'liqlikni echib, zarur bo'lgan joyga konteynerni kiritishingiz mumkin.
 */

namespace zetsoft\service\league;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use League\Container\Container;
use League\Container\Definition\DefinitionInterface;
use League\Container\Exception\{ContainerException, NotFoundException};
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\{ReflectionContainer};
use Acme\{Foo, FooInterface};
use Acme;



class Contain extends ZFrame
{

    #region Test

    public function test()
    {
      //  $this->containerTest();
        $this->interfaceTest();

    }

    #endregion
    public function containerTest()
    {

        $alias1='alias_for_foo';
        $alias2='alias_for_bar';
        $class1=Acme\Bar::class;
        $class2=Foo::class;
        $container=$this->container($alias1,$alias2,$class1,$class2);
        vd($container instanceof Acme\Bar);
    }

    public function container($alias1,$alias2,$class1,$class2)
    {
        require 'example.php';

        $container = new Container;
        $container->add($alias1, $class2)->addArgument($alias2);
        $container->add($alias2, $class1);
        $foo1 = $container->get($alias1);
        $foo2 = $container->get( $alias1);
        return $foo1;

    }
    public function interfaceTest() {
        require 'interface.php';
        $value='Acme\Service\SomeService';
        $gettest=$this->interface($value);
      vd($gettest);
    }
    public function interface($service)
    {
        $container = new Container;

// register the service as shared against the fully qualified classname
        $container->share($service);

// you retrieve the service in exactly the same way, however, each time you
// call `get` you will retrieve the same instance
        $service1 = $container->get($service);
        $service2 = $container->get($service);
        return $service1;

    }


}
