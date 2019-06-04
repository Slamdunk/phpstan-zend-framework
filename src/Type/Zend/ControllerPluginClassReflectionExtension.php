<?php

declare(strict_types=1);

namespace ZendPhpStan\Type\Zend;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\ObjectType;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\PluginManager;

final class ControllerPluginClassReflectionExtension implements BrokerAwareExtension, MethodsClassReflectionExtension
{
    /**
     * @var ServiceManagerLoader
     */
    private $serviceManagerLoader;

    /**
     * @var Broker
     */
    private $broker;

    public function __construct(ServiceManagerLoader $serviceManagerLoader)
    {
        $this->serviceManagerLoader = $serviceManagerLoader;
    }

    public function setBroker(Broker $broker): void
    {
        $this->broker = $broker;
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->isSubclassOf(AbstractController::class) && $this->getControllerPluginManager()->has($methodName);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $plugin          = $this->getControllerPluginManager()->get($methodName);
        $pluginClassName = \get_class($plugin);
        $classReflection = $this->broker->getClass($pluginClassName);

        if (\is_callable($plugin)) {
            return $classReflection->getNativeMethod('__invoke');
        }

        $returnType = new ObjectType($pluginClassName);

        return new PluginMethodReflection(
            $classReflection,
            $methodName,
            $returnType
        );
    }

    private function getControllerPluginManager(): PluginManager
    {
        return $this->serviceManagerLoader->getServiceManager()->get('ControllerPluginManager');
    }
}
