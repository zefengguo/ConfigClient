<?php

require "ConfigProperties.php";
require "ConfigXml.php";
require "ConfigJson.php";
require "ConfigYaml.php";
require "ConfigYml.php";

br();
showTime();
show(ConfigProperties::getInstance());
showTime();
show(ConfigXml::getInstance());
showTime();
show(ConfigJson::getInstance());
showTime();
show(ConfigYaml::getInstance());
showTime();
show(ConfigYml::getInstance());
showTime();

//清空shm
//ConfigProperties::getInstance()->configMonitor->clearShmConfig("101.properties");


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

