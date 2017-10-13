<?php
require '../vendor/autoload.php';
//修改configclient/Config.php配置项CONFIG_FILE_PATH为examples/configfiles文件夹所在绝对目录,运行Demo.php
//properties,json
//获取配置文件加载器实例
$configLoader =\Smartisan\ConfigClient\ConfigLoader::getInstance();
//$configLoader->clearShm("config.properties");
//$configLoader->clearShm("config.yaml");
//$configLoader->clearShm("config.xml");
//加载指定配置文件（namespace为配置文件名称全称），获取配置项Array
$settings = $configLoader->getConfigArray("config.properties");
//获取配置项
echo $settings["name"] . "\n";
//yaml,yml
$settings = $configLoader->getConfigArray("config.yaml");
echo $settings["db"]["name"] . "\n";

//xml
$settings = $configLoader->getConfigArray("config.xml");
echo $settings["db"][0]["name"][0]["#text"] . "\n";