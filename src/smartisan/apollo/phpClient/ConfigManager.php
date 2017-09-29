<?php

namespace smartisan\apollo\phpClient;

class ConfigManager
{
    private static $sInstance = null;
    private $filePath = "";
    private $suffixArray = array('properties', 'xml', 'json', 'yml', 'yaml');

    private function __construct()
    {
        $this->checkConfig();
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$sInstance) || isset (self::$sInstance)) {
            self::$sInstance = new self ();
        }
        return self::$sInstance;
    }

    public function getConfigArray($nameSpace)
    {
        $configArray = null;
        $suffix = $this->getSuffix($nameSpace);
        if (!in_array($suffix, $this->suffixArray)) {
            throw new \Exception("not support the file format:" . $suffix);
        }
        $content = $this->getConfigByNameSpace($nameSpace);
        if (is_null($content) || empty($content)) {
            return $configArray;
        }
        switch ($suffix) {
            case "properties":
                $configArray = parse_ini_string($content, true);
                break;
            case "xml":
                $configArray = $this->getXmlToArray($content);
                break;
            case "json":
                $configArray = json_decode($content, TRUE);
                break;
            case "yml":
                $configArray = yaml_parse($content);
                break;
            case "yaml":
                $configArray = yaml_parse($content);
                break;
            default:
                ;
        }
        return $configArray;
    }

    private function checkConfig()
    {
        if (!is_dir(CONFIG_FILE_PATH)) {
            throw new \Exception("no such directory:" . CONFIG_FILE_PATH);
        }
        $this->filePath = CONFIG_FILE_PATH;
        $suffix = substr($this->filePath, -1);
        if ($suffix != "/" && $suffix != "\\") {
            $this->filePath = $this->filePath . "/";
        }
        if (!defined('SHARE_CACHE_SIZE')) {
            define("SHARE_CACHE_SIZE", 5);
        }
    }

    private function getConfigByNameSpace($nameSpace)
    {
        $pathTem = $this->filePath . $nameSpace;
        if (!file_exists($pathTem)) {
            throw new \Exception(" no such file " . $pathTem);
        }
        return $this->getShm($pathTem);
    }

    public function clearShm($nameSpace)
    {
        if (PHP_OS != "Linux") {
            return;
        }
        $path = $this->filePath . $nameSpace;
        if (!file_exists($path)) {
            throw new \Exception(" no such file " . $path);
        }
        $shm_key = ftok($path, '2');
        $shm_id = shmop_open($shm_key, "c", 0644, SHARE_CACHE_SIZE * 1024);
        shmop_delete($shm_id);
        shmop_close($shm_id);
    }

    private function getShm($path)
    {
        if (PHP_OS != "Linux") {
            return $this->loadFile($path);
        }
        $shm_key = ftok($path, '2');
        $shm_id = shmop_open($shm_key, "c", 0644, SHARE_CACHE_SIZE * 1024);
        $data = shmop_read($shm_id, 0, SHARE_CACHE_SIZE * 1024);
        if ($data != null) {
            $data = str_replace("\x00", "", $data);
            if (empty($data)) {
                $data = $this->loadFile($path);
                if (!empty($data)) {
                    shmop_write($shm_id, $data, 0);
                }
            }
        }
        shmop_close($shm_id);
        return $data;
    }

    private function loadFile($path)
    {
        $f = fopen($path, "r");
        $data = fread($f, filesize($path));
        fclose($f);
        return $data;
    }

    private function getSuffix($nameSpace)
    {
        $nameSpaceSuffixArray = explode('.', $nameSpace);
        return end($nameSpaceSuffixArray);
    }

    private function getXmlToArray($content)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($content);
        return $this->getXmlNodeToArray($dom->documentElement);
    }

    private function getXmlNodeToArray($node)
    {
        $array = false;
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $array[$attr->nodeName] = $attr->nodeValue;
            }
        }
        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 1) {
                $array[$node->firstChild->nodeName] = $this->getXmlNodeToArray($node->firstChild);
            } else {
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeType != XML_TEXT_NODE) {
                        $array[$childNode->nodeName][] = $this->getXmlNodeToArray($childNode);
                    }
                }
            }
        } else {
            return $node->nodeValue;
        }
        return $array;
    }
}

