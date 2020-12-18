<?php
namespace Rushcon\Core;

use Rushcon\Core\Factories\ControllerFactory;
use Rushcon\Model\ConnectionManager;

class Load
{
    private static $instance=null;

    public static function getInstance(){
         if (null === self::$instance) {
             self::$instance = new self;
         }
         return self::$instance;
    }

    /**
     * @param array $PluginParts
     * @param array $params
     */
    public function runObject($PluginParts = array(), $params = array())
    {
        //namespace
        $pluginName = $PluginParts['namespace'];
        //controller class
        $class = $PluginParts['controller'];
        //action/method
        $action = $PluginParts['action'];

        // Core or Plugin
        $pluginInfo = ConfigYmlReader::readConfig(DEFAULT_CORE_YML, $pluginName);
        if (!$pluginInfo) {
            $pluginInfo = ConfigYmlReader::getPluginConfig($pluginName);
        }

        if (empty($pluginInfo)) {
            throw new \RuntimeException('Unkown Plugin: ' . $pluginName);
        }

        $obj = $pluginInfo['controllers'] . NAMESPACE_DELIMITER . $class .CONTROLLER_SUFFIX;

        $instance = ControllerFactory::create($obj);
        call_user_func_array(array($instance, lcfirst(Camelizer::camelize($action)).ACTIONSUFFIX),$params);
    }

}
