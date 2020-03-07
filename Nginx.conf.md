# Nginx配置参考

请根据你的实际情况修改域名和代码所在目录；
* 域名：`scan2pay.org`
* 目录：`/var/www/scan2pay/www/`


```
    server {
        listen       80;
        server_name  scan2pay.org;

        root   /var/www/scan2pay/www/;
        index  index.php index.html index.htm;

        location / {
            try_files $uri $uri/ /index.php?$args;
        }

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        location ~ (index)\.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  /var/www/scan2pay/www$fastcgi_script_name;
            include        fastcgi_params;
        }

        # deny all other php
        #
        location ~ \.php {
            deny  all;
        }
    }
```
