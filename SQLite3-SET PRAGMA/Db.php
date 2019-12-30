<?php

class Db {

    private $conn = null;
    private static $_instance = null;

    private function __construct() {
        try {
            // $config = Config::getConfig('mysql');
            $dsn = 'sqlite:C:\Users\neo\Desktop\verificacion\test.db'; // ruta absoluta
            // $dsn = 'mysql:host=localhost;dbname=db;charset=utf8mb4';
            $this->conn = new PDO($dsn, '', '', array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ));
            $this->conn->exec('PRAGMA foreign_keys = ON;');
        } catch (PDOException $e) {
            trigger_error('Error:' . $e->getMessage(), E_USER_ERROR);
            exit;
        }
    }

    // Devolvemos la conexión
    private function getConnection() {
        return $this->conn;
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone() {}
    
    // close db connection
    public function __destruct() {
        $this->conn = null;
    }

    // Instanciamos la clase
    public static function getInstance() {
        //si no esta inicializada y no es distinta de NULL
        if(!isset(self::$_instance)) {
            self::$_instance = new Db();
        }
        // Despues de instanciar la clase, llamamos al método que devuelve la conexión.
        return self::$_instance->getConnection();
    }
}