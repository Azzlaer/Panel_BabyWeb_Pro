<?php
session_start();
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/modules.php';
require_once __DIR__ . '/includes/content.php';

ensure_storage();
$ip = client_ip();
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
$blocked = is_ip_blocked($ip);
$mode = setting('site_mode', SITE_MODE);
$maintenance = (bool) setting('maintenance_mode', false);

function render_register_module($success, $error, $blocked): void { ?>
  <section class="card main-card module-card module-register">
    <div class="logo-area"><div class="mu-icon">⚔️</div><h1>Crear Cuenta</h1><p>Registro oficial de aventureros</p></div>
    <?php if ($success): ?><div class="alert success">✅ Cuenta creada correctamente. Ya puedes ingresar al juego.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert error">❌ <?= e($error) ?></div><?php endif; ?>
    <?php if ($blocked): ?><div class="alert error">🚫 Tu IP está bloqueada temporalmente por seguridad.</div><?php else: ?>
    <form action="register.php" method="post" class="form-box" autocomplete="off">
      <label>👤 Usuario<input name="username" maxlength="10" minlength="4" required placeholder="Máx. 10 caracteres"></label>
      <label>🔐 Contraseña<input type="password" name="password" maxlength="10" minlength="4" required placeholder="Máx. 10 caracteres"></label>
      <label>🔐 Repetir contraseña<input type="password" name="password_confirm" maxlength="10" minlength="4" required></label>
      <label>🧙 Nombre<input name="name" maxlength="10" minlength="3" required></label>
      <label>📧 Email<input type="email" name="email" maxlength="50" required></label>
      <?php if (CAPTCHA_ENABLED): ?><div class="captcha-box"><img src="captcha.php?<?= time() ?>" alt="Captcha"><label>🧩 Captcha<input name="captcha" maxlength="8" required placeholder="Escribe el código"></label></div><?php endif; ?>
      <label class="check"><input type="checkbox" name="terms" required> Acepto las reglas del servidor 🛡️</label>
      <button>✨ Crear Cuenta</button>
    </form>
    <?php endif; ?>
    <div class="footer-pills"><span>🏰 Season 1</span><span>💎 Registro seguro</span><span>🔥 v<?= e(APP_VERSION) ?></span></div>
  </section>
<?php }

function render_module_card(string $name, $success = '', $error = '', $blocked = false): void {
    if (!module_enabled($name)) return;
    switch ($name) {
        case 'register': render_register_module($success, $error, $blocked); break;
        case 'server_status': ?>
            <section class="card module-card module-status"><h3>🟢 Estado del servidor</h3><?php foreach(module_server_status_items() as $s): ?><div class="row"><span><?= e($s['name']) ?></span><b class="<?= $s['online']?'ok':'bad' ?>"><?= $s['online']?'Online':'Offline' ?></b></div><?php endforeach; ?></section>
        <?php break;
        case 'ranking': ?>
            <section class="card module-card module-ranking"><h3>🏆 Ranking</h3><?php $rk=module_ranking(); if(isset($rk['_error'])): ?><small>No disponible: <?= e($rk['_error']) ?></small><?php else: foreach($rk as $i=>$r): ?><div class="rank"><b>#<?= $i+1 ?></b><span><?= e($r['name']??'') ?></span><small>Reset <?= e($r['resets']??0) ?> · Lv <?= e($r['level']??0) ?></small></div><?php endforeach; endif; ?></section>
        <?php break;
        case 'online': ?>
            <section class="card module-card module-online"><h3>🟢 Online</h3><div class="big-number"><?= module_online_count() ?? 'N/D' ?></div><small>Usuarios conectados</small></section>
        <?php break;
        case 'events': ?>
            <section class="card module-card module-events"><h3>📅 Eventos</h3><?php foreach(module_events() as $ev): ?><div class="row"><span><?= e(($ev['emoji']??'✨').' '.($ev['name']??'')) ?></span><small><?= e($ev['time']??'') ?></small></div><?php endforeach; ?></section>
        <?php break;
        case 'downloads': ?>
            <section class="card module-card module-downloads"><h3>⬇️ Descargas</h3><a class="mini-btn" href="<?= e(setting('downloads.client_url','#')) ?>">Cliente <?= e(setting('downloads.client_version', CLIENT_VERSION)) ?></a><a class="mini-btn" href="<?= e(setting('downloads.patch_url','#')) ?>">Patch</a><a class="mini-btn" href="<?= e(setting('downloads.launcher_url','#')) ?>">Launcher</a></section>
        <?php break;
        case 'news': ?>
            <section class="card module-card module-news"><h3>📰 Noticias</h3><?php foreach(array_slice(module_news(),0,(int)setting('news_public_limit', 3)) as $n): ?><article class="news <?= !empty($n['pinned'])?'pinned':'' ?>"><?php if(!empty($n['image'])): ?><img class="news-cover" src="<?= e($n['image']) ?>" alt="<?= e($n['title']??'Noticia') ?>"><?php endif; ?><b><?= e(($n['emoji']??'📰').' '.($n['title']??'')) ?></b><small><?= e($n['date']??'') ?> · <?= e($n['tag']??'') ?><?= !empty($n['pinned'])?' · 📌':'' ?></small><div class="news-body"><?= render_rich_content($n['content']??'', $n['format']??'plain') ?></div></article><?php endforeach; ?></section>
        <?php break;
    }
}

