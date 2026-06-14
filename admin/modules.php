<?php
require_once __DIR__ . '/_auth.php'; admin_require();
$settings=load_settings(); $saved=false;
$names=['register'=>'Registro','ranking'=>'Ranking','online'=>'Usuarios online','server_status'=>'Estado servidor','downloads'=>'Descargas','news'=>'Noticias','events'=>'Eventos','discord_widget'=>'Widget Discord'];
if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach($names as $key=>$label) $settings['modules'][$key]=!empty($_POST['mod_'.$key]);
  save_settings($settings); $saved=true; write_log('admin.log','Modulos guardados',['ip'=>client_ip()]);
}
admin_header('🧩 Módulos'); if($saved):?><div class="alert">✅ Módulos actualizados</div><?php endif; ?>
<form method="post" class="card"><table class="table"><tr><th>Módulo</th><th>Activo</th></tr><?php foreach($names as $key=>$label):?><tr><td><?=e($label)?></td><td><input type="checkbox" name="mod_<?=e($key)?>" <?=!empty($settings['modules'][$key])?'checked':''?>></td></tr><?php endforeach;?></table><br><button>Guardar módulos</button></form>
<?php admin_footer(); ?>
