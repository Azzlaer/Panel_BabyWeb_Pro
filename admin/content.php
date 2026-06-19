<?php
require_once __DIR__ . '/_auth.php'; admin_require();
require_once __DIR__ . '/../includes/content.php';
$msg='';
$news = json_file_read('news.json', []);
$events = json_file_read('events.json', []);

if($_SERVER['REQUEST_METHOD']==='POST'){
    $action = $_POST['action'] ?? '';
    if($action === 'save_news_form'){
        $items=[];
        $count = count($_POST['title'] ?? []);
        for($i=0;$i<$count;$i++){
            $title = trim($_POST['title'][$i] ?? '');
            $content = (string)($_POST['content'][$i] ?? '');
            if($title === '' && trim($content) === '') continue;
            $items[] = normalize_news_item([
                'id' => $_POST['id'][$i] ?? '',
                'enabled' => isset($_POST['enabled'][$i]),
                'pinned' => isset($_POST['pinned'][$i]),
                'title' => $title,
                'date' => $_POST['date'][$i] ?? date('Y-m-d'),
                'tag' => $_POST['tag'][$i] ?? 'Noticia',
                'emoji' => $_POST['emoji'][$i] ?? '📰',
                'image' => $_POST['image'][$i] ?? '',
                'format' => $_POST['format'][$i] ?? 'bbcode',
                'content' => $content,
            ]);
        }
        json_file_write('news.json',$items); $news=$items; $msg='Noticias guardadas desde el editor visual.';
    }
    if($action === 'save_events_form'){
        $items=[];
        $count = count($_POST['event_name'] ?? []);
        for($i=0;$i<$count;$i++){
            $name = trim($_POST['event_name'][$i] ?? '');
            if($name === '') continue;
            $items[] = ['emoji'=>trim($_POST['event_emoji'][$i] ?? '✨'), 'name'=>$name, 'time'=>trim($_POST['event_time'][$i] ?? '')];
        }
        json_file_write('events.json',$items); $events=$items; $msg='Eventos guardados desde el editor visual.';
    }
    if($action === 'save_raw'){
        if(isset($_POST['news_json'])){ $data=json_decode($_POST['news_json'],true); if(is_array($data)){json_file_write('news.json',$data);$news=$data;$msg='Noticias JSON guardadas';} else $msg='JSON de noticias inválido'; }
        if(isset($_POST['events_json'])){ $data=json_decode($_POST['events_json'],true); if(is_array($data)){json_file_write('events.json',$data);$events=$data;$msg='Eventos JSON guardados';} else $msg='JSON de eventos inválido'; }
    }
}
if(!$news){$news=[normalize_news_item(['title'=>'Apertura oficial','date'=>date('Y-m-d'),'tag'=>'Noticia','emoji'=>'📰','format'=>'bbcode','content'=>'[b]Bienvenido al continente de MU.[/b]\nCrea tu cuenta y prepárate para la aventura.'])];}
if(!$events){$events=[['emoji'=>'🩸','name'=>'Blood Castle','time'=>'Cada 2 horas'],['emoji'=>'🔥','name'=>'Devil Square','time'=>'Cada 3 horas']];}
admin_header('📰 Noticias / Eventos'); if($msg):?><div class="alert"><?=e($msg)?></div><?php endif; ?>

