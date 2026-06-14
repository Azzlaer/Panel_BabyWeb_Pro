<?php
require_once __DIR__ . '/_auth.php'; admin_require();
require_once __DIR__ . '/../includes/security.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!empty($_POST['unblock'])) unblock_ip($_POST['unblock']);
 if(!empty($_POST['manual_ip'])) block_ip(trim($_POST['manual_ip']), $_POST['reason']??'Bloqueo manual');
}
$blocks=get_blocks(); admin_header('🚫 Bloqueos IP'); ?>
<div class="card"><form method="post"><div class="grid"><label>IP manual<input class="admin-input" name="manual_ip" placeholder="190.0.0.1"></label><label>Razón<input class="admin-input" name="reason" value="Bloqueo manual"></label></div><button>Bloquear IP</button></form></div>
<div class="card"><table class="table"><tr><th>IP</th><th>Razón</th><th>Creado</th><th>Hasta</th><th>Acción</th></tr><?php foreach($blocks as $ip=>$b):?><tr><td><?=e($ip)?></td><td><?=e($b['reason']??'')?></td><td><?=e($b['created_at']??'')?></td><td><?=e($b['until']??'')?></td><td><form method="post"><button name="unblock" value="<?=e($ip)?>">Desbloquear</button></form></td></tr><?php endforeach;?></table></div>
<?php admin_footer(); ?>
