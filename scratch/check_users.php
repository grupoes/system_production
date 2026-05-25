<?php

// Leer el archivo .env manualmente
$lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    $line = trim($line);
    if (strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim($parts[1]);
    }
}

$host = trim($env['database.default.hostname'] ?? 'localhost', "'\"");
$dbName = trim($env['database.default.database'] ?? 'control_produccion', "'\"");
$user = trim($env['database.default.username'] ?? 'postgres', "'\"");
$pass = trim($env['database.default.password'] ?? '', "'\"");
$port = trim($env['database.default.port'] ?? '5432', "'\"");

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbName";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // Consulta de usuarios activos
    $stmt = $pdo->query("
        SELECT u.id, (p.nombres || ' ' || p.apellidos) as nombre 
        FROM usuarios u
        JOIN personas p ON p.id = u.persona_id
        WHERE u.estado = true
        ORDER BY p.nombres ASC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "DB ACTIVE USERS:\n";
    foreach ($users as $u) {
        echo "- ID: {$u['id']}, Nombre: '{$u['nombre']}'\n";
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
