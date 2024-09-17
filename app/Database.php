<?php

declare(strict_types=1);

/**
 * @author Taras Shkodenko <podlom@gmail.com>
 * @copyright Shkodenko V. Taras 2024
 */


// Prevent direct access to this file
if (!defined('ALLOW_DIRECT_ACCESS')) {
    // You can redirect to an error page or show a 403 Forbidden message
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access to this file not allowed.');
}

// Check if $config array is set and it is not empty
if (!isset($config) || !is_array($config) || empty($config)) {
    header('HTTP/1.1 400 Bad Request');
    exit('No config.php file detected. See instructions in README.md');
}

class Database
{
    private array $config;

    private PDO $conn;

    private string $driver;

    private string $tableName;

    public function __construct(array $config)
    {
        /** @var array $this->config */
        $this->config = $config;

        $this->driver = $this->config['db']['driver'];

        try {
            if ($this->driver === 'sqlite') {
                $dbFile = $this->config['db']['sqlite']['path'];
                $this->conn = new PDO('sqlite:' . $dbFile);
            } elseif ($this->driver === 'mysql') {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    $this->config['db']['mysql']['host'],
                    $this->config['db']['mysql']['dbname'],
                    $this->config['db']['mysql']['charset']
                );
                $this->conn = new PDO($dsn, $this->config['db']['mysql']['user'], $this->config['db']['mysql']['password']);
            } else {
                throw new Exception("От халепа, спалася помилка: непідтримуваний драйвер бази даних: " . $this->driver);
            }

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->tableName = $this->config['db']['tableName'];
        } catch (PDOException $e) {
            die(__FILE__ . ' +' . __LINE__ . " От халепа, спалася помилка підключення до бази даних: " . $e->getMessage());
        } catch (Exception $e) {
            die(__FILE__ . ' +' . __LINE__ . " От халепа, спалася помилка: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function createTable(): void
    {
        /** @var array $config */
        $createTableSQL = "";

        if ($this->driver === 'sqlite') {
            $createTableSQL = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                date TEXT NOT NULL,
                time_period TEXT NOT NULL,
                weight REAL NOT NULL
            );";
        } elseif ($this->driver === 'mysql') {
            $createTableSQL = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                date DATE NOT NULL,
                time_period VARCHAR(50) NOT NULL,
                weight DECIMAL(5,2) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET={$this->config['db']['mysql']['charset']};";
        }

        $this->conn->exec($createTableSQL);
    }
}
