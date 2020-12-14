<?php
namespace Rushcon\Core;

use Rushcon\Core\Factories\ContainerFactory;
use Rushcon\Core\Factories\ControllerFactory;
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

        //namespace
        $namespace = $PluginParts['namespace'];
        //controller class
        $class = $PluginParts['controller'];
        //action/method
        $action = $PluginParts['action'];

        $obj = $namespace . NAMESPACE_DELIMITER . CONTROLLER_NAMESPACE_SUFFIX . NAMESPACE_DELIMITER . $class .CONTROLLER_SUFFIX;

        $instance = ControllerFactory::create($obj);
        call_user_func_array(array($instance, $action.ACTIONSUFFIX),$params);
    }

}
