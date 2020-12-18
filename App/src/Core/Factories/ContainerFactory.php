<?php


namespace Rushcon\Core\Factories;

use Rushcon\Core\Container;

class ContainerFactory
{
    public static function create(): Container
    {
        return new Container();
    }
}
