<?php

declare(strict_types=1);

namespace ZendPhpStan\Tests\ZendIntegration\data;

use Zend\ServiceManager\ServiceManager;

final class routePluginMethod
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
        $viewHelperManager = $this->serviceManager->get('RoutePluginManager');
        $viewHelperManager->get('route66');
        $viewHelperManager->get('foobar');
    }
}
