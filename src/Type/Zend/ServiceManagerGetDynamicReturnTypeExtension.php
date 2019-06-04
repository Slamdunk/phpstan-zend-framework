<?php

declare(strict_types=1);

namespace ZendPhpStan\Type\Zend;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Zend\ServiceManager\ServiceManager;

final class ServiceManagerGetDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    /**
     * @var ServiceManagerLoader
     */
    private $serviceManagerLoader;

    public function __construct(ServiceManagerLoader $serviceManagerLoader)
    {
        $this->serviceManagerLoader = $serviceManagerLoader;
    }

    public function getClass(): string
    {
        return ServiceManager::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return 'get' === $methodReflection->getName();
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        if (1 !== \count($methodCall->args)) {
            return new MixedType();
        }

        $argType = $scope->getType($methodCall->args[0]->value);
        if (! $argType instanceof ConstantStringType) {
            return new MixedType();
        }

        $objectName     = $argType->getValue();
        $serviceManager = $this->serviceManagerLoader->getServiceManager();
        if (! $serviceManager->has($objectName)) {
            return new NullType();
        }

        return new ObjectType(\get_class($serviceManager->get($objectName)));
    }
}
