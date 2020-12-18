<?php

namespace Rushcon\Core;

class Console
{

    public static function pprintln($msg = "")
    {
        echo $msg."\n";
    }

    public static function pprint($msg)
    {
        echo $msg;
    }

    public static function lineSeperator()
    {
        return "\n";
    }

    public function getNamespace($namespace)
    {
        $namespaceParts = explode("\\", $namespace);

        return $namespaceParts[0];
    }

    public function clean($strText)
    {
        return trim($strText);
    }
}

