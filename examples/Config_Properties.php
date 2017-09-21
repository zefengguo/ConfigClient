<?php

require '../vendor/autoload.php';

class  Config_Properties
{
    private static $_instance = null;
    public $name;
    public $host;
    public $configMonitor;

    private function __construct()
    {
        $this->configMonitor = \smartisan\apollo\phpClient\ConfigManager::getInstance();
        $settings = $this->configMonitor->getSettingArray("101.properties");
        $this->name = $settings["name"];
        $this->host = $settings["host"];
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
