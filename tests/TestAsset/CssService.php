<?php

namespace ZendPhpStan\TestAsset;

use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

final class CssService implements HelperInterface
{
    public function isFoo(): bool
    {
        return true;
    }

    /**
     * Set the View object
     *
     * @param Renderer $view
     * @return HelperInterface
     */
    public function setView(Renderer $view)
    {
        // TODO: Implement setView() method.
    }

    /**
     * Get the View object
     *
     * @return Renderer
     */
    public function getView()
    {
        // TODO: Implement getView() method.
    }
}