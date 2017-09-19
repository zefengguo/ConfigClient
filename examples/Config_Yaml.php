<?php
/**
 * Created by IntelliJ IDEA.
 * User: gzf-zephyr
 * Date: 2017/9/13
 * Time: 15:52
 */


require '../vendor/autoload.php';

class  Config_Yaml
{
    private static $_instance = null;
    public $name;
    public $host;
    public $configMonitor;

    private function __construct()
    {
        $this->configMonitor = \smartisan\apollo\phpClient\ConfigManager::getInstance();
        $settings = $this->configMonitor->getSettingArray("105.yaml");
        $this->name = $settings["db"]["name"];
        $this->host = $settings["db"]["host"];
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