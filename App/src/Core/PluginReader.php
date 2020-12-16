<?php


namespace Rushcon\Core;


use Nette\Utils\Finder;

class PluginReader
{
    /**
     * @return array
     */
    public static function getListOfPlugins(): array
    {
        $listPlugins = Finder::findDirectories()->from(PLUGINS)->limitDepth(0);
        $plugins = [];
        foreach ($listPlugins as $plugin) {
            $plugins[] = $plugin->getFileName();
        }
        return $plugins;
    }
}
