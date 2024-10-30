# Комментарии к выбранным решениям

Первым делом, перед написанием кода, была проведена **нормализация** данной в тестовом задании **таблицы** и **вычленение классов**.

**На какие таблицы распалась исходная таблица**:
- events;
- prices;
- tickets;
- orders;
- purchased_tickets;
- users.

**events**:
Содержит информацию о каждом событии (название, описание, начало и конец события).
Почему была создана: наличие атрибутов у события, поле event_date исходной таблицы не зависит от первичного ключа id.

**prices**:
Содержит информацию о ценниках билетов для каждого события (тип билета - цена билета).
Почему была создана: избегание дубликатов; исходная таблица имела поля вроде ticket_adult_price, т.к. таблица содержит все заказы, то там может быть несколько
записей, относящихся к одному событию, а т.к. цена билета зависит от типа и самого события, то эта информация будет дублироваться в исходной таблице.

**tickets**:
На одну запись приходится "реальный" билет на каждое событие.
**Почему была создана**: если обратиться к реальному миру, то каждый билет представляет собой отдельную сущность, а сущность, по DDD, обладает собственным жизненным циклом (билет создается,
переходит из одного состояния в другой).

**orders**:
Содержит информацию обо всех заказах в системе.
Представляет собой преобразованную исходную таблицу.

**purchased_tickets**:
Содержит информацию о деталях покупки билета (barcode, в каком заказе был куплен).
**Почему была создана**: потому что билет имеет два статуса "свободный для продажи" и "куплен", билет со вторым статусом обладает дополнительными атрибутами - barcode и id заказа, т.к.
эти атрибуты характерны только для билетов со статусом "куплен", то эти атрибуты неоптимально хранить там же, где и обычные билеты (присутствие NULL-значений указывает, что
мы имеем дело с разными сущностями).

**users**:
Содержит информацию о пользователях.
**Почему была создана**: потому что исходная таблица содержала user_id.

**На основе этих таблиц были созданы следующие классы**:
- Event;
- Order;
- PurchasedTicket;
- Ticket;
- User;
- Price.

А также: перечисления **TicketStatus** и **TicketType**, чтобы контролировать тип значения.

Помимо этих классов были созданы **ApiSite** (имитирует поведение API), **Barcode** (единое место для генерации barcode для всей системы), **EventMapper** (слой между БД и объектами в 
оперативной памяти).

# Инструкция как развернуть проект в Docker на ubuntu

**Предварительные условия**: установлен Docker и Docker Compose последних версий.

1. Создать папку для проекта и перейти в нее:
```bash
mkdir app && cd app
```
2. Создать папки:
```bash
mkdir nginx src dockerfiles env
```
3. Создать Dockerfile для php и composer:
```bash
touch composer.Dockerfile php.Dockerfiles
```
4. Заполнить composer.Dockerfile содержимым:
```dockerfile
FROM composer:latest

WORKDIR /var/www/laravel

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
```
5. Заполнить php.Dockerfile содержимым:
```dockerfile
FROM php:8.2-fpm-alpine

WORKDIR /var/www/laravel

RUN docker-php-ext-install pdo pdo_mysql
```
6. Перейти в папку env и создать файл mysql.env:
```bash
touch mysql.env
```
7. Заполнить mysql.env содержимым, вводя собственные значения:
```env
MYSQL_DATABASE=laravel_db
MYSQL_USER=laravel
MYSQL_PASSWORD=password
MYSQL_ROOT_PASSWORD=password
```
8. Перейти в папку nginx и создать файл nginx.conf:
```bash
touch nginx.conf
```
9. Заполнить nginx.cong содержимым:
```conf
server {
    listen 80;
    index index.php index.html;
    server_name localhost;
    root /var/www/laravel/public;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
```
10. Внутри папки app (папки проекта) создать docker-compose.yml:
```bash
touch docker-compose.yml
```
11. Заполнить docker-compose.yml содержимым:
```docker-compose
version: "3.8"

services:
  nginx:
    image: "nginx:stable-alpine"
    ports:
      - "8000:80"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/conf.d/default.conf:ro
      - ./src:/var/www/laravel
    depends_on:
      - mysql
      - php
  php:
    build:
      context: dockerfiles
      dockerfile: php.Dockerfile
    volumes:
      - ./src:/var/www/laravel
  mysql:
    image: mysql:8.0
    ports:
      - "3316:3306"
    env_file:
      - env/mysql.env
    volumes:
      - db_data:/var/lib/mysql

  composer:
    build:
      context: dockerfiles
      dockerfile: composer.Dockerfile
    volumes:
      - ./src:/var/www/laravel
  artisan:
    build:
      context: dockerfiles
      dockerfile: php.Dockerfile
    volumes:
      - ./src:/var/www/laravel
    entrypoint: ["php", "/var/www/laravel/artisan"]
  npm:
    build:
      context: dockerfiles
      dockerfile: npm.Dockerfile
    tty: true
    ports:
      - "5173:5173"
    volumes:
      - ./src:/var/www/laravel
volumes:
  db_data:
```
12. Находясь в папке app запустить команду ```sudo docker compose up -d```;
13. Запустить команду ```sudo docker compose run composer create-project laravel/laravel:^10.0 .```;
14. Перейти в папку app/src, выполнить команду ```git clone https://github.com/AngelusCat/ticket.git``` и скопировать файлы из получившейся папки непосредственно в app/src c помощью команды cp (например: ```sudo cp . ../src -r```, здесь рабочая папка - папка, в которую клонировался проект;
15. В папке src открыть через консоль файл .env и изменить некоторые настройки:
```env
APP_URL=http://localhost:8000
DB_HOST=mysql
DB_DATABASE, DB_USERNAME, DB_PASSWORD вставить такие же значения, как в env/mysql.env
```
16. Запустить следующие команды:
```bash
sudo docker compose run composer install
sudo docker exec -ti app-php-1 sh (затем в терминале контейнера ввести chmod -R 777 /var/www/laravel/storage и выйти из терминала с помощью exit)
sudo docker compose run artisan key:generate
sudo docker compose run artisan migrate
```
17. Перейти по ссылке http://localhost:8000/t, чтобы инициализировать БД изначальными значениями;
18. Перейти в по ссылке http://localhost:8000, чтобы "купить билеты".
