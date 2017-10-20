# aliyun-iot
基于阿里云物联网Iot套件打包14个服务端API，实现了composer加载，使用简单。

# 加载
```
composer require mrgoon/aliyun-iot
```

# 使用
``` 
$iot_service = new \Mrgoon\AliIot\AliIot('access key', 'access secret');
$response = $iot_service->queryDevice('product key', 'current page', 'page size');
```

# 说明
代码整体封装是按照[阿里云套件文档](https://help.aliyun.com/document_detail/45399.html)而来，并附有返回json示例.
