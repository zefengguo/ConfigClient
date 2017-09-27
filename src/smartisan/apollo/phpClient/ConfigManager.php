<?php

namespace smartisan\apollo\phpClient;

class ConfigManager
{
    private static $sInstance = null;
    private $filePath = "";

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

    public function getSettingArray($nameSpace)
    {
        if ($this->getExtension($nameSpace) == "properties") {
            return $this->propertiesArray($nameSpace);
        } elseif ($this->getExtension($nameSpace) == "xml") {
            return $this->xmlArray($nameSpace);
        } elseif ($this->getExtension($nameSpace) == "json") {
            return $this->jsonArray($nameSpace);
        } elseif ($this->getExtension($nameSpace) == "yml") {
            return $this->ymlArray($nameSpace);
        } elseif ($this->getExtension($nameSpace) == "yaml") {
            return $this->yamlArray($nameSpace);
        }
        return null;
    }

    private function checkConfig()
    {
        if (!is_dir(CONFIG_FILE_PATH)) {
            throw new \Exception("no such directory CONFIG_FILE_PATH");
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
        return $this->getShmConfig($pathTem);
    }

    public function clearShmConfig($nameSpace)
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

    private function getShmConfig($path)
    {
        if (PHP_OS != "Linux") {
            return $this->readFile($path);
        }
        $shm_key = ftok($path, '2');
        $shm_id = shmop_open($shm_key, "c", 0644, SHARE_CACHE_SIZE * 1024);
        $data = shmop_read($shm_id, 0, SHARE_CACHE_SIZE * 1024);
        if ($data != null) {
            $data = str_replace("\x00", "", $data);
            if (empty($data)) {
                $data = $this->readFile($path);
                if (!empty($data)) {
                    shmop_write($shm_id, $data, 0);
                }
            }
        }
        shmop_close($shm_id);
        return $data;
    }

    private function readFile($path)
    {
        $f = fopen($path, "r");
        $data = fread($f, filesize($path));
        fclose($f);
        return $data;
    }

    private function propertiesArray($nameSpace)
    {
        if ($this->getExtension($nameSpace) != "properties") {
            throw new \Exception("the file suffix is not .properties");
        }

        $content = $this->getConfigByNameSpace($nameSpace);
        if ($content == "") {
            return $content;
        }
        return parse_ini_string($content, true);
    }

    private function xmlArray($nameSpace)
    {
        if ($this->getExtension($nameSpace) != "xml") {
            throw new Exception("the file suffix is not .xml");
        }
        $content = $this->getConfigByNameSpace($nameSpace);
        if ($content == "") {
            return $content;
        }
        $dom = new \DOMDocument();
        $dom->loadXML($content);
        return $this->getArray($dom->documentElement);
    }

    private function jsonArray($nameSpace)
    {
        if ($this->getExtension($nameSpace) != "json") {
            throw new Exception("the file suffix is not .json");
        }
        $content = $this->getConfigByNameSpace($nameSpace);
        if ($content == "") {
            return $content;
        }
        return json_decode($content, TRUE);
    }

    private function ymlArray($nameSpace)
    {
        if ($this->getExtension($nameSpace) != "yml" && $this->getExtension($nameSpace) != "yaml") {
            throw new Exception("the file suffix is not .yml or .yaml");
        }
        $content = $this->getConfigByNameSpace($nameSpace);
        if ($content == "") {
            return $content;
        }
        return yaml_parse($content);
    }

    private function yamlArray($nameSpace)
    {
        return $this->ymlArray($nameSpace);
    }

    private function getExtension($nameSpace)
    {
        $nameSpaceSuffixArray = explode('.', $nameSpace);
        return end($nameSpaceSuffixArray);
    }

    private function getArray($node)
    {
        $array = false;

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $array[$attr->nodeName] = $attr->nodeValue;
            }
        }
        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 1) {
                $array[$node->firstChild->nodeName] = $this->getArray($node->firstChild);
            } else {
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeType != XML_TEXT_NODE) {
                        $array[$childNode->nodeName][] = $this->getArray($childNode);
                    }
                }
            }
        } else {
            return $node->nodeValue;
        }
        return $array;
    }
}

