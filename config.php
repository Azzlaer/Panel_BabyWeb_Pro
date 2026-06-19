<?php
/**
 * MU Register Enterprise Pro
 * Configuracion principal inicial.
 * Muchas opciones tambien pueden administrarse desde el panel admin.
 */

define('APP_NAME', 'MU Online');
define('APP_VERSION', '2.2.0');
define('APP_TIMEZONE', 'America/Santiago');
date_default_timezone_set(APP_TIMEZONE);

/**
 * Modo visual de la web publica:
 * simple = solo registro centrado
 * pro    = registro + modulos laterales
 */
define('SITE_MODE', 'pro'); // simple | pro

define('SITE_TITLE', 'MU Online - Registro');
define('SITE_DESCRIPTION', 'Servidor privado MU Online Season 1');
define('SITE_KEYWORDS', 'mu online, season 1, servidor privado, mmorpg');

/**
 * Base de datos:
 * auto   = intenta sqlsrv y si no existe usa odbc
 * sqlsrv = usa extension sqlsrv
 * odbc   = usa extension odbc
 */
define('DB_DRIVER', 'auto'); // auto | sqlsrv | odbc

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '1433');
define('DB_NAME', 'MuOnline');
define('DB_USER', 'sa');
define('DB_PASS', 'TU_PASSWORD_SQL');

/**
 * ODBC sin DSN: usa Driver instalado en Windows.
 * Si usas DSN de Windows, pon el nombre en DB_ODBC_DSN y se usara ese DSN.
 */
define('DB_ODBC_DSN', '');
define('DB_ODBC_DRIVER', 'ODBC Driver 18 for SQL Server'); // ODBC Driver 17 for SQL Server | ODBC Driver 18 for SQL Server
define('DB_ODBC_ENCRYPT', false);
define('DB_ODBC_TRUST_CERTIFICATE', true);

/**
 * Admin inicial.
 * Recomendado cambiar al instalar.
 */
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
define('ADMIN_SESSION_NAME', 'MU_REGISTER_ADMIN');

/**
 * Registro.
 */
define('ALLOW_REGISTER', true);
define('MAX_USER_LENGTH', 10);
define('MAX_PASS_LENGTH', 10);
define('MAX_NAME_LENGTH', 10);
define('MAX_EMAIL_LENGTH', 50);
define('REGISTER_COOLDOWN_SECONDS', 60);
define('REGISTER_MAX_PER_IP_DAY', 3);

define('DEFAULT_SNO_NUMBER', '111111111111111111');
define('DEFAULT_BLOC_CODE', '0');
define('DEFAULT_CTL1_CODE', '0');
define('DEFAULT_ACCOUNT_LEVEL', 0);
define('DEFAULT_EXPIRE_DATE', '1900-01-01 00:00:00');

/**
 * Perfil de tabla MEMB_INFO.
 * standard = tabla clasica MU Online sin Cash/Gold/Joyas/HWID.
 * extended = tabla personalizada con Cash/Gold/Joyas/HWID/Auth2FA.
 * Para la SQL enviada el valor correcto es standard.
 */
define('MEMB_INFO_PROFILE', 'standard'); // standard | extended

/**
 * Captcha local.
 */
define('CAPTCHA_ENABLED', true);
define('CAPTCHA_LENGTH', 5);
define('CAPTCHA_SESSION_KEY', 'mu_captcha_answer');

/**
 * Seguridad IP.
 */
define('IP_BLOCK_ENABLED', true);
define('MAX_FAILED_ATTEMPTS', 5);
define('BLOCK_MINUTES', 30);

/**
 * Discord Webhook.
 */
define('DISCORD_WEBHOOK_ENABLED', false);
define('DISCORD_WEBHOOK_URL', 'PEGAR_AQUI_TU_WEBHOOK_DE_DISCORD');
define('DISCORD_HIDE_EMAIL', true);
define('DISCORD_HIDE_IP', false);

/**
 * Fondo visual.
 * none | image | video | youtube
 */
define('BACKGROUND_MODE', 'none');
define('BACKGROUND_IMAGE', 'assets/media/background.jpg');
define('BACKGROUND_VIDEO', 'assets/media/background.mp4');
define('BACKGROUND_YOUTUBE_ID', '');
define('BACKGROUND_OVERLAY_OPACITY', 0.65);

/**
 * Modulos publicos por defecto. Se pueden sobreescribir desde storage/data/settings.json.
 */
define('MODULE_REGISTER_ENABLED', true);
define('MODULE_RANKING_ENABLED', true);
define('MODULE_ONLINE_ENABLED', true);
define('MODULE_SERVER_STATUS_ENABLED', true);
define('MODULE_DOWNLOADS_ENABLED', true);
define('MODULE_NEWS_ENABLED', true);
define('MODULE_EVENTS_ENABLED', true);
define('MODULE_DISCORD_WIDGET_ENABLED', false);

/**
 * Ranking.
 * Ajusta campos si tu base usa nombres diferentes.
 */
define('RANKING_TABLE', 'Character');
define('RANKING_NAME_FIELD', 'Name');
define('RANKING_LEVEL_FIELD', 'cLevel');
define('RANKING_RESET_FIELD', 'ResetCount');
define('RANKING_CLASS_FIELD', 'Class');
define('RANKING_LIMIT', 10);

/**
 * Online.
 * Tabla tipica: MEMB_STAT con ConnectStat = 1.
 */
define('ONLINE_TABLE', 'MEMB_STAT');
define('ONLINE_ACCOUNT_FIELD', 'memb___id');
define('ONLINE_STATUS_FIELD', 'ConnectStat');
define('ONLINE_STATUS_ON_VALUE', '1');

/**
 * Server Status.
 */
define('SERVER_STATUS_ITEMS', json_encode([
    ['name' => 'ConnectServer', 'host' => '127.0.0.1', 'port' => 44405],
    ['name' => 'GameServer', 'host' => '127.0.0.1', 'port' => 55901],
    ['name' => 'SQL Server', 'host' => '127.0.0.1', 'port' => 1433],
]));

/**
 * Descargas por defecto.
 */
define('DOWNLOAD_CLIENT_URL', '#');
define('DOWNLOAD_PATCH_URL', '#');
define('DOWNLOAD_LAUNCHER_URL', '#');
define('CLIENT_VERSION', 'Season 1 - v1.0.0');
define('CLIENT_SIZE', '1.4 GB');

/**
 * Mantenimiento.
 */
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'Estamos preparando la apertura del servidor.');
