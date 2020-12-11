<?php


namespace Rushcon\Core\Factories;


use Rushcon\Core\BaseController;

class ControllerFactory
{
    public static function create(string $className):BaseController
    {
        $class =  $className;
        return new $class(ContainerFactory::create());
    }
}
