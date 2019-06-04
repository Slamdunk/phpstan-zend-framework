<?php

declare(strict_types=1);

namespace ZendPhpStan\Tests\Type\Zend;

use PHPStan\ShouldNotHappenException;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;
use ZendPhpStan\TestAsset\BarService;
use ZendPhpStan\TestAsset\FooService;
use ZendPhpStan\Type\Zend\ServiceManagerLoader;

/**
 * @covers \ZendPhpStan\Type\Zend\ServiceManagerLoader
 */
final class ServiceManagerLoaderTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testWithNullFileUseADefaultInstanceWithPluginManagerConfigured()
    {
        $serviceManagerLoader = new ServiceManagerLoader(null);

        $serviceManager = $serviceManagerLoader->getServiceManager(ServiceManager::class, false);

        // @see \Zend\Mvc\Service\ServiceManagerConfig
        static::assertTrue($serviceManager->has(EventManagerInterface::class));
        static::assertTrue($serviceManager->has('ControllerPluginManager'));

        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');

        static::assertTrue($controllerPluginManager->has('redirect'));
    }

    public function testGetSubserviceDependingOnCallOnTypeGiven()
    {
        $serviceManagerLoader = new ServiceManagerLoader(null);

        static::assertInstanceOf(HelperPluginManager::class, $serviceManagerLoader->getServiceManager(HelperPluginManager::class, true));
        static::assertInstanceOf(PluginManager::class, $serviceManagerLoader->getServiceManager(PluginManager::class, true));
    }

    public function testLoaderMustBeAValidFile()
    {
        $this->expectException(ShouldNotHappenException::class);

        new ServiceManagerLoader(\uniqid(__DIR__ . '/woot'));
    }

    public function testLoaderMustReturnAServiceManagerInstance()
    {
        $this->expectException(ShouldNotHappenException::class);

        new ServiceManagerLoader(__DIR__ . '/data/nothingloader.php');
    }

    public function testLoaderReturnsTheProvidedServiceManager()
    {
        $file                   = \dirname(__DIR__, 2) . '/ZendIntegration/servicemanagerloader.php';
        $serviceManagerFromFile = require $file;
        $serviceManagerLoader   = new ServiceManagerLoader($file);

        $serviceManager = $serviceManagerLoader->getServiceManager(ServiceManager::class, false);

        static::assertTrue($serviceManager->has('foo'));
        static::assertFalse($serviceManager->has('bar'));

        static::assertInstanceOf(FooService::class, $serviceManager->get('foo'));

        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');

        static::assertFalse($controllerPluginManager->has('foo'));
        static::assertTrue($controllerPluginManager->has('bar'));

        static::assertInstanceOf(BarService::class, $controllerPluginManager->get('bar'));
    }
}
