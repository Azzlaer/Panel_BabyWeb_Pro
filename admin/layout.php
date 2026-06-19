<?php
require_once __DIR__ . '/_auth.php'; admin_require();
require_once __DIR__ . '/../includes/modules.php';
$msg='';
$settings = load_settings();
$presets = layout_presets();
$available = [
    'register' => '⚔️ Registro',
    'news' => '📰 Noticias',
    'ranking' => '🏆 Ranking',
    'online' => '🟢 Usuarios online',
    'server_status' => '🖥️ Estado servidor',
    'events' => '📅 Eventos',
    'downloads' => '⬇️ Descargas',
];
$zonesNames = ['top'=>'Arriba', 'left'=>'Izquierda', 'center'=>'Centro', 'right'=>'Derecha', 'bottom'=>'Abajo'];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $settings['layout_preset'] = $_POST['layout_preset'] ?? 'classic_center';
    $settings['layout_custom_enabled'] = !empty($_POST['layout_custom_enabled']);
    $zones=[];
    foreach($zonesNames as $key=>$label){
        $raw = trim($_POST['zone_'.$key] ?? '');
        $items = array_values(array_filter(array_map('trim', explode(',', $raw))));
        $items = array_values(array_filter($items, fn($m)=>array_key_exists($m, $available)));
        $zones[$key]=$items;
    }
    if(!in_array('register', array_merge(...array_values($zones)), true)) {
        $zones['center'][] = 'register';
        $msg = 'Se agregó Registro al centro porque el index necesita un formulario de registro activo.';
    } else $msg='Diseño guardado correctamente.';
    $settings['layout_zones']=$zones;
    save_settings($settings);
}
$activePreset = $settings['layout_preset'] ?? 'classic_center';
$custom = !empty($settings['layout_custom_enabled']);
$zones = $settings['layout_zones'] ?? default_layout_zones();
admin_header('🎨 Diseñador del Index'); if($msg):?><div class="alert"><?=e($msg)?></div><?php endif; ?>

<div class="card">
<h3>🧩 Esquemas predeterminados</h3>
<p class="muted">Elige un esquema rápido o activa el modo personalizado para decidir en qué zona va cada bloque.</p>
<form method="post">
<label>Template / esquema
<select name="layout_preset" id="layoutPreset" onchange="applyPresetPreview()">
<?php foreach($presets as $key=>$p): ?><option value="<?=e($key)?>" <?=$activePreset===$key?'selected':''?>><?=e($p['name'])?></option><?php endforeach; ?>
</select>
</label>
<label class="checkline"><input type="checkbox" name="layout_custom_enabled" value="1" <?=$custom?'checked':''?>> Usar distribución personalizada en lugar del esquema predeterminado</label>
</div>

<div class="layout-admin-grid">
  <div class="card">
    <h3>📦 Módulos disponibles</h3>
    <p class="muted">Copia estos nombres técnicos en las zonas, separados por coma.</p>
    <?php foreach($available as $key=>$label): ?><div class="pill"><code><?=e($key)?></code> <?=e($label)?></div><?php endforeach; ?>
  </div>
  <div class="card">
    <h3>🧱 Zonas del index</h3>
    <?php foreach($zonesNames as $key=>$label): ?>
      <label><?=e($label)?> <small>zona: <?=e($key)?></small>
      <input class="admin-input zone-input" name="zone_<?=e($key)?>" id="zone_<?=e($key)?>" value="<?=e(implode(',', $zones[$key] ?? []))?>" placeholder="register,news,ranking">
      </label>
    <?php endforeach; ?>
    <button>💾 Guardar diseño</button>
    <a class="btn" href="../index.php" target="_blank">👁️ Ver index</a>
  </div>
</div>
</form>

<div class="card">
<h3>👁️ Vista esquemática</h3>
<div class="wireframe">
  <div class="wf wf-top">Arriba</div>
  <div class="wf-cols"><div class="wf wf-left">Izquierda</div><div class="wf wf-center">Centro</div><div class="wf wf-right">Derecha</div></div>
  <div class="wf wf-bottom">Abajo</div>
</div>
</div>

<script>
const presets = <?=json_encode($presets, JSON_UNESCAPED_UNICODE)?>;
function applyPresetPreview(){
  const key=document.getElementById('layoutPreset').value;
  const zones=presets[key]?.zones || {};
  ['top','left','center','right','bottom'].forEach(z=>{
    const input=document.getElementById('zone_'+z);
    if(input) input.value=(zones[z]||[]).join(',');
  });
}
</script>
<?php admin_footer(); ?>
