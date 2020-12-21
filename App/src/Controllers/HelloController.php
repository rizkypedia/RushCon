<?php

namespace Rushcon\Controllers;


use Rushcon\Core\BaseController;
use Rushcon\Core\Console;
use Rushcon\Core\Container;
use Rushcon\Core\Services\LoggerService;

class HelloController extends BaseController
{

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function indexAction()
    {
        Console::pprintln('Hello World');
    }

}

