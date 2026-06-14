<?php
session_start();
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/captcha.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/discord.php';

$ip = client_ip();
if (!ALLOW_REGISTER || !module_enabled('register')) redirect_with('error', 'El registro está deshabilitado.');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect_with('error', 'Solicitud inválida.');
if (is_ip_blocked($ip)) redirect_with('error', 'Tu IP está bloqueada temporalmente.');
[$okIp, $ipMsg] = can_register_by_ip($ip); if (!$okIp) redirect_with('error', $ipMsg);

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$password2 = trim($_POST['password_confirm'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$captcha = trim($_POST['captcha'] ?? '');

function fail_reg($ip, $msg) { failed_attempt($ip, $msg); redirect_with('error', $msg); }
if (empty($_POST['terms'])) fail_reg($ip, 'Debes aceptar las reglas del servidor.');
if (!$username || !$password || !$password2 || !$name || !$email) fail_reg($ip, 'Todos los campos son obligatorios.');
if ($password !== $password2) fail_reg($ip, 'Las contraseñas no coinciden.');
if (CAPTCHA_ENABLED && !captcha_verify($captcha)) fail_reg($ip, 'Captcha incorrecto.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail_reg($ip, 'Email inválido.');
foreach (['usuario'=>$username,'contraseña'=>$password,'nombre'=>$name] as $label=>$value) if (!preg_match('/^[a-zA-Z0-9_]+$/', $value)) fail_reg($ip, "El $label solo puede usar letras, números y guion bajo.");
if (strlen($username)<4 || strlen($username)>MAX_USER_LENGTH) fail_reg($ip, 'Usuario fuera de longitud permitida.');
if (strlen($password)<4 || strlen($password)>MAX_PASS_LENGTH) fail_reg($ip, 'Contraseña fuera de longitud permitida.');
if (strlen($name)<3 || strlen($name)>MAX_NAME_LENGTH) fail_reg($ip, 'Nombre fuera de longitud permitida.');
if (strlen($email)>MAX_EMAIL_LENGTH) fail_reg($ip, 'Email demasiado largo.');

try {
    $db = db();
    $exists = (int)$db->scalar('SELECT COUNT(*) FROM MEMB_INFO WHERE memb___id = ?', [$username]);
    if ($exists > 0) fail_reg($ip, 'El usuario ya existe.');
    $emailExists = (int)$db->scalar('SELECT COUNT(*) FROM MEMB_INFO WHERE mail_addr = ?', [$email]);
    if ($emailExists > 0) fail_reg($ip, 'El email ya está registrado.');

    $sql = "INSERT INTO MEMB_INFO (memb___id,memb__pwd,memb_name,sno__numb,post_code,addr_info,addr_deta,tel__numb,phon_numb,mail_addr,fpas_ques,fpas_answ,job__code,appl_days,modi_days,out__days,true_days,mail_chek,bloc_code,ctl1_code,AccountLevel,AccountExpireDate,BBless,BSoul,BLife,BChaos,BCreation,BGuardian,BHarmony,BRefin,BLowRefin,BGemstone,BPickBless,BPickSoul,BPickLife,BPickChaos,BPickCreation,BPickGuardian,BPickHarmony,BPickRefin,BPickLowRefin,BPickGemstone,hwid,auth2fa,Cash,Gold) VALUES (?,?,?,?,NULL,NULL,NULL,NULL,NULL,?,NULL,NULL,NULL,GETDATE(),GETDATE(),NULL,GETDATE(),'0',?,?,?,?,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0',0,?,?)";
    $db->query($sql, [$username,$password,$name,DEFAULT_SNO_NUMBER,$email,DEFAULT_BLOC_CODE,DEFAULT_CTL1_CODE,DEFAULT_ACCOUNT_LEVEL,DEFAULT_EXPIRE_DATE,DEFAULT_CASH,DEFAULT_GOLD]);
    mark_registration_ip($ip);
    write_log('register.log', 'Cuenta creada', ['user'=>$username,'email'=>$email,'ip'=>$ip]);
    discord_notify_register($username, $email, $ip);
    header('Location: index.php?success=1'); exit;
} catch (Throwable $e) {
    write_log('errors.log', 'Error registro', ['error'=>$e->getMessage()]);
    redirect_with('error', 'No se pudo crear la cuenta. Revisa la conexión o la estructura de la base de datos.');
}
