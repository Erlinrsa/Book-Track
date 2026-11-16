<?php
/**
 * Lightweight Laravel Compatible Core Engine
 * Sprint 07 (Week 9) - Laravel + CRUD + Database
 * 
 * Provides native implementations of:
 * - Illuminate\Database\Eloquent\Model
 * - Illuminate\Support\Facades\Schema
 * - Illuminate\Support\Facades\Route
 * - Illuminate\Database\Schema\Blueprint
 * - Illuminate\Database\Migrations\Migration
 */

namespace Illuminate\Support\Facades {
    class Route {
        public static array $routes = [];

        public static function get(string $uri, $action): void {
            self::$routes['GET'][$uri] = $action;
        }

        public static function post(string $uri, $action): void {
            self::$routes['POST'][$uri] = $action;
        }

        public static function put(string $uri, $action): void {
            self::$routes['PUT'][$uri] = $action;
        }

        public static function delete(string $uri, $action): void {
            self::$routes['DELETE'][$uri] = $action;
        }

        public static function dispatch(string $method, string $uri) {
            $parsedUri = parse_url($uri, PHP_URL_PATH) ?: '/';
            // Normalize trailing slashes except for root
            if (strlen($parsedUri) > 1) {
                $parsedUri = rtrim($parsedUri, '/');
            }

            // Check exact match
            if (isset(self::$routes[$method][$parsedUri])) {
                $action = self::$routes[$method][$parsedUri];
                return is_callable($action) ? $action() : $action;
            }

            // Check parameter patterns e.g. /programs/{id}/edit
            if (isset(self::$routes[$method])) {
                foreach (self::$routes[$method] as $routePattern => $action) {
                    $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
                    $regex = '#^' . $regex . '$#';
                    if (preg_match($regex, $parsedUri, $matches)) {
                        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                        return is_callable($action) ? call_user_func_array($action, array_values($params)) : $action;
                    }
                }
            }

            // Fallback for default root
            if ($parsedUri === '/' || $parsedUri === '/index.php') {
                if (isset(self::$routes['GET']['/programs'])) {
                    $action = self::$routes['GET']['/programs'];
                    return is_callable($action) ? $action() : $action;
                }
            }

            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Route [$method $parsedUri] tidak ditemukan.</p>";
            exit;
        }
    }

    class Schema {
        public static function create(string $table, callable $callback): void {
            $bp = new \Illuminate\Database\Schema\Blueprint($table);
            $callback($bp);
            \App\Core\Database::createTable($table, $bp->getColumns());
        }

        public static function dropIfExists(string $table): void {
            \App\Core\Database::dropTable($table);
        }
    }
}

namespace Illuminate\Database\Schema {
    class Blueprint {
        protected string $table;
        protected array $columns = [];

        public function __construct(string $table) {
            $this->table = $table;
        }

        public function id(): self {
            $this->columns['id'] = 'id';
            return $this;
        }

        public function string(string $col): self {
            $this->columns[$col] = 'string';
            return $this;
        }

        public function text(string $col): self {
            $this->columns[$col] = 'text';
            return $this;
        }

        public function integer(string $col): self {
            $this->columns[$col] = 'integer';
            return $this;
        }

        public function float(string $col): self {
            $this->columns[$col] = 'float';
            return $this;
        }

        public function default($val): self {
            return $this;
        }

        public function nullable(): self {
            return $this;
        }

        public function timestamps(): self {
            $this->columns['created_at'] = 'timestamp';
            $this->columns['updated_at'] = 'timestamp';
            return $this;
        }

        public function getColumns(): array {
            return $this->columns;
        }
    }
}

namespace Illuminate\Database\Migrations {
    abstract class Migration {
        abstract public function up(): void;
        abstract public function down(): void;
    }
}

namespace Illuminate\Database\Eloquent {
    abstract class Model implements \ArrayAccess {
        protected $table;
        protected $fillable = [];
        protected $attributes = [];

        public function __construct(array $attributes = []) {
            $this->fill($attributes);
        }

        public function fill(array $attributes): self {
            foreach ($attributes as $key => $value) {
                if (empty($this->fillable) || in_array($key, $this->fillable)) {
                    $this->attributes[$key] = $value;
                }
            }
            return $this;
        }

        public function getTable(): string {
            if (!empty($this->table)) {
                return $this->table;
            }
            $shortName = (new \ReflectionClass($this))->getShortName();
            return strtolower($shortName) . 's';
        }

