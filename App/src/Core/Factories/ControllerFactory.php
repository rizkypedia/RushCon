<?php


namespace Rushcon\Core\Factories;


use Rushcon\Core\BaseController;
use Rushcon\Core\Container;

class ControllerFactory
{
    public static function create(string $className, Container $container):BaseController
    {
        $class = DEFAULT_NAMESPACE . NAMESPACE_DELIMITER . CONTROLLER_SUFFIX . NAMESPACE_DELIMITER . $className . CONTROLLER_SUFFIX;
        return new $class(ContainerFactory::create($container));
    }
}
