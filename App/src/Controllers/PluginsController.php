<?php


namespace RushCon\Controllers;


use Rushcon\Core\BaseController;
use Rushcon\Core\Console;
use Rushcon\Core\Container;

class PluginsController extends BaseController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function listAction():void
    {
        Console::pprintln('Test');
    }
}