<div class="card">
<h3>🧾 Editor visual de noticias</h3>
<p class="muted">Puedes escribir en texto plano, BBCode o HTML seguro. El BBCode soporta: <code>[b]</code>, <code>[i]</code>, <code>[u]</code>, <code>[color=#d49a3a]</code>, <code>[url]</code>, <code>[img]</code>, <code>[quote]</code>, <code>[center]</code>, <code>[hr]</code>.</p>
<form method="post" id="newsForm">
<input type="hidden" name="action" value="save_news_form">
<div id="newsItems">
<?php foreach(array_values($news) as $i=>$n): $n=normalize_news_item($n); ?>
  <div class="editor-item">
    <div class="editor-head"><strong>📰 Noticia #<?= $i+1 ?></strong><button type="button" class="danger small" onclick="this.closest('.editor-item').remove()">Eliminar</button></div>
    <input type="hidden" name="id[]" value="<?=e($n['id'])?>">
    <div class="grid">
      <label>Activa<br><input type="checkbox" name="enabled[<?=$i?>]" <?=$n['enabled']?'checked':''?>></label>
      <label>Fijada 📌<br><input type="checkbox" name="pinned[<?=$i?>]" <?=$n['pinned']?'checked':''?>></label>
      <label>Emoji<input class="admin-input" name="emoji[]" value="<?=e($n['emoji'])?>"></label>
      <label>Fecha<input class="admin-input" type="date" name="date[]" value="<?=e($n['date'])?>"></label>
      <label>Etiqueta<input class="admin-input" name="tag[]" value="<?=e($n['tag'])?>"></label>
      <label>Formato<select name="format[]"><option value="plain" <?=$n['format']==='plain'?'selected':''?>>Texto plano</option><option value="bbcode" <?=$n['format']==='bbcode'?'selected':''?>>BBCode</option><option value="html" <?=$n['format']==='html'?'selected':''?>>HTML seguro</option></select></label>
    </div>
    <label>Título<input class="admin-input" name="title[]" value="<?=e($n['title'])?>"></label>
    <label>Imagen portada opcional<input class="admin-input" name="image[]" value="<?=e($n['image'])?>" placeholder="assets/media/noticia.jpg o https://..."></label>
    <label>Contenido<textarea name="content[]" rows="8"><?=e($n['content'])?></textarea></label>
  </div>
<?php endforeach; ?>
</div>
<button type="button" class="btn" onclick="addNewsItem()">➕ Agregar noticia</button>
<button>💾 Guardar noticias</button>
</form>
</div>

<div class="card">
<h3>📅 Editor visual de eventos</h3>
<form method="post" id="eventsForm">
<input type="hidden" name="action" value="save_events_form">
<div id="eventItems">
<?php foreach(array_values($events) as $i=>$ev): ?>
<div class="event-row"><input class="admin-input" name="event_emoji[]" value="<?=e($ev['emoji']??'✨')?>" placeholder="Emoji"><input class="admin-input" name="event_name[]" value="<?=e($ev['name']??'')?>" placeholder="Nombre del evento"><input class="admin-input" name="event_time[]" value="<?=e($ev['time']??'')?>" placeholder="Horario"><button type="button" class="danger" onclick="this.parentNode.remove()">X</button></div>
<?php endforeach; ?>
</div>
<button type="button" class="btn" onclick="addEventItem()">➕ Agregar evento</button>
<button>💾 Guardar eventos</button>
</form>
</div>

<div class="grid">
<form method="post" class="card"><h3>Noticias JSON avanzado</h3><input type="hidden" name="action" value="save_raw"><textarea name="news_json" rows="18"><?=e(json_encode($news,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))?></textarea><button>Guardar JSON noticias</button></form>
<form method="post" class="card"><h3>Eventos JSON avanzado</h3><input type="hidden" name="action" value="save_raw"><textarea name="events_json" rows="18"><?=e(json_encode($events,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))?></textarea><button>Guardar JSON eventos</button></form>
</div>

<script>
function addNewsItem(){
 const i=document.querySelectorAll('#newsItems .editor-item').length;
 const html=`<div class="editor-item"><div class="editor-head"><strong>📰 Nueva noticia</strong><button type="button" class="danger small" onclick="this.closest('.editor-item').remove()">Eliminar</button></div><input type="hidden" name="id[]" value=""><div class="grid"><label>Activa<br><input type="checkbox" name="enabled[${i}]" checked></label><label>Fijada 📌<br><input type="checkbox" name="pinned[${i}]"></label><label>Emoji<input class="admin-input" name="emoji[]" value="📰"></label><label>Fecha<input class="admin-input" type="date" name="date[]" value="<?=date('Y-m-d')?>"></label><label>Etiqueta<input class="admin-input" name="tag[]" value="Noticia"></label><label>Formato<select name="format[]"><option value="plain">Texto plano</option><option value="bbcode" selected>BBCode</option><option value="html">HTML seguro</option></select></label></div><label>Título<input class="admin-input" name="title[]" value="Nueva noticia"></label><label>Imagen portada opcional<input class="admin-input" name="image[]" placeholder="assets/media/noticia.jpg o https://..."></label><label>Contenido<textarea name="content[]" rows="8">[b]Título importante[/b]\nEscribe aquí tu noticia...</textarea></label></div>`;
 document.getElementById('newsItems').insertAdjacentHTML('beforeend',html);
}
function addEventItem(){document.getElementById('eventItems').insertAdjacentHTML('beforeend',`<div class="event-row"><input class="admin-input" name="event_emoji[]" value="✨" placeholder="Emoji"><input class="admin-input" name="event_name[]" placeholder="Nombre del evento"><input class="admin-input" name="event_time[]" placeholder="Horario"><button type="button" class="danger" onclick="this.parentNode.remove()">X</button></div>`)}
</script>
<?php admin_footer(); ?>
