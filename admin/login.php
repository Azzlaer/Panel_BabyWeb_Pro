<?php
require_once __DIR__ . '/_auth.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (($_POST['user']??'') === ADMIN_USER && ($_POST['pass']??'') === ADMIN_PASS) {
        $_SESSION[ADMIN_SESSION_NAME] = true; header('Location: dashboard.php'); exit;
    }
    $error='Credenciales incorrectas'; write_log('security.log','Login admin fallido',['ip'=>client_ip()]);
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Login</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="background-grid"></div><main class="layout layout-simple"><section class="card main-card"><div class="logo-area"><div class="mu-icon">🛡️</div><h1>Panel Admin</h1><p>MU Register Enterprise Pro</p></div><?php if($error):?><div class="alert error">❌ <?= e($error) ?></div><?php endif;?><form method="post" class="form-box"><label>Usuario<input name="user" required></label><label>Contraseña<input type="password" name="pass" required></label><button>Entrar</button></form></section></main></body></html>
