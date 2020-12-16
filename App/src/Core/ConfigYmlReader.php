<?php


namespace Rushcon\Core;


use Nette\Utils\Finder;

class ConfigYmlReader
{
    /**
     * @param string $strpath
     * @param string $pluginName
     * @return array|bool
     */
    public static function readConfig(string $strpath, string $pluginName):array
    {
        $yaml = \yaml_parse_file($strpath);

        if (!$yaml) {
            return false;
        }
        $pluginInfo = [];
        foreach ($yaml as $key => $value) {

            if($value['name'] === $pluginName || in_array($pluginName, $value['alias'], true)) {
                $pluginInfo = $value;
                break;
            }
        }
        return $pluginInfo;
    }

    /**
     * @param string $pluginname
     * @return array
     */
    public static function getPluginConfig(string $pluginname):array
    {
        $plugins = PluginReader::getListOfPlugins();
        $yamlConfig = [];
        foreach ($plugins as $plugin) {
            $yamls = Finder::findFiles(Camelizer::decamelize($plugin) . '.info.yml')->from(PLUGINS . DS . $plugin);
            foreach ($yamls as $yaml) {
                $yamlConfig = self::readConfig($yaml->getPathName(), $pluginname);
            }
        }
        return $yamlConfig;
    }
}
