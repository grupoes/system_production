<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('usuarios', 'Usuarios::index');

$routes->get('permisos', 'Permisos::index');

$routes->get('modulos', 'Modulos::index');

$routes->get('acciones', 'Acciones::index');
$routes->get('configuracion-acciones-modulos', 'Acciones::configuracionAccionesModulos');

$routes->get('universidades', 'Universidad::index');
$routes->get('carreras', 'Carreras::index');
$routes->get('feriados', 'Feriados::index');
$routes->get('origen_contactos', 'OrigenContactos::index');
$routes->get('nivel_academico', 'NivelAcademico::index');
