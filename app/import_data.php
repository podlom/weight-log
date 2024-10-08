<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

// Define a constant to be used for allowing direct access
// define('ALLOW_DIRECT_ACCESS', true);

require_once __DIR__ . '/vendor/autoload.php'; // Якщо використовуєш Composer
require_once 'config.php';
require_once 'Database.php';


use Dotenv\Dotenv;


// Завантаження змінних середовища з .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();



try {
    /** @var array $config */
    $database = new Database($config);
    $conn = $database->getConnection();
    $table = $database->getTableName();
    // $database->createTable();

    // Шлях до файлу CSV
    $csvFile = __DIR__ . '/data/import_weight.csv';

    // Перевірка наявності файлу CSV
    if (!file_exists($csvFile)) {
        die(__FILE__ . ' +' . __LINE__ . ' Файл не знайдено: ' . $csvFile . PHP_EOL);
    }

    $n = 0;
    // Відкриваємо файл для читання
    if (($handle = fopen($csvFile, 'r')) !== false) {
        // Пропускаємо заголовок (перший рядок)
        fgetcsv($handle);

        // Підготовка SQL для вставки даних
        $stmt = $conn->prepare("INSERT INTO {$table} (user_id, date, time_period, weight) 
                            VALUES (:user_id, :date, :time_period, :weight)");

        // Вставляємо кожний рядок з CSV у базу даних
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // Дата і час дня
            $date = $data[0];
            $time_period = $data[1];
            $weight = (float)$data[2];

            // Вставляємо в базу даних
            $stmt->execute([
                ':user_id' => 1, // Тут ти можеш підставити актуального користувача
                ':date' => $date,
                ':time_period' => $time_period,
                ':weight' => $weight,
            ]);

            $n ++;
        }

        fclose($handle);
        echo __FILE__ . ' +' . __LINE__ . " Дані ({$n} рядків) були успішно імпортовані!" . PHP_EOL;
    } else {
        echo __FILE__ . ' +' . __LINE__ . " От халепа, сталася помилка відкриття файлу." . PHP_EOL;
    }
} catch (Exception $e) {
    die(__FILE__ . ' +' . __LINE__ . " От халепа, сталася помилка: " . $e->getMessage() . PHP_EOL);
}
