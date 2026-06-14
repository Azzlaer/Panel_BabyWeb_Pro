<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');
echo "MU Register Enterprise Pro - DB Test\n";
echo "DB_DRIVER configurado: " . DB_DRIVER . "\n";
echo "sqlsrv disponible: " . (function_exists('sqlsrv_connect') ? 'SI' : 'NO') . "\n";
echo "ODBC disponible: " . (function_exists('odbc_connect') ? 'SI' : 'NO') . "\n\n";
try {
    $db = db();
    echo "Conexion OK usando: " . $db->driver . "\n";
    echo "Total cuentas MEMB_INFO: " . $db->scalar('SELECT COUNT(*) FROM MEMB_INFO') . "\n";
} catch (Throwable $e) {
    echo "ERROR:\n" . $e->getMessage() . "\n";
}
