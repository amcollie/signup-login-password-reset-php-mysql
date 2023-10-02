<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;
use mysqli;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->load();

class Database
{
    private string $host; 
    private string $username; 
    private string $password; 
    private string $database;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->database = $_ENV['DB_NAME'];
    }

    public function connect(): mysqli
    {
        $mysqli = new mysqli(
            hostname: $this->host, 
            username: $this->username, 
            password: $this->password, 
            database: $this->database
        );

        if ($mysqli->connect_error) {
            die("Connection failed: ". $mysqli->connect_error);
        }

        return $mysqli;
    }
}