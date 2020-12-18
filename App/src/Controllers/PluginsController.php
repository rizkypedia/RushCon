<?php


namespace Rushcon\Controllers;

use Rushcon\Core\BaseController;
use Rushcon\Core\Console;
use Rushcon\Core\Container;
use Rushcon\Core\PluginReader;


class PluginsController extends BaseController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     *
     */
    public function listAction():void
    {
      $plugins = PluginReader::getListOfPlugins();

      foreach ($plugins as $plugin) {
          Console::pprintln($plugin);
      }
    }
}
