<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = Config\Services::codeigniter();
$app->initialize();
$db = \Config\Database::connect();
print_r($db->table('tarea')->orderBy('id', 'DESC')->limit(1)->get()->getResultArray());
