<a href="https://ioz.vn">
    <img width="120" src="https://ioz.vn/public/assets/images/logo.png">
</a>

------------------------------------------

# Infomation
- IOZ Laravel example
- Laravel: 11.x

# VS Code Extensions
- PHP Intelephense

# Docker setup

## Required
- Docker
- Docker compose

## Images
- Mysql: 8.3
- PHP-Apache: 8.2
- Redis: latest

## Terminal
```
// run docker
$ docker compose up -d

// run project
$ sh run.local.sh

// open with http://127.0.0.1:49011

// exec php container
$ docker compose exec ioz_php bash

// exec mysql container
$ docker compose exec ioz_mysql bash

// exec redis container
$ docker compose exec ioz_redis bash

```

## Network infomation
- Mysql: 
    - Host: 10.10.10.2
    - Port: 49010
    - User: root
    - Password: PwDev123

- PHP: 
    - Host: 10.10.10.3
    - Port: 49011

- Redis: 
    - Host: 10.10.10.4
    - Port: 49012

# Documents

- https://github.com/alexeymezenin/laravel-best-practices

- [Repository - Service](documents/repository-service.md)