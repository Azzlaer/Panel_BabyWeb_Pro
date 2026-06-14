<?php
require_once __DIR__ . '/paths.php';

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }

function client_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = explode(',', $_SERVER[$key])[0];
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

function redirect_with(string $type, string $message): void
{
    header('Location: index.php?' . $type . '=' . urlencode($message));
    exit;
}

function ensure_storage(): void
{
    foreach ([STORAGE_PATH, LOG_PATH, DATA_PATH, BLOCK_PATH] as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0775, true);
    }
}

function write_log(string $file, string $message, array $context = []): void
{
    ensure_storage();
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if ($context) $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
    file_put_contents(LOG_PATH . '/' . $file, $line . PHP_EOL, FILE_APPEND);
}

function read_log_lines(string $file, int $limit = 200): array
{
    $path = LOG_PATH . '/' . $file;
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    return array_slice($lines, -$limit);
}

function json_file_read(string $file, array $default = []): array
{
    $path = DATA_PATH . '/' . $file;
    if (!file_exists($path)) return $default;
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : $default;
}

function json_file_write(string $file, array $data): bool
{
    ensure_storage();
    return file_put_contents(DATA_PATH . '/' . $file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}
