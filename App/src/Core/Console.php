<?php namespace RushCon\Core;

class Console {
    
    public static function pprintln($msg = "") {
        echo $msg ."\n";
    }
    
    public static function pprint($msg) {
        echo $msg;
    }
    
    public static function lineSeperator() {
        return "\n";
    }
    
}

