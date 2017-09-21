<?php

require '../vendor/autoload.php';

class  Config_Xml
{
    private static $_instance = null;
    public $name;
    public $host;
    public $configMonitor;

    private function __construct()
    {
        $this->configMonitor = \smartisan\apollo\phpClient\ConfigManager::getInstance();
        $settings = $this->configMonitor->getSettingArray("102.xml");
        $this->name = $settings["db"][0]["name"][0]["#text"];
        $this->host = $settings["db"][0]["host"][0]["#text"];
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$_instance) || isset (self::$_instance)) {
            self::$_instance = new self ();
        }
        return self::$_instance;
    }
}
