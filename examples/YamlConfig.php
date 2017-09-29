<?php

require '../vendor/autoload.php';

class  YamlConfig
{
    private static $sInstance = null;
    private $name;
    private $host;
    private $configMonitor;

    private function __construct()
    {
        $this->configMonitor = \smartisan\apollo\phpClient\ConfigManager::getInstance();
        $settings = $this->configMonitor->getConfigArray("105.yaml");
        $this->name = $settings["db"]["name"];
        $this->host = $settings["db"]["host"];
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
