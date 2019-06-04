<?php

declare(strict_types=1);

namespace ZendPhpStan\Tests\ZendIntegration\data;

use Zend\ServiceManager\ServiceManager;

final class controllerMethod
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function getDynamicType(): void
    {
        $controllerManager = $this->serviceManager->get('ControllerManager');
        $controllerManager->get('xyz');
        $controllerManager->get('foobar');
    }
}
