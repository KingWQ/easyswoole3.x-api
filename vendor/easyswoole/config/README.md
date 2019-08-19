# config
存取配置

##安装方法
```php
composer require easyswoole/config
```

##使用方法
SplArray 用法
```php
<?php

$arr=['aa'=>'123'];
$config=new \EasySwoole\Config\SplArrayConfig('dev');
$config->load($arr);
$config->getConf('aa');
$config->setConf('bb',234);
$arr2=['abc'=>'easyswoole'];
$config->merge($arr2);
$config->clear();
```

SwooleTable 用法
```php
<?php

$arr=['aa'=>'123'];
$config=new \EasySwoole\Config\TableConfig('dev');
$config->load($arr);
$config->getConf('aa');
$config->setConf('bb',234);
$arr2=['abc'=>'easyswoole'];
$config->merge($arr2);
$config->clear();
```


