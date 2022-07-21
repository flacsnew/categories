## Categories Queries App

## Stack

- PHP (Laravel)
- Database (mysql Provider)

* Ключевой контроллер: /app/Http/Controllers/CategoriesController.php
* Получаемые от сервера данные в формате JSON (response.data)

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
* Сортировка по имени поля <name> (только разрешенные поля)
* Направление сортировки регулируется знаком "-" (DESC) в значение поля sort
* Поддержка пагинации
