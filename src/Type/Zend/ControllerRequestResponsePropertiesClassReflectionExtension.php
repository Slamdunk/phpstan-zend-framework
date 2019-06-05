<?php

declare(strict_types=1);

namespace ZendPhpStan\Type\Zend;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ObjectType;
use Zend\ServiceManager\ServiceLocatorInterface;

final class ControllerRequestResponsePropertiesClassReflectionExtension implements PropertiesClassReflectionExtension
{
    /**
     * @var ServiceManagerLoader
     */
    private $serviceManagerLoader;

    private $methodToServiceMap = [
        'request'   => 'Request',
        'response'  => 'Response',
    ];

    public function __construct(ServiceManagerLoader $serviceManagerLoader)
    {
        $this->serviceManagerLoader = $serviceManagerLoader;
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        return isset($this->methodToServiceMap[$propertyName]);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $serviceManager = $this->serviceManagerLoader->getServiceManager(ServiceLocatorInterface::class);
        $serviceName    = $this->methodToServiceMap[$propertyName];
        $serviceClass   = \get_class($serviceManager->get($serviceName));

        $propertyReflection = $classReflection->getNativeReflection()->getProperty($propertyName);

        return new PhpPropertyReflection(
            $classReflection,
            new ObjectType($serviceClass),
            $propertyReflection,
            null,
            false,
            false
        );
    }
}
