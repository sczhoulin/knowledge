# my_knowledge
知识库

#nginx 配置文件
server
{
    listen 80;
    #listen [::]:80 default_server ipv6only=on;
    server_name knowledge.net;
    index index.html index.htm index.php index.shtml;
    root  /data/knowledge;

    #error_page   404   /404.html;
    include enable-php.conf;

    location /nginx_status
    {
        stub_status on;
        access_log   off;
    }

    location / {

            if (!-e $request_filename) {
                    rewrite ^(.*)$ /router.php last;
                    break;
            }   
    }   

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
    }

    location ~ .*\.(js|css)?$
    {
        expires      12h;
    }

    location ~ /\.
    {
        deny all;
    }

    access_log  /usr/local/nginx/logs/access.log;

}
           