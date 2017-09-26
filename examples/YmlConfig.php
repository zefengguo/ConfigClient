<?php

require '../vendor/autoload.php';

class  YmlConfig
{
    private static $sInstance = null;
    private $name;
    private $host;
    private $configMonitor;

    private function __construct()
    {
        $this->configMonitor = \smartisan\apollo\phpClient\ConfigManager::getInstance();
        $settings = $this->configMonitor->getSettingArray("104.yml");
        $this->name = $settings["db"]["name"];
        $this->host = $settings["db"]["host"];
    }

    private function __clone()
    {

    }

    public function __get($property_name)
    {
        return $this->$property_name;
    }

    public static function getInstance()
    {
        if (is_null(self::$sInstance) || isset (self::$sInstance)) {
            self::$sInstance = new self ();
        }
        return self::$sInstance;
    }
}
