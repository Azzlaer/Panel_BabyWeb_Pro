<?php
require_once __DIR__ . '/_auth.php'; admin_require();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(isset($_POST['news_json'])){ $data=json_decode($_POST['news_json'],true); if(is_array($data)){json_file_write('news.json',$data);$msg='Noticias guardadas';} else $msg='JSON de noticias inválido'; }
 if(isset($_POST['events_json'])){ $data=json_decode($_POST['events_json'],true); if(is_array($data)){json_file_write('events.json',$data);$msg='Eventos guardados';} else $msg='JSON de eventos inválido'; }
}
$news=json_file_read('news.json', [['title'=>'Apertura oficial','date'=>date('Y-m-d'),'tag'=>'Noticia','content'=>'Bienvenido al continente de MU.']]);
$events=json_file_read('events.json', [['emoji'=>'🩸','name'=>'Blood Castle','time'=>'Cada 2 horas']]);
admin_header('📰 Noticias / Eventos'); if($msg):?><div class="alert"><?=e($msg)?></div><?php endif; ?>
<div class="grid"><form method="post" class="card"><h3>Noticias JSON</h3><textarea name="news_json" rows="18"><?=e(json_encode($news,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))?></textarea><button>Guardar noticias</button></form><form method="post" class="card"><h3>Eventos JSON</h3><textarea name="events_json" rows="18"><?=e(json_encode($events,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))?></textarea><button>Guardar eventos</button></form></div>
<?php admin_footer(); ?>
