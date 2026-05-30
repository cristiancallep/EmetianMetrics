<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$dsn = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $sqlFiles = glob(__DIR__ . '/*.sql');
    sort($sqlFiles, SORT_STRING);

    foreach ($sqlFiles as $filePath) {
        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new RuntimeException('No se pudo leer el archivo de migración: ' . $filePath);
        }
        $pdo->exec($sql);
        echo "Ejecutado: " . basename($filePath) . "\n";
    }

    echo "Migración completada correctamente.\n";
} catch (Throwable $exception) {
    echo 'Error al ejecutar la migración: ' . $exception->getMessage() . "\n";
    exit(1);
}
