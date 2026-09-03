<?php

namespace App\Database;

use PDO;
use Throwable;

/**
 * Applies pending .sql files from a migrations directory, tracked in a `migrations` table.
 */
final class MigrationRunner
{
    /**
     * @param PDO $pdo Open connection to the target database.
     * @param string $migrationsPath Absolute path to the directory of .sql migration files.
     */
    public function __construct(private readonly PDO $pdo, private readonly string $migrationsPath)
    {
    }

    /**
     * @return string[] filenames applied during this run
     * @throws Throwable
     */
    public function run(): array
    {
        $this->ensureMigrationsTable();
        $applied = $this->appliedMigrations();
        $newlyApplied = [];

        foreach ($this->migrationFiles() as $file) {
            $name = basename($file);
            if (in_array($name, $applied, true)) {
                continue;
            }

            $sql = file_get_contents($file);
            $this->pdo->beginTransaction();
            try {
                $this->pdo->exec($sql);
                $stmt = $this->pdo->prepare(
                    'INSERT INTO migrations (migration, applied_at) VALUES (:migration, NOW())'
                );
                $stmt->execute(['migration' => $name]);
                // CREATE TABLE issues an implicit commit in MySQL/MariaDB, so the
                // transaction may already be over by the time we get here.
                if ($this->pdo->inTransaction()) {
                    $this->pdo->commit();
                }
                $newlyApplied[] = $name;
            } catch (Throwable $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $e;
            }
        }

        return $newlyApplied;
    }

    private function ensureMigrationsTable(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                applied_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    /** @return string[] */
    private function appliedMigrations(): array
    {
        return $this->pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
    }

    /** @return string[] */
    private function migrationFiles(): array
    {
        $files = glob(rtrim($this->migrationsPath, '/') . '/*.sql');
        sort($files);

        return $files;
    }
}
