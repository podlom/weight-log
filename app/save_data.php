<?php

declare(strict_types=1);


/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */

session_start();

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not a POST request, block access
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access to this file is not allowed.');
} else {
    // Define a constant to be used for allowing direct access
    define('ALLOW_DIRECT_ACCESS', true);
}

require_once 'config.php';
require_once 'Database.php';


// Initialize an array to hold error messages
$errors = [];

// Валідуємо та отримуємо дані з форми
// Validate the date (required and valid date format)
if (empty($_POST['date'])) {
    $errors[] = 'Дата обов’язкова.';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
    $errors[] = 'Невірний формат дати. Використовуйте формат РРРР-ММ-ДД.';
} else {
    $date = $_POST['date'];
}

// Validate the time period (required and valid format HH:MM:SS)
if (empty($_POST['time_period'])) {
    $errors[] = 'Часовий проміжок обов’язковий.';
} elseif (!preg_match('/^(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])$/', $_POST['time_period'])) {
    $errors[] = 'Невірний формат часу. Використовуйте формат ГГ:ХХ:СС.';
} else {
    $time_period = $_POST['time_period'];
}

// Перевіряємо, чи встановлено поле 'weight' і чи не є воно порожнім
if (!isset($_POST['weight']) || $_POST['weight'] === '') {
    $errors[] = 'Поле ваги є обов`язковим.';
} else {
    $weight = $_POST['weight'];

    // Перевіряємо, чи це дійсне число
    if (!filter_var($weight, FILTER_VALIDATE_FLOAT)) {
        $errors[] = 'Вага має бути числом з плаваючою комою.';
    } elseif ($weight <= 0) {
        // Перевіряємо, чи вага є позитивною
        $errors[] = 'Вага має бути більше нуля.';
    }
}

// Check if there are any errors
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: add_data.php');  // Замініть на ім'я вашої сторінки з формою
    exit;
}

try {
    /** @var array $config */
    $database = new Database($config);
    $conn = $database->getConnection();
    $database->createTable();
    $table = $database->getTableName();

    // Збереження даних у базу
    $stmt = $conn->prepare("INSERT INTO {$table} (user_id, date, time_period, weight) VALUES (?, ?, ?, ?)");
    $user_id = 1; // Якщо є авторизація, можна додати унікального користувача
    $stmt->execute([$user_id, $date, $time_period, $weight]);

    // TODO: перевірити чи треба це тут ?
    if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
        // Очищаємо помилки після успішного запису
        unset($_SESSION['form_errors']);
    }

    // Переадресація після збереження
    header("Location: index.php");
    exit;

} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
} catch (Exception $e) {
    die("Помилка: " . $e->getMessage());
}
