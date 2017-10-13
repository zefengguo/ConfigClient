<?php
namespace Smartisan\ConfigClient;

class ConfigLoader
{
    private static $sInstance;
    private static $sSuffixArray = array('properties', 'xml', 'json', 'yml', 'yaml');

    private function __construct()
    {
        $this->checkConfig();
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!(self::$sInstance instanceof self)) {
            self::$sInstance = new self ();
        }
        return self::$sInstance;
    }

    public function getConfigArray($namespace)
    {
        $configArray = null;
        $suffix = $this->getSuffix($namespace);
        if (!in_array($suffix, self::$sSuffixArray)) {
            throw new \Exception("not support the file format:" . $suffix);
        }
        $content = $this->getConfigByNameSpace($namespace);
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
        if (!defined('SHARE_CACHE_SIZE')) {
            define("SHARE_CACHE_SIZE", 5);
        }
    }

    private function getConfigByNameSpace($namespace)
    {
        $path = $this->combinePath(CONFIG_FILE_PATH, $namespace);
        if (!file_exists($path)) {
            throw new \Exception(" no such file " . $path);
        }
        if (PHP_OS != "Linux") {
            return $this->loadFile($path);
        }
        $shm_id = $this->getShmId($path);
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

    private function getShmId($path)
    {
        $shm_key = ftok($path, '2');
        $shm_id = shmop_open($shm_key, "c", 0777, SHARE_CACHE_SIZE * 1024);
        return $shm_id;
    }

    public function clearShm($namespace)
    {
        if (PHP_OS != "Linux") {
            return;
        }
        $path = $this->combinePath(CONFIG_FILE_PATH, $namespace);
        if (!file_exists($path)) {
            throw new \Exception(" no such file " . $path);
        }
        $shm_id = $this->getShmId($path);
        shmop_delete($shm_id);
        shmop_close($shm_id);
    }

    private function loadFile($path)
    {
        $f = fopen($path, "r");
        $data = fread($f, filesize($path));
        fclose($f);
        return $data;
    }

    private function getSuffix($namespace)
    {
        $namespaceSuffixArray = explode('.', $namespace);
        return end($namespaceSuffixArray);
    }

    private function combinePath($path, $anotherPath)
    {
        return realpath($path) . DIRECTORY_SEPARATOR . $anotherPath;
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

