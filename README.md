# README

> ВНИМАНИЕ!
>
> Файлы Docker Compose (кроме `docker-compose.local.yml`) редактировать с максимальной осторожность!
>
> Данные файлы влияют на работу приложения на сервере!

> ВНИМАНИЕ!
>
> Файл `.gitlab-ci.yml` редактировать запрещено!
> 
> Это место работы администратора!

## Как запускать проект?

> ВНИМАНИЕ!
> 
> Данная инструкция показывает как развернуть backend локально.

Создайте `.env` файл в корне директории.

Добавьте туда переменную:

```dotenv
COMPOSE_FILE=docker-compose.yml:docker-compose.local.yml
```

Дополните файл `docker-compose.local.yml` так, чтобы вы могли комфортно разрабатывать локально.

Используйте Makefile или 
Запустите:

```shell
docker-compose up
```