<?php

namespace ZendPhpStan\Tests\Type\Zend;

use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Service\ControllerPluginManagerFactory;
use ZendPhpStan\Type\Zend\ServiceManagerLoader;
use PHPStan\ShouldNotHappenException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ZendPhpStan\Type\Zend\ServiceManagerLoader
 */
final class ServiceManagerLoaderTest extends TestCase
{
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
        $serviceManagerFromFile = require __DIR__ . '/data/servicemanagerloader.php';
        $serviceManagerLoader = new ServiceManagerLoader(__DIR__ . '/data/servicemanagerloader.php');

        static::assertInstanceOf(get_class($serviceManagerFromFile), $serviceManagerLoader->getServiceManager());
    }

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
}