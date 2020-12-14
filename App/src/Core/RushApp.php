<?php

namespace Rushcon\Core;

class RushApp
{
    private static $__shortCommands = array(
        "-v" => "Shows the Rushcon Version",
        "-h" => "Shows the help",
        "-a" => "About Rushcon",
        "-e" => "Executes a Rushcon plugin e. g. PluginName:Controller:action [params]");


    public static function run($args = array()) {
        array_shift($args);


        if (empty($args)) {
            self::errors("0");
        }

        if (!array_key_exists($args[0], self::$__shortCommands)) {
            self::errors("1", $args[0]);
        }

        $paremeters = isset($args[2]) ? self::getParameters($args) : array();

        switch ($args[0]) {
            case "-a":
                self::printAbout();
                break;
            case "-v":
                self::printVersion();
                break;
            case "-h":
                self::printHelp();
                break;

            default:

                $plugin = self::parseInput($args[1]);

                $l = Load::getInstance();
                $l->runObject($plugin, $paremeters);
                break;
        }

    }

    private static function errors($errorType = "0", $vars = null) {
        $errors = array(
            "0" => "No Parameters detected!",
            "1" => "Unkown prefix " . (empty($vars) ? "" : $vars) . " allowed prefix: " . Console::lineSeperator() . self::printLegalPrefix(),
            "2" => "Wrong Plugincall! Call for Plugin must look like this: PluginName.Controller.[action] [params]. Each element before [params] must be seperated by " . DELIMITER . ". [action] is optional, by leaving this option out an Rushcon tries to call an Index method"
        );

        Console::pprint($errors[$errorType]);
        die();
    }

    private static function printLegalPrefix() {
        $msg = "";

        foreach (self::$__shortCommands as $key => $description) {
            $msg .= $key . " ". $description . Console::lineSeperator();
        }

        return $msg;
    }

    private static function printAbout() {
        $about = "";

        if (file_exists(PROJECT_ROOT_PATH . DS . "composer.json")) {
            $jsonFile = json_decode(file_get_contents(PROJECT_ROOT_PATH . DS . "composer.json"));
            //var_dump($jsonFile);
            $about .= $jsonFile->name . Console::lineSeperator();
            $about .= $jsonFile->description . Console::lineSeperator();
            $about .= "Version: " . $jsonFile->version . Console::lineSeperator();
            $about .= "License: " . $jsonFile->license . Console::lineSeperator();
            $about .= "Developed by: ";
            $about .= $jsonFile->authors[0]->name . Console::lineSeperator();
            $about .= "E-Mail: " . $jsonFile->authors[0]->email . Console::lineSeperator();
            $about .= "Homepage: " . $jsonFile->homepage . Console::lineSeperator();
            $about .= "Copyright by rzkdwn1979 " . date('Y');

        }

        Console::pprintln($about);
    }

    private static function printVersion() {
        $version = "";
        if (file_exists(PROJECT_ROOT_PATH . DS . "composer.json")) {
            $jsonFile = json_decode(file_get_contents(PROJECT_ROOT_PATH . DS . "composer.json"), false);
            $version .= $jsonFile->version . Console::lineSeperator();
        }

        Console::pprint($version);
    }

    private static function printHelp() {
        Console::pprintln(Console::lineSeperator() . self::printLegalPrefix());
    }

    private static function parseInput($input) {
        $userInputs = explode(DELIMITER, $input);

        $PluginParts = array();


        if (count($userInputs) < 2 || count($userInputs) > 3) {
            self::errors("2");
        }

        $PluginParts['namespace'] = $userInputs[0];
        $PluginParts['controller'] = $userInputs[1];
        $PluginParts['action'] = $userInputs[2] ?? "Index";

        return $PluginParts;

    }

    private static function getParameters($args, $startIndex = 2) {
        $params = array();
        for ($i=$startIndex, $iMax = count($args); $i< $iMax; $i++) {
            $params[] = $args[$i];
        }
        return $params;
    }
}
