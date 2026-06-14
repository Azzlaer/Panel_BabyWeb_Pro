<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/settings.php';

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
    return json_file_read('news.json', [
        ['title'=>'Apertura oficial', 'date'=>date('Y-m-d'), 'tag'=>'Noticia', 'content'=>'Bienvenido al continente de MU. Crea tu cuenta y prepárate para la aventura.']
    ]);
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
