# Очень быстрый запуск 

```
docker-compose up --build
```

# Эндпойнты и их параметры

## Буду приводить примеры где пользователь не допустил ошибки при передаче/получении данных и нет никакой ошибки сервера. 

### ```http://127.0.0.1:8080/register``` - метод POST

Пример тела: 

Формат json.
```
{
    "name": "kirill",
    "email": "kirill@webant.ru",
    "password": "123321"
}
```
Пример ответа:

Формат json. Код 201.

```
{
    "created": true,
    "name": "kirill"
}
```

### ```http://127.0.0.1:8080/login``` - Метод POST

Пример тела:

Формат json.

```
{
    "email": "kirill@webant.ru",
    "password": "123321"
}
```

Пример ответа:

Формат json. Код 200

```
{
    "user": "Dana@gmail.ru",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJEYW5hQGdtYWlsLnJ1IiwidXNlcklkIjozLCJuYW1lIjoiRGFuYSIsImVtYWlsIjoiRGFuYUBnbWFpbC5ydSIsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJpYXQiOjE3NzgxNjcyMjMsImV4cCI6MTc3ODE3MDgyM30.W5jb7mYOXgMl45W2e2PyljQ3aNuaoAD2aTCLLs-s1hk"
}
```

### ```http://127.0.0.1:8080/picture``` - метод POST

Пример тела:

Формат form data 

- name text Природа - Обязательное поле
  
- image file <Тут ваша картинка> - Обязательное поле

- description text абвгде -можно оставить пустым

- category text New/Popular - их всего 2. Это Enum тип.

Также необходимо передать header с токеном

Пример:

Authorization      Bearer eyJ0eXAiOiJKV1QiLCJhbGc......

Пример ответа: Код 201

```
{
    "answ": "img/kirill/69fcadcc0290a_Природа.jpg"
}
```

### Просмотр картинок. Метод GET. 2 вариации

- ```http://127.0.0.1:8080/picture/1```
- ```http://127.0.0.1:8080/picture/1?category=New```

Примера нет. Я снес базу и картинок больше нет.

### P.S. Картинки хранятся в папке ./public/image/<Имя пользователя>/

