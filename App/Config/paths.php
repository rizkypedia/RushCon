<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT_PATH', dirname(__DIR__));
define('PROJECT_ROOT_PATH', dirname(ROOT_PATH));
define('VENDOR', PROJECT_ROOT_PATH . DS . "vendor" );
define('TMP', ROOT_PATH . DS . "tmp");
define('PLUGINS', ROOT_PATH . DS . "src/custom/plugins");
define('DEFAULT_CORE_YML', ROOT_PATH . DS ."src/rushcon.yml") ;
