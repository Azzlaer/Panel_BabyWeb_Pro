# ⚔️ MU Register Enterprise Pro - MuOnline Standard

Web modular para registrar cuentas de MU Online usando `dbo.MEMB_INFO` estándar en SQL Server.

## Funciones principales

- Registro de cuentas compatible con la tabla `MEMB_INFO` estándar.
- Conexión por `sqlsrv`, `ODBC` o modo `auto`.
- Captcha local generado por la web.
- Bloqueo temporal por IP.
- Logs de registro y seguridad.
- Notificación de nuevas cuentas por Discord Webhook.
- Panel admin.
- Modo visual `simple` o `pro`.
- Fondo configurable: ninguno, imagen, video local o YouTube.
- Módulos activables: registro, ranking, online, estado, descargas, noticias y eventos.
- Diseñador del index desde el panel admin.
- Editor visual de noticias con texto plano, BBCode o HTML seguro.

## Panel admin

URL:

```txt
http://localhost/mu_register_enterprise_pro/admin/login.php
```

Credenciales iniciales:

```txt
Usuario: admin
Clave: admin123
```

Cambia estas credenciales en `config.php` antes de publicar la web.

## Base de datos

Configura en `config.php`:

```php
define('DB_DRIVER', 'auto'); // auto | sqlsrv | odbc
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '1433');
define('DB_NAME', 'MuOnline');
define('DB_USER', 'sa');
define('DB_PASS', 'TU_PASSWORD_SQL');
define('MEMB_INFO_PROFILE', 'standard');
```

Para probar la conexión:

```txt
http://localhost/mu_register_enterprise_pro/db_test.php
```

## Editor de noticias

En el panel entra a:

```txt
📰 Noticias/Eventos
```

Puedes crear noticias usando:

- Texto plano.
- BBCode.
- HTML seguro.

BBCode soportado:

```txt
[b]negrita[/b]
[i]cursiva[/i]
[u]subrayado[/u]
[color=#ffd28a]texto dorado[/color]
[url]https://ejemplo.com[/url]
[url=https://ejemplo.com]Texto[/url]
[img]https://ejemplo.com/imagen.jpg[/img]
[quote]cita[/quote]
[center]centrado[/center]
[hr]
[br]
```

## Diseñador del index

En el panel entra a:

```txt
🎨 Diseñador Index
```

Puedes elegir esquemas predeterminados:

- Clásico centrado.
- Registro izquierda.
- Registro derecha.
- Registro arriba tipo portada.
- Revista / Portal.
- Minimal Pro.

También puedes activar distribución personalizada y decidir dónde va cada módulo:

```txt
top
left
center
right
bottom
```

Módulos disponibles:

```txt
register
news
ranking
online
server_status
events
downloads
```

Ejemplo:

```txt
Centro: register,news
Izquierda: ranking
Derecha: online,server_status,events
Abajo: downloads
```

## Créditos

- ChatGPT OpenAI
- Azzlaer / LatinBattle.com
