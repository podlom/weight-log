<?php

declare(strict_types=1);

session_start();

  /**
   * @author Taras Shkodenko <podlom@gmail.com>
   * @copyright Shkodenko V. Taras 2024
   */
    date_default_timezone_set('Europe/Kyiv');

    // Define a constant to be used for allowing direct access
    define('ALLOW_DIRECT_ACCESS', true);

    $currentDate = date("Y-m-d");
    $currentTime = date("H:i:s");

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Щоденник показників ваги | додати новий запис</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1><a href="/" title="Щоденник показників вимірювання тиску та пульсу | записи щоденника">Щоденник ваги</a> - додати новий запис</h1>
        <?php

            // Перевіряємо, чи є збережені помилки у сесії
            if (!empty($_SESSION['form_errors'])) {
                foreach ($_SESSION['form_errors'] as $error) {
                    echo '<p class="text-danger">' . htmlspecialchars($error) . '</p>';
                }

                // Очищаємо помилки після їх відображення
                unset($_SESSION['form_errors']);
            }

        ?>
        <form method="POST" action="save_data.php">
            <label for="date">Дата:</label>
            <input id="date" type="date" name="date" value="<?= $currentDate ?>" required><br>

            <label for="time_period">Поточний час: <?= $currentTime ?></label>
            <input id="time_period" name="time_period" type="hidden" value="<?= $currentTime ?>" required><br>

            <label for="weight">Вага (кг):</label>
            <input type="number" id="weight" name="weight" step="0.01" min="0" required><br>

            <button type="submit">Зберегти</button>
        </form>
    </div>

</body>
</html>
