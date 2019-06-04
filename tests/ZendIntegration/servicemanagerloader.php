<?php

declare(strict_types=1);

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use ZendPhpStan\TestAsset\BarService;
use ZendPhpStan\TestAsset\FooService;

$serviceManagerConfig = new ServiceManagerConfig();
$serviceManager       = new ServiceManager();
$serviceManagerConfig->configureServiceManager($serviceManager);
$serviceManager->setService('ApplicationConfig', [
    'modules' => [
        'zendphpstan' => new class() implements ConfigProviderInterface {
            /**
             * @return array|\Traversable
             */
            public function getConfig()
            {
                return [
                    'service_manager' => [
                        'invokables' => [
                            'foo' => FooService::class,
                        ],
                    ],
                    'controller_plugins' => [
                        'invokables' => [
                            'bar' => BarService::class,
                        ],
                    ],
                ];
            }
        },
    ],
    'module_listener_options' => [],
]);
$serviceManager->get(ModuleManager::class)->loadModules();

return $serviceManager;
