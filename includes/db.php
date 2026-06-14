<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';

class MuDb
{
    public string $driver;
    private $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $wanted = DB_DRIVER;
        if ($wanted === 'auto') {
            if (function_exists('sqlsrv_connect')) $wanted = 'sqlsrv';
            elseif (function_exists('odbc_connect')) $wanted = 'odbc';
            else throw new Exception('No esta habilitado sqlsrv ni odbc en PHP.');
        }
        if ($wanted === 'sqlsrv') $this->connectSqlsrv();
        elseif ($wanted === 'odbc') $this->connectOdbc();
        else throw new Exception('DB_DRIVER invalido. Usa auto, sqlsrv u odbc.');
        $this->driver = $wanted;
    }

    private function connectSqlsrv(): void
    {
        if (!function_exists('sqlsrv_connect')) throw new Exception('La extension sqlsrv no esta habilitada.');
        $server = DB_HOST . (DB_PORT !== '' ? ',' . DB_PORT : '');
        $opts = ['Database'=>DB_NAME,'Uid'=>DB_USER,'PWD'=>DB_PASS,'CharacterSet'=>'UTF-8','TrustServerCertificate'=>true];
        $this->conn = sqlsrv_connect($server, $opts);
        if (!$this->conn) throw new Exception('No se pudo conectar por sqlsrv: ' . print_r(sqlsrv_errors(), true));
    }

    private function connectOdbc(): void
    {
        if (!function_exists('odbc_connect')) throw new Exception('La extension ODBC no esta habilitada en PHP.');
        if (DB_ODBC_DSN !== '') {
            $dsn = DB_ODBC_DSN;
        } else {
            $server = DB_HOST . (DB_PORT !== '' ? ',' . DB_PORT : '');
            $encrypt = DB_ODBC_ENCRYPT ? 'yes' : 'no';
            $trust = DB_ODBC_TRUST_CERTIFICATE ? 'yes' : 'no';
            $dsn = 'Driver={' . DB_ODBC_DRIVER . '};Server=' . $server . ';Database=' . DB_NAME . ';Encrypt=' . $encrypt . ';TrustServerCertificate=' . $trust . ';';
        }
        $this->conn = @odbc_connect($dsn, DB_USER, DB_PASS);
        if (!$this->conn) throw new Exception('No se pudo conectar por ODBC: ' . odbc_errormsg());
    }

    public function query(string $sql, array $params = [])
    {
        if ($this->driver === 'sqlsrv') {
            $stmt = sqlsrv_query($this->conn, $sql, $params);
            if ($stmt === false) throw new Exception(print_r(sqlsrv_errors(), true));
            return $stmt;
        }
        $stmt = odbc_prepare($this->conn, $sql);
        if (!$stmt) throw new Exception(odbc_errormsg($this->conn));
        if (!odbc_execute($stmt, $params)) throw new Exception(odbc_errormsg($this->conn));
        return $stmt;
    }

    public function fetchAssoc($stmt): ?array
    {
        if ($this->driver === 'sqlsrv') {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            return $row ?: null;
        }
        $row = odbc_fetch_array($stmt);
        return $row ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        $rows = [];
        while ($row = $this->fetchAssoc($stmt)) $rows[] = $row;
        return $rows;
    }

    public function scalar(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        $row = $this->fetchAssoc($stmt);
        if (!$row) return null;
        return array_values($row)[0] ?? null;
    }
}

function db(): MuDb { return new MuDb(); }
