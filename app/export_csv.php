<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

// Define a constant to be used for allowing direct access
define('ALLOW_DIRECT_ACCESS', true);

require_once 'config.php';
require_once 'Database.php';

try {
    /** @var array $config */
    $database = new Database($config);
    $conn = $database->getConnection();
    $table = $database->getTableName();

    // Отримуємо дані з таблиці
    $sql = "SELECT date, time_period, weight FROM {$table} WHERE user_id = 1 ORDER BY date DESC";
    $stmt = $conn->query($sql);

    // Встановлюємо заголовки для експорту у CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $table . '.csv');

    // Відкриваємо потік для запису даних у CSV
    $output = fopen('php://output', 'w');

    // Записуємо заголовки колонок у CSV
    fputcsv($output, ['Дата', 'Час дня', 'Вага']);

    // Записуємо дані в CSV
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    // Закриваємо потік
    fclose($output);
    exit;

} catch (PDOException $e) {
    die(__FILE__ . ' +' . __LINE__ . " От халепа, сталася помилка підключення до бази даних: " . $e->getMessage());
} catch (Exception $e) {
    die(__FILE__ . ' +' . __LINE__ . " От халепа, сталася помилка: " . $e->getMessage());
}
