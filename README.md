# Тестовое задание: блог на PHP + Smarty + MySQL

Простой блог без фреймворков: главная страница с категориями и последними
постами, страница категории с сортировкой/пагинацией, страница поста с
похожими статьями.

## Стек

- PHP 8.1+ (front controller + самописный Router, без фреймворка)
- Smarty 5 (шаблонизатор)
- MySQL (PDO, prepared statements)
- SCSS (компилируется в `public/css/main.css`)
- PHPUnit (юнит-тесты чистой логики)

## Запуск под OSPanel / любым Apache+PHP+MySQL

1. Документ-рут виртуального хоста должен указывать на `public/`.
2. `composer install`
3. `cp .env.example .env` и указать реальные `DB_*` данные.
4. Создать базу: `CREATE DATABASE blog CHARACTER SET utf8mb4;`
5. `php bin/migrate.php` — накатить схему.
6. `php bin/seed.php` — наполнить тестовыми категориями/статьями.
7. Открыть домен в браузере.

## Запуск через Docker

```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/migrate.php
docker compose exec php php bin/seed.php
```

Сайт будет доступен на `http://localhost:8080`.

## Сборка стилей

Компилируется вручную Dart Sass, отдельного npm/composer-скрипта нет:

```bash
sass resources/scss/main.scss public/css/main.css --no-source-map
```

Скомпилированный `public/css/main.css` закоммичен в репозиторий, чтобы сайт
работал без установленного `sass`. Пересобирать нужно только после правок в
`resources/scss/`.

## Тесты

```bash
composer test
```

Юнит-тестами покрыта только чистая логика без обращения к БД (роутинг,
генерация slug, пагинация, ранжирование похожих статей)

## Пересидить данные

`php bin/seed.php` идемпотентен — очищает таблицы и наполняет заново.
