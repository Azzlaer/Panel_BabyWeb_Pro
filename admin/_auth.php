<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/functions.php';

function admin_is_logged(): bool { return !empty($_SESSION[ADMIN_SESSION_NAME]); }
function admin_require(): void { if (!admin_is_logged()) { header('Location: login.php'); exit; } }
function admin_header(string $title): void { ?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($title) ?> - Admin</title><link rel="stylesheet" href="admin.css"></head><body><div class="admin-wrap"><aside class="sidebar"><h2>⚔️ Admin</h2><a href="dashboard.php">📊 Dashboard</a><a href="settings.php">⚙️ Configuración</a><a href="modules.php">🧩 Módulos</a><a href="accounts.php">👥 Cuentas</a><a href="content.php">📰 Noticias/Eventos</a><a href="ip_blocks.php">🚫 Bloqueos IP</a><a href="logs.php">📜 Logs</a><a href="../index.php">🌐 Ver web</a><a href="logout.php">🚪 Salir</a></aside><main class="content"><h1><?= e($title) ?></h1><br>
<?php }
function admin_footer(): void { ?></main></div></body></html><?php }
