# SQLite https://www.sqlite.org/foreignkeys.html
EL caso es que en la eliminación en ON DELETE CASCADE
PROBLEMAS POR NO SETEAR PRAGMA foreign_keys = ON

try {
    $dsn = 'sqlite:C:\Users\neo\Desktop\db.db'; // ruta absoluta
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
