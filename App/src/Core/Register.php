<?php namespace Rushcon\Core;

class Register {

    public static function table($tablename, $credentials) {
        $parts = explode(".", $tablename);
        $namespace= $parts[0];
        $table = $parts[1] . TABLECLASS_SUFFIX;

        $objStr = $namespace ."\\" . MODELNAMESPACE ."\\" . $table;

        $obj = new $objStr($credentials);

        return $obj;
    }

}
