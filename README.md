# ⚔️ MU Register Enterprise Pro

Web modular para crear cuentas de MU Online en SQL Server usando `sqlsrv`, `ODBC` o modo `auto`.

## ✅ Incluye

- Registro público para tabla `MEMB_INFO`.
- `config.php` central.
- Soporte `DB_DRIVER = auto | sqlsrv | odbc`.
- Captcha local generado por la web.
- Bloqueo temporal por IP.
- Cooldown de registro por IP.
- Logs de seguridad, registro, errores, Discord y admin.
- Discord webhook al crear cuenta.
- Fondo configurable: none, image, video, youtube.
- Modo `simple` o `pro`.
- Módulos activables:
  - Registro
  - Ranking
  - Online
  - Estado servidor
  - Descargas
  - Noticias
  - Eventos
- Panel admin.

## 📁 Instalación

Copiar la carpeta en XAMPP:

```txt
C:\xampp\htdocs\mu_register_enterprise_pro
```

Abrir:

```txt
http://localhost/mu_register_enterprise_pro
```

Panel admin:

```txt
http://localhost/mu_register_enterprise_pro/admin/login.php
```

Credenciales por defecto:

```txt
Usuario: admin
Clave: admin123
```

Cambiar esto en `config.php`.

## ⚙️ Base de datos

Editar `config.php`:

```php
define('DB_DRIVER', 'auto');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '1433');
define('DB_NAME', 'MuOnlineS1');
define('DB_USER', 'sa');
define('DB_PASS', 'TU_PASSWORD_SQL');
```

Para ODBC:

```php
define('DB_DRIVER', 'odbc');
define('DB_ODBC_DRIVER', 'ODBC Driver 18 for SQL Server');
```

Si usas ODBC Driver 17:

```php
define('DB_ODBC_DRIVER', 'ODBC Driver 17 for SQL Server');
```

## 🧪 Probar conexión

```txt
http://localhost/mu_register_enterprise_pro/db_test.php
```

## 🛡️ Recomendaciones

- Cambiar usuario y clave admin.
- Configurar Discord Webhook desde panel admin o `config.php`.
- Revisar nombres de tablas/campos del ranking si tu Season 1 usa otros nombres.
- Mantener `storage/` con permisos de escritura para Apache/PHP.

## 📌 Nota ranking/online

El ranking usa por defecto:

```txt
Character.Name
Character.cLevel
Character.ResetCount
Character.Class
```

Si tu base no tiene `ResetCount`, cambia `RANKING_RESET_FIELD` en `config.php`.

Online usa por defecto:

```txt
MEMB_STAT.ConnectStat = 1
```

Créditos:
- ChatGPT OpenAI
- Azzlaer / LatinBattle.com
