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


// Підключення до бази даних
if (!isset($conn)) {
    require_once 'setup_db_1.php';
}

// Створюємо підключення до бази даних
try {
    /** @var array $config */
    $database = new Database($config);
    $conn = $database->getConnection();
    $table = $database->getTableName();

    // Отримуємо дані з бази
    $sql = "SELECT date, time_period, weight FROM {$table} WHERE user_id = 1 ORDER BY date DESC";
    $stmt = $conn->query($sql);

} catch (PDOException $e) {
    die(__FILE__ . ' +' . __LINE__ . " От, халепа, помилка підключення до бази даних: " . $e->getMessage());
} catch (Exception $e) {
    die(__FILE__ . ' +' . __LINE__ . " От, халепа, сталась інша помилка: " . $e->getMessage());
}

$n = 0;

?>

    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Щоденник показників вимірювання ваги | записи щоденника</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    </head>
    <body>
    <div class="container">
        <h1>Щоденник показників вимірювання ваги</h1>
        <table>
            <caption>Дані записів щоденника показників ваги</caption>
            <thead>
            <tr>
                <th>Дата</th>
                <th>Час</th>
                <th>Вага</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['time_period']; ?></td>
                    <td><?php echo $row['weight']; ?></td>
                </tr>
            <?php $n ++; ?>
            <?php endwhile; ?>
            </tbody>
        </table>
        <p>Додати ще один запис через <a href='add_data.php'>форму додавання даних</a>.</p>
        <?php if ($n > 0) { ?><p>Вивантаження введених даних (<?php echo $n; ?> записів). <a href="export_csv.php" target="_blank">Експортувати дані в CSV</a>.</p><?php } ?>
    </div>

    </body>
    </html>

<?php
$conn = null; // Закриваємо підключення
