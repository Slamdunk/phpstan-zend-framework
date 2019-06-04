<?php

namespace ZendPhpStan\Tests\Type\Zend;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Service\ControllerPluginManagerFactory;
use ZendPhpStan\TestAsset\BarService;
use ZendPhpStan\TestAsset\FooService;
use ZendPhpStan\Type\Zend\ServiceManagerLoader;
use PHPStan\ShouldNotHappenException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ZendPhpStan\Type\Zend\ServiceManagerLoader
 */
final class ServiceManagerLoaderTest extends TestCase
{
    public function testWitNullFileUseADefaultInstanceWithPluginManagerConfigured()
    {
        $serviceManagerLoader = new ServiceManagerLoader(null);

        $serviceManager = $serviceManagerLoader->getServiceManager();

        // @see \Zend\Mvc\Service\ServiceManagerConfig
        $this->assertTrue($serviceManager->has(EventManagerInterface::class));
        $this->assertTrue($serviceManager->has('ControllerPluginManager'));

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');

        $this->assertTrue($controllerPluginManager->has('redirect'));
    }

    public function testLoaderMustBeAValidFile()
    {
        $this->expectException(ShouldNotHappenException::class);

        new ServiceManagerLoader(uniqid(__DIR__ . '/woot'));
    }

    public function testLoaderMustReturnAServiceManagerInstance()
    {
        $this->expectException(ShouldNotHappenException::class);

        new ServiceManagerLoader(__DIR__ . '/data/nothingloader.php');
    }

    public function testLoaderReturnsTheProvidedServiceManager()
    {
        $file = dirname(__DIR__, 2) . '/ZendIntegration/servicemanagerloader.php';
        $serviceManagerFromFile = require $file;
        $serviceManagerLoader = new ServiceManagerLoader($file);

        $serviceManager = $serviceManagerLoader->getServiceManager();

        static::assertTrue($serviceManager->has('foo'));
        static::assertFalse($serviceManager->has('bar'));

        static::isInstanceOf(FooService::class, $serviceManager->get('foo'));

        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');

        static::assertFalse($controllerPluginManager->has('foo'));
        static::assertTrue($controllerPluginManager->has('bar'));

        static::isInstanceOf(BarService::class, $controllerPluginManager->get('bar'));
    }
}