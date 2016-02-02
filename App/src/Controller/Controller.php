<?php namespace RushCon\Controller;

use RushCon\Core\Console;

class Controller {
    
    public function __construct() {
        ;
    }
    
    public function getNamespace($namespace) {
        $namespaceParts = explode("\\", $namespace);
        return $namespaceParts[0];
    }
    
    public function clean($strText) {
        return trim($strText);
    }
    
}

