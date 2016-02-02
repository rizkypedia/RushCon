<?php namespace RushCon\Core;

use RushCon\Core\Shell;

class Dispatcher {
    
    public static function dispatch($args = array()) {
        Shell::parseArguments($args);
    }
    
}



