# Apollo client php

Apollo 客户端 PHP 版。
## 项目目录
```html
│  composer.json
├─configs     demo读取配置文件  
├─examples                      demo
│      JsonConfig.php
│      PropertiesConfig.php
│      XmlConfig.php
│      YamlConfig.php
│      YmlConfig.php
│      Demo.php
├─src
│  └─smartisan
│      └─apollo
│          └─phpClient
│                  Config.php        配置文件
│                  ConfigManager.php 主文件

```

## 使用
```html
1.修改配置文件存放目录（src/apollo/phpClient/Config.php）
define("CONFIG_FILE_PATH","配置文件存放目录")

Linux环境下默认共享内存为5kb，若配置文件过大,需添加配置项（kb）
define("SHARE_CACHE_SIZE",5);

备注：
Config.php配置应与apollo-agent中config_file_path、share_cache_size保持一致,

使用参考Demo.php
```




