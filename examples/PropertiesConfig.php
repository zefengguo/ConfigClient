<?php

require '../vendor/autoload.php';

class  PropertiesConfig
{
    private static $sInstance = null;
    private $name;
    private $host;
    private $configMonitor;

    private function __construct()
    {
        $this->configMonitor = \smartisan\apollo\phpClient\ConfigManager::getInstance();
        $settings = $this->configMonitor->getConfigArray("101.properties");
        $this->name = $settings["name"];
        $this->host = $settings["host"];
    }

    private function __clone()
    {

    }

    public function __get($propertyName)
    {
        return $this->$propertyName;
    }

    public static function getInstance()
    {
        if (is_null(self::$sInstance) || isset (self::$sInstance)) {
            self::$sInstance = new self ();
        }
        return self::$sInstance;
    }
}
