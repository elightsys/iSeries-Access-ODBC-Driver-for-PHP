<?php
/**
 * Minimal PDO ODBC connection test to IBM i (AS/400 / DB2).
 * Ensure your DSN exists in /etc/odbc.ini (or ~/.odbc.ini) and permissions are safe (chmod 600).
 */

$dsn  = 'odbc:DSN=MYIBMI'; // or 'odbc:Driver={IBM i Access ODBC Driver};System=your-host;...'
$user = 'YOUR_USERNAME';
$pass = 'YOUR_PASSWORD';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "Connected OK\n";

    // Example query — replace with a table you actually have access to
    $stmt = $pdo->query("SELECT CURRENT_DATE as today FROM SYSIBM.SYSDUMMY1");
    $row = $stmt->fetch();
    var_dump($row);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Connection failed: " . $e->getMessage();
}
