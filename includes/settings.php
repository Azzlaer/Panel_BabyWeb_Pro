<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/paths.php';

function default_settings(): array
{
    return [
        'site_mode' => SITE_MODE,
        'site_title' => SITE_TITLE,
        'site_description' => SITE_DESCRIPTION,
        'maintenance_mode' => MAINTENANCE_MODE,
        'maintenance_message' => MAINTENANCE_MESSAGE,
        'background_mode' => BACKGROUND_MODE,
        'background_image' => BACKGROUND_IMAGE,
        'background_video' => BACKGROUND_VIDEO,
        'background_youtube_id' => BACKGROUND_YOUTUBE_ID,
        'background_overlay_opacity' => BACKGROUND_OVERLAY_OPACITY,
        'discord_webhook_enabled' => DISCORD_WEBHOOK_ENABLED,
        'discord_webhook_url' => DISCORD_WEBHOOK_URL,
        'modules' => [
            'register' => MODULE_REGISTER_ENABLED,
            'ranking' => MODULE_RANKING_ENABLED,
            'online' => MODULE_ONLINE_ENABLED,
            'server_status' => MODULE_SERVER_STATUS_ENABLED,
            'downloads' => MODULE_DOWNLOADS_ENABLED,
            'news' => MODULE_NEWS_ENABLED,
            'events' => MODULE_EVENTS_ENABLED,
            'discord_widget' => MODULE_DISCORD_WIDGET_ENABLED,
        ],
        'downloads' => [
            'client_url' => DOWNLOAD_CLIENT_URL,
            'patch_url' => DOWNLOAD_PATCH_URL,
            'launcher_url' => DOWNLOAD_LAUNCHER_URL,
            'client_version' => CLIENT_VERSION,
            'client_size' => CLIENT_SIZE,
        ],
    ];
}

function settings_file(): string { return DATA_PATH . '/settings.json'; }

function load_settings(): array
{
    $defaults = default_settings();
    $file = settings_file();
    if (!file_exists($file)) return $defaults;
    $json = json_decode(file_get_contents($file), true);
    if (!is_array($json)) return $defaults;
    return array_replace_recursive($defaults, $json);
}

function save_settings(array $settings): bool
{
    if (!is_dir(DATA_PATH)) mkdir(DATA_PATH, 0775, true);
    return file_put_contents(settings_file(), json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function setting(string $key, $default = null)
{
    static $settings = null;
    if ($settings === null) $settings = load_settings();
    $parts = explode('.', $key);
    $value = $settings;
    foreach ($parts as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) return $default;
        $value = $value[$part];
    }
    return $value;
}

function module_enabled(string $name): bool
{
    return (bool) setting('modules.' . $name, false);
}
