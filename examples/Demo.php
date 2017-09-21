<?php

require "Config_Properties.php";
require "Config_Xml.php";
require "Config_Json.php";
require "Config_Yaml.php";
require "Config_Yml.php";

br();
showTime();
show(Config_Properties::getInstance());
showTime();
show(Config_Xml::getInstance());
showTime();
show(Config_Json::getInstance());
showTime();
show(Config_Yaml::getInstance());
showTime();
show(Config_Yml::getInstance());
showTime();

//清空shm
//Config_Properties::getInstance()->configMonitor->clearShmConfig("101.properties");


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

