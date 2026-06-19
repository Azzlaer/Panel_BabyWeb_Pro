<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/content.php';

function module_ranking(): array
{
    try {
        $sql = "SELECT TOP " . intval(RANKING_LIMIT) . " " . RANKING_NAME_FIELD . " AS name, " . RANKING_LEVEL_FIELD . " AS level, " . RANKING_RESET_FIELD . " AS resets, " . RANKING_CLASS_FIELD . " AS class FROM " . RANKING_TABLE . " ORDER BY " . RANKING_RESET_FIELD . " DESC, " . RANKING_LEVEL_FIELD . " DESC";
        return db()->fetchAll($sql);
    } catch (Throwable $e) {
        return ['_error' => $e->getMessage()];
    }
}

function module_online_count()
{
    try {
        return (int) db()->scalar("SELECT COUNT(*) FROM " . ONLINE_TABLE . " WHERE " . ONLINE_STATUS_FIELD . " = ?", [ONLINE_STATUS_ON_VALUE]);
    } catch (Throwable $e) {
        return null;
    }
}

function module_server_status_items(): array
{
    $items = json_decode(SERVER_STATUS_ITEMS, true) ?: [];
    foreach ($items as &$item) {
        $fp = @fsockopen($item['host'], (int)$item['port'], $errno, $errstr, 0.8);
        $item['online'] = (bool)$fp;
        if ($fp) fclose($fp);
    }
    return $items;
}

function module_news(): array
{
    $news = json_file_read('news.json', [
        ['id'=>'welcome','enabled'=>true,'pinned'=>true,'title'=>'Apertura oficial','date'=>date('Y-m-d'),'tag'=>'Noticia','emoji'=>'📰','format'=>'bbcode','content'=>'[b]Bienvenido al continente de MU.[/b]\nCrea tu cuenta y prepárate para la aventura.']
    ]);
    return sorted_news($news);
}

function module_events(): array
{
    return json_file_read('events.json', [
        ['emoji'=>'🩸','name'=>'Blood Castle','time'=>'Cada 2 horas'],
        ['emoji'=>'🔥','name'=>'Devil Square','time'=>'Cada 3 horas'],
        ['emoji'=>'🏰','name'=>'Chaos Castle','time'=>'Cada 4 horas'],
        ['emoji'=>'🐉','name'=>'Golden Invasion','time'=>'22:00'],
    ]);
}

function layout_presets(): array
{
    return [
        'classic_center' => [
            'name' => 'Clásico centrado',
            'class' => 'layout-classic-center',
            'zones' => ['left'=>['server_status','ranking'], 'center'=>['register'], 'right'=>['online','events','downloads','news'], 'top'=>[], 'bottom'=>[]]
        ],
        'register_left' => [
            'name' => 'Registro izquierda',
            'class' => 'layout-register-left',
            'zones' => ['left'=>['register'], 'center'=>['news','ranking'], 'right'=>['server_status','online','events','downloads'], 'top'=>[], 'bottom'=>[]]
        ],
        'register_right' => [
            'name' => 'Registro derecha',
            'class' => 'layout-register-right',
            'zones' => ['left'=>['server_status','online','events','downloads'], 'center'=>['news','ranking'], 'right'=>['register'], 'top'=>[], 'bottom'=>[]]
        ],
        'hero_top' => [
            'name' => 'Registro arriba tipo portada',
            'class' => 'layout-hero-top',
            'zones' => ['top'=>['register'], 'left'=>['ranking','news'], 'center'=>['server_status','online'], 'right'=>['events','downloads'], 'bottom'=>[]]
        ],
        'magazine' => [
            'name' => 'Revista / Portal',
            'class' => 'layout-magazine',
            'zones' => ['top'=>['news'], 'left'=>['ranking'], 'center'=>['register'], 'right'=>['online','server_status'], 'bottom'=>['events','downloads']]
        ],
        'minimal_pro' => [
            'name' => 'Minimal Pro',
            'class' => 'layout-minimal-pro',
            'zones' => ['left'=>[], 'center'=>['register','news'], 'right'=>[], 'top'=>[], 'bottom'=>['server_status','online','events','downloads','ranking']]
        ],
    ];
}

function default_layout_zones(): array
{
    $presets = layout_presets();
    return $presets['classic_center']['zones'];
}

function active_layout_zones(): array
{
    $preset = setting('layout_preset', 'classic_center');
    $custom = (bool) setting('layout_custom_enabled', false);
    $presets = layout_presets();
    if ($custom) {
        $zones = setting('layout_zones', default_layout_zones());
        return is_array($zones) ? array_replace(['top'=>[], 'left'=>[], 'center'=>['register'], 'right'=>[], 'bottom'=>[]], $zones) : default_layout_zones();
    }
    return $presets[$preset]['zones'] ?? default_layout_zones();
}

function active_layout_class(): string
{
    $preset = setting('layout_preset', 'classic_center');
    $presets = layout_presets();
    return $presets[$preset]['class'] ?? 'layout-classic-center';
}
