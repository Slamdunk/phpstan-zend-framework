<?php

declare(strict_types=1);

namespace ZendPhpStan\Tests\ZendIntegration\data;

use Zend\ServiceManager\ServiceManager;

final class viewHelperPluginMethod
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
        $viewHelperManager = $this->serviceManager->get('ViewHelperManager');
        $viewHelperManager->get('css');
        $viewHelperManager->get('foobar');
    }
}
