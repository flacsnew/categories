## Categories Queries App

## Стек технологий

- PHP (Laravel)
- mysql

* Контроллер для БД: /app/Http/Controllers/CategoriesController.php
* Получаемые от сервера данные в формате JSON (response.data)
* Поддержка Docker (Php 7.4-fpm, nginx, mysql)

## Управление

Базовый URL для запросов: http://site/api/categories/#operation#

Заметка: #operation# - псевдоним (alias) для вызова операции с БД.
Примеры вызова для GET:

* http://site/api/categories/getBySlug?slug=second
* http://site/api/categories/getByID?id=1

### Создать категорию (add)

- Метод: POST
- Параметры: id, slug, name, description, active

Все параметры обязательны, кроме description.

### Изменить категорию (update)

- Метод: PUT
- Параметры: id, slug, name, description, createdDate, active

Все параметры необязательны, кроме slug. Поддерживается частичное обновление.

### Удалить категорию (delete)

- Метод: DELETE
- Параметры: slug

Параметр slug обязателен.

### Получить категорию по id (getByID)

- Метод: GET
- Параметры: id

Параметр id обязателен.


### Получить категорию по slug (getBySlug)

- Метод: GET
- Параметры: slug

Параметр slug обязателен.

### Фильтрация (filter)

- Метод: GET
- Параметры: name, description, active, search, pageSize, page, sort

#### Особенности:

* Поддержка одновременной сортировки (AND) по полям (name, description, active)
* Сортировка по имени поля sort=#name# (только разрешенные поля)
* Направление сортировки регулируется знаком "-" (DESC) в значение поля sort // sort=-description
* Поддержка пагинации

## Установка (Docker)

### Окружение

```
docker-compose build app
```
```
docker-compose up -d
```
```
docker-compose exec app composer install
```
```
docker-compose exec app php artisan key:generate
```

### Редактируем файл .env

```
DB_HOST=db
```

### Импортируем данные в базу

```
docker-compose exec app php artisan migrate:fresh
```
```
docker-compose exec app php artisan db:seed --class=MainSeeder
```