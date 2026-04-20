<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldData('notificaciones');

foreach ($fields as $field) {
    echo $field->name . ' (' . $field->type . ') - Length: ' . $field->max_length . PHP_EOL;
}
