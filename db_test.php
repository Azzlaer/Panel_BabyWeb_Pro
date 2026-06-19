<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');
echo "MU Register Enterprise Pro - DB Test
";
echo "DB_DRIVER configurado: " . DB_DRIVER . "
";
echo "DB_NAME configurado: " . DB_NAME . "
";
echo "MEMB_INFO_PROFILE: " . (defined('MEMB_INFO_PROFILE') ? MEMB_INFO_PROFILE : 'standard') . "
";
echo "sqlsrv disponible: " . (function_exists('sqlsrv_connect') ? 'SI' : 'NO') . "
";
echo "ODBC disponible: " . (function_exists('odbc_connect') ? 'SI' : 'NO') . "

";
try {
    $db = db();
    echo "Conexion OK usando: " . $db->driver . "
";
    echo "Total cuentas MEMB_INFO: " . $db->scalar('SELECT COUNT(*) FROM MEMB_INFO') . "
";
    echo "Columnas esperadas para registro standard:
";
    echo "memb___id, memb__pwd, memb_name, sno__numb, mail_addr, appl_days, modi_days, true_days, mail_chek, bloc_code, ctl1_code, AccountLevel, AccountExpireDate

";
    $missing = [];
    $cols = ['memb___id','memb__pwd','memb_name','sno__numb','mail_addr','appl_days','modi_days','true_days','mail_chek','bloc_code','ctl1_code','AccountLevel','AccountExpireDate'];
    foreach ($cols as $col) {
        $exists = (int)$db->scalar("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='MEMB_INFO' AND COLUMN_NAME=?", [$col]);
        if ($exists < 1) $missing[] = $col;
    }
    if ($missing) echo "Faltan columnas: " . implode(', ', $missing) . "
";
    else echo "Estructura MEMB_INFO standard OK.
";
} catch (Throwable $e) {
    echo "ERROR:
" . $e->getMessage() . "
";
}
