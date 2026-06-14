<?php
require_once __DIR__ . '/../config.php';

function captcha_generate(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < CAPTCHA_LENGTH; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
    $_SESSION[CAPTCHA_SESSION_KEY] = $code;
    return $code;
}

function captcha_verify(string $answer): bool
{
    if (!CAPTCHA_ENABLED) return true;
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $expected = $_SESSION[CAPTCHA_SESSION_KEY] ?? '';
    unset($_SESSION[CAPTCHA_SESSION_KEY]);
    return strtoupper(trim($answer)) === strtoupper((string)$expected);
}
