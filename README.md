# BlogApp
## Prérequis

- docker
- php 8.2
- Symfony 7
- docker compose

## Build Project

```console
make build
```

API visible in http://localhost:8085/
DataBase adminer visible in http://localhost:8083/ : user:root ; password:root;pqsql:db


## Start Services

```console
make start
```

## Get in PHP container

```console
make in
```
if you have problem in getting Token Run inside container this command : chown -R www-data:www-data config/jwt
 Run Composer install inside container to install all compoenents

## Run Tests

```console
make test
```

## Migrate Entity database

```console
make migrate
```

```console
 php bin/console assets:install
```
