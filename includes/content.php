<?php
/**
 * Utilidades de contenido para noticias.
 * Soporta texto plano, BBCode y HTML seguro.
 */

function bbcode_to_html(string $text): string
{
    $html = e($text);
    $patterns = [
        '/\[b\](.*?)\[\/b\]/is' => '<strong>$1</strong>',
        '/\[i\](.*?)\[\/i\]/is' => '<em>$1</em>',
        '/\[u\](.*?)\[\/u\]/is' => '<u>$1</u>',
        '/\[center\](.*?)\[\/center\]/is' => '<div class="text-center">$1</div>',
        '/\[color=(#[a-f0-9]{3,6}|[a-z]+)\](.*?)\[\/color\]/is' => '<span style="color:$1">$2</span>',
        '/\[size=([0-9]{1,2})\](.*?)\[\/size\]/is' => '<span style="font-size:$1px">$2</span>',
        '/\[url\](https?:\/\/[^\s\[]+)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener">$1</a>',
        '/\[url=(https?:\/\/[^\s\]]+)\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener">$2</a>',
        '/\[img\](https?:\/\/[^\s\[]+)\[\/img\]/is' => '<img class="news-img" src="$1" alt="Imagen noticia">',
        '/\[quote\](.*?)\[\/quote\]/is' => '<blockquote>$1</blockquote>',
        '/\[hr\]/i' => '<hr>',
        '/\[br\]/i' => '<br>',
    ];
    foreach ($patterns as $pattern => $replacement) {
        $html = preg_replace($pattern, $replacement, $html);
    }
    return nl2br($html);
}

function sanitize_news_html(string $html): string
{
    $allowed = '<p><br><strong><b><em><i><u><span><div><ul><ol><li><a><img><blockquote><hr><h1><h2><h3><h4><small>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/javascript\s*:/i', '', $html);
    $html = preg_replace('/<a\s+/i', '<a target="_blank" rel="noopener" ', $html);
    return $html;
}

function render_rich_content(string $content, string $format = 'plain'): string
{
    $format = strtolower($format ?: 'plain');
    if ($format === 'bbcode') return bbcode_to_html($content);
    if ($format === 'html') return sanitize_news_html($content);
    return nl2br(e($content));
}

function normalize_news_item(array $item): array
{
    return [
        'id' => $item['id'] ?? ('news_' . date('YmdHis') . '_' . mt_rand(1000,9999)),
        'enabled' => array_key_exists('enabled', $item) ? (bool)$item['enabled'] : true,
        'pinned' => array_key_exists('pinned', $item) ? (bool)$item['pinned'] : false,
        'title' => trim((string)($item['title'] ?? 'Nueva noticia')),
        'date' => trim((string)($item['date'] ?? date('Y-m-d'))),
        'tag' => trim((string)($item['tag'] ?? 'Noticia')),
        'emoji' => trim((string)($item['emoji'] ?? '📰')),
        'image' => trim((string)($item['image'] ?? '')),
        'format' => in_array(($item['format'] ?? 'bbcode'), ['plain','bbcode','html'], true) ? $item['format'] : 'bbcode',
        'content' => (string)($item['content'] ?? ''),
    ];
}

function sorted_news(array $items): array
{
    $items = array_map('normalize_news_item', $items);
    $items = array_values(array_filter($items, fn($n) => !empty($n['enabled'])));
    usort($items, function($a, $b) {
        if ($a['pinned'] !== $b['pinned']) return $a['pinned'] ? -1 : 1;
        return strcmp($b['date'], $a['date']);
    });
    return $items;
}
