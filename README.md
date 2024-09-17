# weight-log - щоденник ваги

## Щоденник показників вимірювання ваги

Щоб почати роботу виконайте такі команди:

```bash
git clone git@github.com:podlom/weight-log.git .
```

```bash
cd app
```

## Конфігурація

```bash
cp -v .env_example.txt .env
cp -v config_example.php config.php
```

Оберіть тип бази даних опція DB_DRIVER може мати значення **sqlite** (за замовчуванням).
В такому випадку треба відповідним чином налаштувати параметр path в масиві файлу config.php
А також встановити значення обов'язкового параметру **DB_NAME**

```php
// Отримання змінних середовища
$driver = $_ENV['DB_DRIVER'] ?: 'mysql'; // або sqlite
$host = $_ENV['DB_HOST'] ?: 'localhost';
$dbname = $_ENV['DB_NAME'] ?: 'weight_log';
$user = $_ENV['DB_USER'] ?: 'root';
$password = $_ENV['DB_PASSWORD'] ?: '';
$charset = $_ENV['DB_CHARSET'] ?: 'utf8mb4';
$tableName = $_ENV['TABLE_NAME'] ?: 'weight_log';

$config = [
    'db' => [
        'driver' => $driver,
        'sqlite' => [
            'path' => __DIR__ . '/data/' . $dbname . '.db',
        ],
```

Якщо ви хочете використовувати MySQL, тоді створіть базу даних, користувача, а також налаштуйте параметри:

```dotenv
DB_DRIVER=mysql
DB_HOST=yourDbName
DB_NAME=pressure_pulse_log
DB_USER=your_db_user
DB_PASSWORD=your_mysql_password
```

Для запуску проєкту в Docker середовищі переконайтесь, що значення середовища бази даних з файлу app/docker-compose.yml відповідають вашим налаштуванням у файлі app/.env

```yaml
      - db
    environment:
      DB_DRIVER: ${DB_DRIVER:-sqlite} # За замовчуванням sqlite
      DB_HOST: yourDbName
      DB_NAME: pressure_pulse_log
      DB_USER: your_db_user
      DB_PASSWORD: your_mysql_password
```

## Білд та запуск у Docker

Запустіть команду:

```bash
docker-compose build --no-cache
```

для побудови Docker середовища.

І команду:

```bash
docker-compose up -d
```
для запуску додатка в середовищі Docker.

Коли процес запуску Docker успішно закінчиться відкрийте посилання: <http://localhost:8080> в браузері.

Щоб зупинити роботу Docker виконайте команду:

```bash
docker-compose down
```

### Подякувати автору
[![ko-fi](https://camo.githubusercontent.com/70e2ef5e0263b261f9a2a314bb1d6919d1d43292eed117fe8fc766a68c7d96ea/68747470733a2f2f6b6f2d66692e636f6d2f696d672f676974687562627574746f6e5f736d2e737667)](https://ko-fi.com/L3L5LJ3TB)