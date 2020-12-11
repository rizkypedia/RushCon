<?php namespace Rushcon\Core;

use Rushcon\Model\ConnectionManager;

class Load {
    private static $instance=null;

    public static function getInstance(){
         if (null === self::$instance) {
             self::$instance = new self;
         }
         return self::$instance;
    }

    public function runObject($PluginParts = array(), $params = array()) {
        $cons = ConnectionManager::$connections;

        //namespace
        $namespace = $PluginParts['namespace'];
        //controller class
        $class = $PluginParts['controller'];
        //action/method
        $action = $PluginParts['action'];


        $obj = $namespace ."\\Controller\\" . $class ."Controller";
        $instance = new $obj();
        call_user_func_array(array($instance, $action.ACTIONSUFFIX),$params);
    }

}
