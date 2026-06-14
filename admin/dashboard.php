<?php
require_once __DIR__ . '/_auth.php'; admin_require();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/security.php';
admin_header('📊 Dashboard');
$total='N/D'; $today='N/D'; $dbstatus='Offline'; $driver='N/D';
try{ $d=db(); $driver=$d->driver; $total=(int)$d->scalar('SELECT COUNT(*) FROM MEMB_INFO'); $today=(int)$d->scalar("SELECT COUNT(*) FROM MEMB_INFO WHERE CONVERT(date, appl_days)=CONVERT(date, GETDATE())"); $dbstatus='Online'; }catch(Throwable $e){ write_log('errors.log','Dashboard DB error',['error'=>$e->getMessage()]); }
$blocks=count(get_blocks());
?>
<div class="grid"><div class="card"><h3>Base de datos</h3><div class="stat"><?= e($dbstatus) ?></div><small>Driver: <?= e($driver) ?></small></div><div class="card"><h3>Total cuentas</h3><div class="stat"><?= e($total) ?></div></div><div class="card"><h3>Cuentas hoy</h3><div class="stat"><?= e($today) ?></div></div><div class="card"><h3>IPs bloqueadas</h3><div class="stat"><?= e($blocks) ?></div></div></div>
<div class="card"><h3>Últimos registros</h3><div class="log"><?= e(implode("\n", array_reverse(read_log_lines('register.log', 20)))) ?></div></div>
<?php admin_footer(); ?>
