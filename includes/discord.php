<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/functions.php';

function discord_notify_register(string $username, string $email, string $ip): void
{
    $enabled = (bool) setting('discord_webhook_enabled', DISCORD_WEBHOOK_ENABLED);
    $url = (string) setting('discord_webhook_url', DISCORD_WEBHOOK_URL);
    if (!$enabled || $url === '' || str_contains($url, 'PEGAR_AQUI')) return;

    $safeEmail = DISCORD_HIDE_EMAIL ? preg_replace('/(^.).*(@.*$)/', '$1***$2', $email) : $email;
    $safeIp = DISCORD_HIDE_IP ? preg_replace('/\d+$/', 'xxx', $ip) : $ip;
    $payload = [
        'username' => APP_NAME . ' Registro',
        'embeds' => [[
            'title' => '⚔️ Nueva cuenta registrada',
            'color' => 15844367,
            'fields' => [
                ['name' => '👤 Usuario', 'value' => $username, 'inline' => true],
                ['name' => '📧 Email', 'value' => $safeEmail, 'inline' => true],
                ['name' => '🌎 IP', 'value' => $safeIp, 'inline' => true],
                ['name' => '🏰 Servidor', 'value' => APP_NAME, 'inline' => true],
                ['name' => '🕒 Fecha', 'value' => date('Y-m-d H:i:s'), 'inline' => true],
            ],
            'footer' => ['text' => 'MU Register Enterprise Pro']
        ]]
    ];
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_POST=>true, CURLOPT_HTTPHEADER=>['Content-Type: application/json'], CURLOPT_POSTFIELDS=>json_encode($payload), CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>8]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) write_log('discord.log', 'Error webhook', ['error'=>$err]);
    else write_log('discord.log', 'Webhook enviado', ['user'=>$username, 'response'=>$res]);
}
