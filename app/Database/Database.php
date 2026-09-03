<?php

namespace App\Database;

use PDO;

/**
 * Provides a single memoized PDO connection for the current request.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * @param array{driver: string, host: string, port: string, database: string, username: string, password: string} $config
     */
    public static function connection(array $config): PDO
    {
        if (self::$connection !== null) {
           return self::$connection;
        }

         $driver = $config['driver'] ?? 'mysql';

            $dsn = match ($driver) {
                'sqlite' => 'sqlite:' . $config['database'],
                'pgsql' => sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    $config['host'],
                    $config['port'],
                    $config['database']
                ),
                default => sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $config['host'],
                    $config['port'],
                    $config['database']
                ),
            };

            $username = $driver === 'sqlite' ? null : $config['username'];
            $password = $driver === 'sqlite' ? null : $config['password'];

            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

        return self::$connection;
    }
}
