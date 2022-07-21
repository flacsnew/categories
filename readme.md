## Categories Queries App

## Stack

- PHP (Laravel)
- Database (mysql Provider)

## Управление

### Создать категорию

- Метод: POST
- Параметры: id, slug, name, description, active

Все параметры обязательны, кроме description.

### Изменить категорию

- Метод: PUT
- Параметры: id, slug, name, description, createdDate, active

Все параметры необязательны, кроме slug. Поддерживается частичное обновление.

### Удалить категорию

- Метод: DELETE
- Параметры: slug

Параметр slug обязателен.

### Получить категорию по id

- Метод: GET
- Параметры: id

Параметр id обязателен.


### Получить категорию по slug

- Метод: GET
- Параметры: slug

Параметр slug обязателен.

### Фильтрация

- Метод: GET
- Параметры: name, description, active, search, pageSize, page, sort

#### Особенности:

* Поддержка одновременной сортировки (AND) по полям (name, description, active)
* Сортировка по имени поля <name> (только разрешенные поля)
* Направление сортировки регулируется знаком "-" (DESC) в значение поля sort
* Поддержка пагинации
