<?php

require "PropertiesConfig.php";
require "XmlConfig.php";
require "JsonConfig.php";
require "YamlConfig.php";
require "YmlConfig.php";

br();
showTime();
show(PropertiesConfig::getInstance());
showTime();
show(XmlConfig::getInstance());
showTime();
show(JsonConfig::getInstance());
showTime();
show(YamlConfig::getInstance());
showTime();
show(YmlConfig::getInstance());
showTime();

//清空shm
PropertiesConfig::getInstance()->configMonitor->clearShmConfig("101.properties");


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

function showTime()
{
    echo "time:" . getMillisecond();
    br();
}

function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

?>

