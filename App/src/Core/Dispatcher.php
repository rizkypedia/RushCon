<?php namespace Rushcon\Core;

use Rushcon\Core\Shell;

class Dispatcher {

    public static function dispatch($args = array()) {
        Shell::parseArguments($args);
    }

}



