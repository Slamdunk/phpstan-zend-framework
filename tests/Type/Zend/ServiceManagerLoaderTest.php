<?php

declare(strict_types=1);

namespace ZendPhpStan\Tests\Type\Zend;

use PHPStan\ShouldNotHappenException;
use PHPUnit\Framework\TestCase;
use Zend\Cache\PatternPluginManager as CachePatternPluginManager;
use Zend\Cache\Storage\AdapterPluginManager as CacheStorageAdapterPluginManager;
use Zend\Cache\Storage\PluginManager as CacheStoragePluginManager;
use Zend\Config\ReaderPluginManager as ConfigReaderPluginManager;
use Zend\Config\WriterPluginManager as ConfigWriterPluginManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Filter\FilterPluginManager;
use Zend\Form\FormElementManager;
use Zend\Hydrator\HydratorPluginManager;
use Zend\I18n\Translator\LoaderPluginManager as I18nLoaderPluginManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\Log\FilterPluginManager as LogFilterPluginManager;
use Zend\Log\FormatterPluginManager as LogFormatterPluginManager;
use Zend\Log\ProcessorPluginManager as LogProcessorPluginManager;
use Zend\Log\WriterPluginManager as LogWriterPluginManager;
use Zend\Mail\Protocol\SmtpPluginManager;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Controller\PluginManager as ControllerPluginManager;
use Zend\Paginator\AdapterPluginManager as PaginatorAdapterPluginManager;
use Zend\Paginator\ScrollingStylePluginManager;
use Zend\Router\RoutePluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\ValidatorPluginManager;
use Zend\View\Helper\Navigation\PluginManager as NavigationPluginManager;
use Zend\View\HelperPluginManager;
use ZendPhpStan\ServiceManagerLoader;
use ZendPhpStan\TestAsset\BarService;
use ZendPhpStan\TestAsset\FooService;

/**
 * @covers \ZendPhpStan\ServiceManagerLoader
 */
final class ServiceManagerLoaderTest extends TestCase
{
    public function testWithNullFileUseADefaultInstanceWithPluginManagerConfigured()
    {
        $serviceManagerLoader = new ServiceManagerLoader(null);

        $serviceManager = $serviceManagerLoader->getServiceLocator(ServiceManager::class);

        // @see \Zend\Mvc\Service\ServiceManagerConfig
        static::assertTrue($serviceManager->has(EventManagerInterface::class));
        static::assertTrue($serviceManager->has('ControllerPluginManager'));

        /** @var ControllerPluginManager $controllerPluginManager */
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');

        static::assertTrue($controllerPluginManager->has('redirect'));
    }

    public function testGetSubserviceDependingOnCallOnTypeGiven()
    {
        $serviceManagerLoader = new ServiceManagerLoader(null);
        $knownPluginManagers  = [
            CachePatternPluginManager::class,
            CacheStorageAdapterPluginManager::class,
            CacheStoragePluginManager::class,
            // ConfigReaderPluginManager::class,
            // ConfigWriterPluginManager::class,
            ControllerManager::class,
            ControllerPluginManager::class,
            FilterPluginManager::class,
            FormElementManager::class,
            HelperPluginManager::class,
            HydratorPluginManager::class,
            I18nLoaderPluginManager::class,
            InputFilterPluginManager::class,
            LogFilterPluginManager::class,
            LogFormatterPluginManager::class,
            LogProcessorPluginManager::class,
            LogWriterPluginManager::class,
            // NavigationPluginManager::class,
            PaginatorAdapterPluginManager::class,
            RoutePluginManager::class,
            ScrollingStylePluginManager::class,
            SmtpPluginManager::class,
            ValidatorPluginManager::class,
        ];

        foreach ($knownPluginManagers as $pluginManagerClassName) {
            static::assertInstanceOf($pluginManagerClassName, $serviceManagerLoader->getServiceLocator($pluginManagerClassName));
        }
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

        $serviceManager = $serviceManagerLoader->getServiceLocator(ServiceManager::class);

        static::assertTrue($serviceManager->has('foo'));
        static::assertFalse($serviceManager->has('bar'));

        static::assertInstanceOf(FooService::class, $serviceManager->get('foo'));

        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');

        static::assertFalse($controllerPluginManager->has('foo'));
        static::assertTrue($controllerPluginManager->has('bar'));

        static::assertInstanceOf(BarService::class, $controllerPluginManager->get('bar'));
    }
}
