<?php
/**
 * =========================================================================
 * SPRINT 07 (WEEK 9) - LARAVEL + CRUD + DATABASE
 * Tema Project : BookTrack & PresidentKu Data Management
 * Sesuai Modul Pertemuan 9: Model, Eloquent ORM, Migration, CRUD Database
 * =========================================================================
 */

define('LARAVEL_START', microtime(true));

// 1. Load Core Engine & Models
require_once __DIR__ . '/app/Core/Engine.php';
require_once __DIR__ . '/app/Models/Program.php';
require_once __DIR__ . '/app/Models/Book.php';

// 2. Auto-initialize Database and Migrations if needed
if (!file_exists(__DIR__ . '/database/database.json')) {
    $migrationFiles = glob(__DIR__ . '/database/migrations/*.php');
    sort($migrationFiles);
    foreach ($migrationFiles as $file) {
        $migration = require $file;
        if (is_object($migration) && method_exists($migration, 'up')) {
            $migration->up();
        }
    }
    require_once __DIR__ . '/database/seeders/ProgramSeeder.php';
    require_once __DIR__ . '/database/seeders/DatabaseSeeder.php';
    (new \Database\Seeders\DatabaseSeeder())->run();
}

// 3. Load Routes
require_once __DIR__ . '/routes/web.php';

// 4. Dispatch Request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';

\Illuminate\Support\Facades\Route::dispatch($method, $uri);
