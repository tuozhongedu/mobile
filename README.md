## 手机号码归属地解析

* 使用 [https://github.com/ls0f/phone](https://github.com/ls0f/phone) 提供的数据库

#### 安装

```bash
composer require jiemo/mobile

```

```php
$mobile = new \Jiemo\Mobile\Parser();
// 解析
$mobile->parse(13333333333);
// 数据库版本
$mobile->getDbVersion();

```

* 命令行工具

```bash
php bin/parsemobile 13333333333
```

    Array
    (
        [province] => 辽宁
        [city] => 大连
        [zip_code] => 116000
        [area_code] => 0411
        [isp] => 联通
    )