        public function __get($key) {
            return $this->attributes[$key] ?? null;
        }

        public function __set($key, $value) {
            $this->attributes[$key] = $value;
        }

        public function __isset($key) {
            return isset($this->attributes[$key]);
        }

        public function offsetExists($offset): bool {
            return isset($this->attributes[$offset]);
        }

        public function offsetGet($offset): mixed {
            return $this->attributes[$offset] ?? null;
        }

        public function offsetSet($offset, $value): void {
            $this->attributes[$offset] = $value;
        }

        public function offsetUnset($offset): void {
            unset($this->attributes[$offset]);
        }

        public function toArray(): array {
            return $this->attributes;
        }

        // Eloquent ORM: Model::all()
        public static function all(): array {
            $instance = new static();
            $table = $instance->getTable();
            $records = \App\Core\Database::getTableData($table);

            $models = [];
            foreach ($records as $row) {
                $m = new static();
                $m->attributes = $row;
                $models[] = $m;
            }
            return $models;
        }

        // Eloquent ORM: Model::find($id)
        public static function find($id): ?static {
            $instance = new static();
            $table = $instance->getTable();
            $records = \App\Core\Database::getTableData($table);

            foreach ($records as $row) {
                if (isset($row['id']) && (string)$row['id'] === (string)$id) {
                    $m = new static();
                    $m->attributes = $row;
                    return $m;
                }
            }
            return null;
        }

        // Eloquent ORM: Model::create([...])
        public static function create(array $attributes): static {
            $model = new static($attributes);
            $model->save();
            return $model;
        }

        // Eloquent ORM: $model->update([...])
        public function update(array $attributes = []): bool {
            $this->fill($attributes);
            return $this->save();
        }

        // Eloquent ORM: $model->save()
        public function save(): bool {
            $table = $this->getTable();
            $now = date('Y-m-d H:i:s');

            if (!isset($this->attributes['created_at'])) {
                $this->attributes['created_at'] = $now;
            }
            $this->attributes['updated_at'] = $now;

            $records = \App\Core\Database::getTableData($table);

            if (!isset($this->attributes['id']) || empty($this->attributes['id'])) {
                $maxId = 0;
                foreach ($records as $r) {
                    if (isset($r['id']) && (int)$r['id'] > $maxId) {
                        $maxId = (int)$r['id'];
                    }
                }
                $this->attributes['id'] = $maxId + 1;
                $records[] = $this->attributes;
            } else {
                foreach ($records as $idx => $r) {
                    if (isset($r['id']) && (string)$r['id'] === (string)$this->attributes['id']) {
                        $records[$idx] = array_merge($r, $this->attributes);
                        break;
                    }
                }
            }

            \App\Core\Database::saveTableData($table, $records);
            return true;
        }

        // Eloquent ORM: $model->delete()
        public function delete(): bool {
            if (!isset($this->attributes['id'])) return false;

            $table = $this->getTable();
            $records = \App\Core\Database::getTableData($table);
            $newRecords = [];

            foreach ($records as $r) {
                if (isset($r['id']) && (string)$r['id'] === (string)$this->attributes['id']) {
                    continue; // Skip/delete
                }
                $newRecords[] = $r;
            }

            \App\Core\Database::saveTableData($table, $newRecords);
            return true;
        }
    }
}

namespace App\Core {
    class Database {
        protected static string $dbFile = __DIR__ . '/../../database/database.sqlite';
        protected static string $jsonFile = __DIR__ . '/../../database/database.json';

        public static function getStorageFile(): string {
            $dir = dirname(self::$jsonFile);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            return self::$jsonFile;
        }

        public static function getAllData(): array {
            $file = self::getStorageFile();
            if (!file_exists($file)) {
                return [];
            }
            $content = file_get_contents($file);
            return json_decode($content, true) ?: [];
        }

