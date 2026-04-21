<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
define('ENVIRONMENT', 'development'); // Fix for the previous error
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = Config\Services::codeigniter();
$app->initialize();
$db = \Config\Database::connect();
$query = $db->query("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'tarea'");
print_r($query->getResultArray());
