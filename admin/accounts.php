<?php
require_once __DIR__ . '/_auth.php'; admin_require();
require_once __DIR__ . '/../includes/db.php';
$msg='';
try{
 $d=db();
 if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['account'], $_POST['action'])){
   $acc=$_POST['account'];
   if($_POST['action']==='block'){$d->query("UPDATE MEMB_INFO SET bloc_code='1' WHERE memb___id=?",[$acc]);$msg='Cuenta bloqueada';}
   if($_POST['action']==='unblock'){$d->query("UPDATE MEMB_INFO SET bloc_code='0' WHERE memb___id=?",[$acc]);$msg='Cuenta desbloqueada';}
   write_log('admin.log','Accion cuenta',['account'=>$acc,'action'=>$_POST['action']]);
 }
 $q=trim($_GET['q']??'');
 if($q!=='') $rows=$d->fetchAll("SELECT TOP 100 memb___id,memb_name,mail_addr,appl_days,bloc_code,AccountLevel,Cash,Gold FROM MEMB_INFO WHERE memb___id LIKE ? OR mail_addr LIKE ? ORDER BY memb_guid DESC",['%'.$q.'%','%'.$q.'%']);
 else $rows=$d->fetchAll("SELECT TOP 100 memb___id,memb_name,mail_addr,appl_days,bloc_code,AccountLevel,Cash,Gold FROM MEMB_INFO ORDER BY memb_guid DESC");
}catch(Throwable $e){$rows=[];$msg='Error DB: '.$e->getMessage();}
admin_header('👥 Cuentas'); if($msg):?><div class="alert"><?=e($msg)?></div><?php endif; ?>
<form method="get" class="card"><label>Buscar usuario/email<input class="admin-input" name="q" value="<?=e($_GET['q']??'')?>"></label><button>Buscar</button></form>
<div class="card"><table class="table"><tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Fecha</th><th>Block</th><th>Cash</th><th>Gold</th><th>Acción</th></tr><?php foreach($rows as $r):?><tr><td><?=e($r['memb___id']??'')?></td><td><?=e($r['memb_name']??'')?></td><td><?=e($r['mail_addr']??'')?></td><td><?=e(is_object($r['appl_days']??null)?$r['appl_days']->format('Y-m-d H:i:s'):($r['appl_days']??''))?></td><td><?=e($r['bloc_code']??'')?></td><td><?=e($r['Cash']??0)?></td><td><?=e($r['Gold']??0)?></td><td><form method="post"><input type="hidden" name="account" value="<?=e($r['memb___id']??'')?>"><button name="action" value="<?=($r['bloc_code']??'0')==='1'?'unblock':'block'?>"><?=($r['bloc_code']??'0')==='1'?'Desbloquear':'Bloquear'?></button></form></td></tr><?php endforeach;?></table></div>
<?php admin_footer(); ?>
