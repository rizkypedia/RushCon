<?php

namespace Rushcon\Controller;



use Rushcon\CoreBaseController;
use Rushcon\Core\Console;
use Rushcon\CoreContainer;

class Controller extends BaseController {

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

}

