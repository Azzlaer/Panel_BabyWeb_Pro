<?php
require_once __DIR__ . '/_auth.php'; admin_require();
$file=$_GET['file']??'register.log';
$allowed=['register.log','security.log','errors.log','discord.log','admin.log'];
if(!in_array($file,$allowed,true))$file='register.log';
admin_header('📜 Logs'); ?>
<div class="card"><?php foreach($allowed as $f):?><a class="btn" href="?file=<?=e($f)?>"><?=e($f)?></a> <?php endforeach;?></div>
<div class="card"><h3><?=e($file)?></h3><div class="log"><?=e(implode("\n", array_reverse(read_log_lines($file,300))))?></div></div>
<?php admin_footer(); ?>
