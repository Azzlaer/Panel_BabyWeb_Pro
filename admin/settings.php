<?php
require_once __DIR__ . '/_auth.php'; admin_require();
$settings=load_settings(); $saved=false;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $settings['site_mode']=$_POST['site_mode']??'simple';
  $settings['site_title']=$_POST['site_title']??SITE_TITLE;
  $settings['site_description']=$_POST['site_description']??SITE_DESCRIPTION;
  $settings['maintenance_mode']=!empty($_POST['maintenance_mode']);
  $settings['maintenance_message']=$_POST['maintenance_message']??'';
  $settings['background_mode']=$_POST['background_mode']??'none';
  $settings['background_image']=$_POST['background_image']??'';
  $settings['background_video']=$_POST['background_video']??'';
  $settings['background_youtube_id']=$_POST['background_youtube_id']??'';
  $settings['background_overlay_opacity']=(float)($_POST['background_overlay_opacity']??0.65);
  $settings['discord_webhook_enabled']=!empty($_POST['discord_webhook_enabled']);
  $settings['discord_webhook_url']=$_POST['discord_webhook_url']??'';
  $settings['downloads']['client_url']=$_POST['client_url']??'#';
  $settings['downloads']['patch_url']=$_POST['patch_url']??'#';
  $settings['downloads']['launcher_url']=$_POST['launcher_url']??'#';
  $settings['downloads']['client_version']=$_POST['client_version']??'';
  $settings['downloads']['client_size']=$_POST['client_size']??'';
  save_settings($settings); $saved=true; write_log('admin.log','Configuracion guardada',['ip'=>client_ip()]);
}
admin_header('⚙️ Configuración'); if($saved):?><div class="alert">✅ Configuración guardada</div><?php endif; ?>
<form method="post" class="card"><div class="grid"><label>Modo sitio<select name="site_mode"><option value="simple" <?=($settings['site_mode']==='simple'?'selected':'')?>>Simple</option><option value="pro" <?=($settings['site_mode']==='pro'?'selected':'')?>>Pro</option></select></label><label>Título<input class="admin-input" name="site_title" value="<?=e($settings['site_title'])?>"></label><label>Descripción<input class="admin-input" name="site_description" value="<?=e($settings['site_description'])?>"></label><label>Overlay fondo<input class="admin-input" name="background_overlay_opacity" value="<?=e($settings['background_overlay_opacity'])?>"></label></div><label><input type="checkbox" name="maintenance_mode" <?=!empty($settings['maintenance_mode'])?'checked':''?>> Modo mantenimiento</label><label>Mensaje mantenimiento<textarea name="maintenance_message"><?=e($settings['maintenance_message'])?></textarea></label><h3>Fondo</h3><div class="grid"><label>Modo fondo<select name="background_mode"><option>none</option><option <?=($settings['background_mode']==='image'?'selected':'')?>>image</option><option <?=($settings['background_mode']==='video'?'selected':'')?>>video</option><option <?=($settings['background_mode']==='youtube'?'selected':'')?>>youtube</option></select></label><label>Imagen<input class="admin-input" name="background_image" value="<?=e($settings['background_image'])?>"></label><label>Video<input class="admin-input" name="background_video" value="<?=e($settings['background_video'])?>"></label><label>YouTube ID<input class="admin-input" name="background_youtube_id" value="<?=e($settings['background_youtube_id'])?>"></label></div><h3>Discord</h3><label><input type="checkbox" name="discord_webhook_enabled" <?=!empty($settings['discord_webhook_enabled'])?'checked':''?>> Activar webhook registro</label><label>Webhook URL<input class="admin-input" name="discord_webhook_url" value="<?=e($settings['discord_webhook_url'])?>"></label><h3>Descargas</h3><div class="grid"><label>Cliente URL<input class="admin-input" name="client_url" value="<?=e($settings['downloads']['client_url']??'#')?>"></label><label>Patch URL<input class="admin-input" name="patch_url" value="<?=e($settings['downloads']['patch_url']??'#')?>"></label><label>Launcher URL<input class="admin-input" name="launcher_url" value="<?=e($settings['downloads']['launcher_url']??'#')?>"></label><label>Versión cliente<input class="admin-input" name="client_version" value="<?=e($settings['downloads']['client_version']??'')?>"></label><label>Tamaño cliente<input class="admin-input" name="client_size" value="<?=e($settings['downloads']['client_size']??'')?>"></label></div><button>Guardar configuración</button></form>
<?php admin_footer(); ?>
