<?php

declare(strict_types=1);

namespace ZendPhpStan\Rules\Zend;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\PluginManagerInterface;
use Zend\View\HelperPluginManager;
use ZendPhpStan\Type\Zend\ObjectServiceManagerType;
use ZendPhpStan\Type\Zend\ServiceManagerLoader;

final class ServiceManagerGetMethodCallRule implements Rule
{
    /**
     * @var ServiceManagerLoader
     */
    private $serviceManagerLoader;

    /**
     * @var array
     */
    private $knownUnmappedAliasToClassServices = [
        ControllerManager::class   => 'ControllerManager',
        HelperPluginManager::class => 'ViewHelperManager',
    ];

    public function __construct(ServiceManagerLoader $serviceManagerLoader)
    {
        $this->serviceManagerLoader = $serviceManagerLoader;
    }

    public function getNodeType(): string
    {
        return Node\Expr\MethodCall::class;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $node
     * @param Scope                           $scope
     *
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! isset($node->args[0])) {
            return [];
        }
        $argType = $scope->getType($node->args[0]->value);
        if (! $argType instanceof ConstantStringType) {
            return [];
        }

        $calledOnType = $scope->getType($node->var);
        if (! $calledOnType instanceof ObjectType) {
            return [];
        }

        $methodNameIdentifier = $node->name;
        if (! $methodNameIdentifier instanceof Node\Identifier) {
            return [];
        }

        $methodName = $methodNameIdentifier->toString();
        if ('get' !== $methodName) {
            return [];
        }

        $serviceManagerName = $calledOnType->getClassName();
        $serviceManager     = $this->serviceManagerLoader->getServiceManager();
        if (isset($this->knownUnmappedAliasToClassServices[$serviceManagerName])) {
            $serviceManager = $serviceManager->get($this->knownUnmappedAliasToClassServices[$serviceManagerName]);
        } elseif ($calledOnType->isInstanceOf(PluginManagerInterface::class)->yes()) {
            $serviceManager = $serviceManager->get($serviceManagerName);
        }

        $serviceName = $argType->getValue();
        if ($serviceManager->has($serviceName)) {
            return [];
        }

        return [\sprintf(
            'The service "%s" was not configured in %s.',
            $serviceName,
            $calledOnType instanceof ObjectServiceManagerType
                ? $calledOnType->getServiceName()
                : $serviceManagerName
        )];
    }
}
