# About
FilaConf is a Conference Management System. It's build on top of Laravel 11 and uses Filament [TALL Stack]. If you are familiar with Laravel, and Filament you should feel right at home.

## Installation
In terms of local development, you can use the following requirements:

- PHP 8.3 - with SQLite, GD, and other common extensions.
- Node.js 16 or more recent.

If you have these requirements, you can start by cloning the repository and installing the dependencies:

```bash
git clone https://github.com/arifhossen-dev/FilaConf.git

cd FilaConf
```

Next, install the dependencies using [Composer](https://getcomposer.org):

```bash
composer install
```

After that, set up your `.env` file:

```bash
cp .env.example .env

php artisan key:generate
```

Prepare your database and run the migrations:

```bash
touch database/database.sqlite

php artisan migrate --seed
```

Finally, start the development server:

```bash
php artisan serve
```
