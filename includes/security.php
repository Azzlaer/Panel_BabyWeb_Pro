<?php
require_once __DIR__ . '/functions.php';

function block_file(): string { return BLOCK_PATH . '/ip_blocks.json'; }
function attempts_file(): string { return BLOCK_PATH . '/ip_attempts.json'; }
function registrations_file(): string { return BLOCK_PATH . '/ip_registrations.json'; }

function get_blocks(): array
{
    if (!file_exists(block_file())) return [];
    $data = json_decode(file_get_contents(block_file()), true);
    return is_array($data) ? $data : [];
}

function save_blocks(array $blocks): void
{
    ensure_storage();
    file_put_contents(block_file(), json_encode($blocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function is_ip_blocked(string $ip): bool
{
    if (!IP_BLOCK_ENABLED) return false;
    $blocks = get_blocks();
    if (!isset($blocks[$ip])) return false;
    if (!empty($blocks[$ip]['until']) && strtotime($blocks[$ip]['until']) < time()) {
        unset($blocks[$ip]);
        save_blocks($blocks);
        return false;
    }
    return true;
}

function block_ip(string $ip, string $reason = 'Abuso de registro'): void
{
    $blocks = get_blocks();
    $blocks[$ip] = [
        'ip' => $ip,
        'reason' => $reason,
        'created_at' => date('Y-m-d H:i:s'),
        'until' => date('Y-m-d H:i:s', time() + BLOCK_MINUTES * 60),
    ];
    save_blocks($blocks);
    write_log('security.log', 'IP bloqueada', ['ip' => $ip, 'reason' => $reason]);
}

function unblock_ip(string $ip): void
{
    $blocks = get_blocks();
    unset($blocks[$ip]);
    save_blocks($blocks);
    write_log('security.log', 'IP desbloqueada desde admin', ['ip' => $ip]);
}

function failed_attempt(string $ip, string $reason): void
{
    ensure_storage();
    $data = file_exists(attempts_file()) ? json_decode(file_get_contents(attempts_file()), true) : [];
    if (!is_array($data)) $data = [];
    $now = time();
    if (!isset($data[$ip])) $data[$ip] = ['count' => 0, 'first' => $now];
    if (($now - (int)$data[$ip]['first']) > 3600) $data[$ip] = ['count' => 0, 'first' => $now];
    $data[$ip]['count']++;
    $data[$ip]['last_reason'] = $reason;
    $data[$ip]['last_at'] = date('Y-m-d H:i:s');
    file_put_contents(attempts_file(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    write_log('security.log', 'Intento fallido', ['ip' => $ip, 'reason' => $reason, 'count' => $data[$ip]['count']]);
    if ($data[$ip]['count'] >= MAX_FAILED_ATTEMPTS) block_ip($ip, 'Demasiados intentos fallidos');
}

function can_register_by_ip(string $ip): array
{
    ensure_storage();
    $path = registrations_file();
    $data = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    if (!is_array($data)) $data = [];
    $today = date('Y-m-d');
    if (!isset($data[$ip]) || ($data[$ip]['date'] ?? '') !== $today) {
        return [true, 'OK'];
    }
    $last = strtotime($data[$ip]['last_at'] ?? '1970-01-01');
    if (time() - $last < REGISTER_COOLDOWN_SECONDS) return [false, 'Debes esperar unos segundos antes de registrar otra cuenta.'];
    if (($data[$ip]['count'] ?? 0) >= REGISTER_MAX_PER_IP_DAY) return [false, 'Has alcanzado el máximo de registros diarios por IP.'];
    return [true, 'OK'];
}

function mark_registration_ip(string $ip): void
{
    ensure_storage();
    $path = registrations_file();
    $data = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    if (!is_array($data)) $data = [];
    $today = date('Y-m-d');
    if (!isset($data[$ip]) || ($data[$ip]['date'] ?? '') !== $today) {
        $data[$ip] = ['date' => $today, 'count' => 0];
    }
    $data[$ip]['count']++;
    $data[$ip]['last_at'] = date('Y-m-d H:i:s');
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
