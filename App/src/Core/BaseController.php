<?php

namespace Rushcon\Core;


class BaseController
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}
