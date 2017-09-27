<?php

require "PropertiesConfig.php";
require "XmlConfig.php";
require "JsonConfig.php";
require "YamlConfig.php";
require "YmlConfig.php";

show(PropertiesConfig::getInstance());
show(XmlConfig::getInstance());
show(JsonConfig::getInstance());
show(YamlConfig::getInstance());
show(YmlConfig::getInstance());
//清空shm
#PropertiesConfig::getInstance()->configMonitor->clearShmConfig("101.properties");


function br()
{
    echo "\n";
    echo "<br/>";
}

function show($config)
{
    echo "file:" . get_class($config);
    br();
    echo "host:" . $config->host;
    br();
    echo "name:" . $config->name;
    br();
}

?>

