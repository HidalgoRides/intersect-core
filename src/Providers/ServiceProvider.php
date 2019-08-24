<?php

namespace Intersect\Core\Providers;

use Intersect\Core\Container;

abstract class ServiceProvider {

    /** @var Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function init() {}
    public function initCommands() {}

}