        public static function saveAllData(array $data): void {
            $file = self::getStorageFile();
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        public static function getTableData(string $table): array {
            $data = self::getAllData();
            return $data[$table] ?? [];
        }

        public static function saveTableData(string $table, array $rows): void {
            $data = self::getAllData();
            $data[$table] = array_values($rows);
            self::saveAllData($data);
        }

        public static function createTable(string $table, array $columns): void {
            $data = self::getAllData();
            if (!isset($data[$table])) {
                $data[$table] = [];
                self::saveAllData($data);
            }
        }

        public static function dropTable(string $table): void {
            $data = self::getAllData();
            unset($data[$table]);
            self::saveAllData($data);
        }
    }
}

namespace {
    // Global Helper functions matching Laravel standard API
    if (!function_exists('env')) {
        function env(string $key, $default = null) {
            $val = getenv($key);
            if ($val !== false) return $val;
            static $envVars = null;
            if ($envVars === null) {
                $envVars = [];
                $envFile = __DIR__ . '/../../.env';
                if (file_exists($envFile)) {
                    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $l) {
                        $l = trim($l);
                        if (str_starts_with($l, '#') || !str_contains($l, '=')) continue;
                        [$k, $v] = explode('=', $l, 2);
                        $envVars[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
                    }
                }
            }
            return $envVars[$key] ?? $default;
        }
    }

    if (!function_exists('database_path')) {
        function database_path(string $path = ''): string {
            return __DIR__ . '/../../database/' . ltrim($path, '/');
        }
    }

    if (!function_exists('request')) {
        function request(?string $key = null, $default = null) {
            $data = array_merge($_GET, $_POST);
            if ($key === null) return $data;
            return $data[$key] ?? $default;
        }
    }

    if (!function_exists('redirect')) {
        function redirect(string $url) {
            header("Location: $url");
            exit;
        }
    }

    if (!function_exists('view')) {
        function view(string $viewName, array $data = []) {
            extract($data);
            $viewPath = str_replace('.', '/', $viewName);
            $fullPath = __DIR__ . '/../../resources/views/' . $viewPath . '.blade.php';
            if (!file_exists($fullPath)) {
                $fullPath = __DIR__ . '/../../resources/views/' . $viewPath . '.php';
            }

            if (!file_exists($fullPath)) {
                echo "<h1>View [$viewName] not found at $fullPath</h1>";
                return;
            }

            $content = file_get_contents($fullPath);

            // Simple Blade compiler
            // Handle @extends('layouts.app')
            $layout = null;
            if (preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $content, $m)) {
                $layout = $m[1];
                $content = str_replace($m[0], '', $content);
            }

            // Extract @section('content') ... @endsection
            $sectionContent = '';
            if (preg_match('/@section\([\'"]content[\'"]\)(.*?)@endsection/s', $content, $sm)) {
                $sectionContent = $sm[1];
            } else {
                $sectionContent = $content;
            }

            // Compile Blade directives in section
            $compiled = $sectionContent;
            $compiled = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars((string)($1), ENT_QUOTES, "UTF-8") ?>', $compiled);
            $compiled = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?= $1 ?>', $compiled);
            $compiled = preg_replace('/@if\s*\((.+)\)/', '<?php if($1): ?>', $compiled);
            $compiled = preg_replace('/@elseif\s*\((.+)\)/', '<?php elseif($1): ?>', $compiled);
            $compiled = preg_replace('/@else/', '<?php else: ?>', $compiled);
            $compiled = preg_replace('/@endif/', '<?php endif; ?>', $compiled);
            $compiled = preg_replace('/@foreach\s*\((.+)\)/', '<?php foreach($1): ?>', $compiled);
            $compiled = preg_replace('/@endforeach/', '<?php endforeach; ?>', $compiled);
            $compiled = preg_replace('/@empty/', '<?php endforeach; else: ?>', $compiled);
            $compiled = preg_replace('/@csrf/', '<input type="hidden" name="_token" value="csrf_token_dummy">', $compiled);

            // Render section in memory
            ob_start();
            eval('?>' . $compiled);
            $renderedSection = ob_get_clean();

            if ($layout) {
                $layoutPath = str_replace('.', '/', $layout);
                $layoutFullPath = __DIR__ . '/../../resources/views/' . $layoutPath . '.blade.php';
                $layoutContent = file_get_contents($layoutFullPath);

                $layoutCompiled = str_replace("@yield('content')", $renderedSection, $layoutContent);
                $layoutCompiled = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars((string)($1), ENT_QUOTES, "UTF-8") ?>', $layoutCompiled);

                ob_start();
                eval('?>' . $layoutCompiled);
                echo ob_get_clean();
            } else {
                echo $renderedSection;
            }
        }
    }

    if (!function_exists('response')) {
        function response() {
            return new class {
                public function json(array $data) {
                    header('Content-Type: application/json');
                    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    exit;
                }
            };
        }
    }
}
