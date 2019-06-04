<?php

declare(strict_types=1);

namespace ZendPhpStan\Type\Zend;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

final class ServiceManagerLoader
{
    /**
     * @var null|ServiceManager
     */
    private $serviceManager;

    public function __construct(?string $serviceManagerLoader)
    {
        if (null === $serviceManagerLoader) {
            return;
        }

        if (! \file_exists($serviceManagerLoader) || ! \is_readable($serviceManagerLoader)) {
            throw new \PHPStan\ShouldNotHappenException('Service manager could not be loaded');
        }

        $serviceManager = require $serviceManagerLoader;
        if (! $serviceManager instanceof ServiceManager) {
            throw new \PHPStan\ShouldNotHappenException(\sprintf('Loader "%s" doesn\'t return a ServiceManager instance', $serviceManagerLoader));
        }

        $this->serviceManager = $serviceManager;
    }

    public function getServiceManager(): ServiceManager
    {
        if (null === $this->serviceManager) {
            $serviceManagerConfig = new ServiceManagerConfig();
            $serviceManager       = new ServiceManager();
            $serviceManagerConfig->configureServiceManager($serviceManager);
            $serviceManager->setService('ApplicationConfig', [
                'modules'                 => [],
                'module_listener_options' => [],
            ]);
            $serviceManager->get(ModuleManager::class)->loadModules();

            $this->serviceManager = $serviceManager;
        }

        return $this->serviceManager;
    }
}
