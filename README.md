## 手机号码归属地解析

#### 安装


```bash
composer require jiemo/mobile

```

```php
$mobile = new \Jiemo\Mobile\Parser();
// 解析
$mobile->parse(18624394876);
// 数据库版本
$mobile->getDbVersion();
```