function render_zone(string $zone, array $modules, $success, $error, $blocked): void {
    if (!$modules) return;
    echo '<div class="zone zone-' . e($zone) . '">';
    foreach ($modules as $module) render_module_card((string)$module, $success, $error, $blocked);
    echo '</div>';
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(setting('site_title', SITE_TITLE)) ?></title>
<meta name="description" content="<?= e(setting('site_description', SITE_DESCRIPTION)) ?>">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php $bg = setting('background_mode', BACKGROUND_MODE); ?>
<?php if ($bg === 'image'): ?><div class="media-bg image-bg" style="background-image:url('<?= e(setting('background_image', BACKGROUND_IMAGE)) ?>')"></div><?php endif; ?>
<?php if ($bg === 'video'): ?><video class="media-bg video-bg" autoplay muted loop playsinline><source src="<?= e(setting('background_video', BACKGROUND_VIDEO)) ?>" type="video/mp4"></video><?php endif; ?>
<?php if ($bg === 'youtube' && setting('background_youtube_id', '') !== ''): ?><div class="youtube-bg"><iframe src="https://www.youtube.com/embed/<?= e(setting('background_youtube_id')) ?>?autoplay=1&mute=1&controls=0&loop=1&playlist=<?= e(setting('background_youtube_id')) ?>&showinfo=0&modestbranding=1&rel=0" allow="autoplay; fullscreen"></iframe></div><?php endif; ?>
<div class="media-overlay" style="background:rgba(0,0,0,<?= e(setting('background_overlay_opacity', BACKGROUND_OVERLAY_OPACITY)) ?>)"></div>
<div class="background-grid"></div>

<header class="topbar">
  <div class="brand"><span>⚔️</span><strong><?= e(APP_NAME) ?></strong></div>
  <a class="admin-link" href="admin/login.php">🛡️ Admin</a>
</header>

<?php if ($maintenance): ?>
<main class="layout layout-simple"><section class="card main-card"><h1>🛠️ Mantenimiento</h1><p><?= e(setting('maintenance_message', MAINTENANCE_MESSAGE)) ?></p></section></main>
<?php else: ?>
  <?php if ($mode !== 'pro'): ?>
  <main class="layout layout-simple">
    <?php render_register_module($success, $error, $blocked); ?>
  </main>
  <?php else: $zones = active_layout_zones(); ?>
  <main class="layout layout-pro layout-builder <?= e(active_layout_class()) ?>">
    <?php render_zone('top', $zones['top'] ?? [], $success, $error, $blocked); ?>
    <div class="layout-columns">
      <?php render_zone('left', $zones['left'] ?? [], $success, $error, $blocked); ?>
      <?php render_zone('center', $zones['center'] ?? ['register'], $success, $error, $blocked); ?>
      <?php render_zone('right', $zones['right'] ?? [], $success, $error, $blocked); ?>
    </div>
    <?php render_zone('bottom', $zones['bottom'] ?? [], $success, $error, $blocked); ?>
  </main>
  <?php endif; ?>
<?php endif; ?>
</body>
</html>
