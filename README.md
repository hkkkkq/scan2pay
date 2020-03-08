# Scan2Pay

Scan2Pay is a simple and fast payment gateway for wechat and alipay, only support qr code / image scan, written in php.

Scan page snapshot:

![Snapshot of scan page](./snapshot/scan.png ''Scan2Pay snapshot of scan page'')


## Features

* Use redis and local file to save data
* Small and quick
* User should input a short order number as payment remark
* You can update order status
* You can view orders by day, month, year


## Install

You need get PHP, php-pecl-redis, php-fpm, nginx and redis ready first.

Such as install commands in CentOS:
```
yum -y install php php-fpm php-pecl-redis nginx redis
```

Then follow the steps:

1. Download source code
2. Replace the qr image with your wechat and alipay qr image in the directory: `/www/img/`
3. Create directory `/runtime` and add write permission to php-fpm
4. Config your web server with a domain, example in [Nginx.conf.md](./Nginx.conf.md)

That's all, now visit your domain to get start.


## Config

Config file:
`conf/appConfig.php`

* Wechat qr image file path
* Alipay qr image file path
* Password of administrator
* Notify url to update order status when it's changed


## For developers

### Order api

Http api to create new order and scan to pay.

Url:
`api/createorder`

Method:
`POST`

Parameters:
```
price       - Amount of CN Yuan (RMB)
order_id    - [optional] Id of the order
user_id     - [optional] Id of the user
```

### Http notify api

If you call api/createorder with pamameter **order_id** and there is notify url,
the order status will post to the notify url when the order status is changing to **paid** or **refund**.

Url:
`http(s)://yourdomain/notifyurl`

Method:
`POST`

Parameters:
```
order_id    - Id of the order
status      - Status: paid / refund
user_id     - [optional] Id of the user
```


### App parameters

Parameter "USC::$app" and "$viewData" always can be used in the view file.

```
<?php
print_r(USC::$app);
print_r($viewData);
?>
```


### Core class

Core class directory:
`lib/`

* DataFactory - Get, Save data from redis and local file
* OrderFactory - Get, Save orders, and order status update

