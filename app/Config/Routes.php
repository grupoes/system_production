<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->post('/', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('home', 'Home::index');
$routes->get('consultas/documento/(:segment)/(:segment)', 'Consultas::documento/$1/$2');

$routes->get('usuarios', 'Usuarios::index');
$routes->post('usuarios/save', 'Usuarios::save');
$routes->get('usuarios/list', 'Usuarios::getUsuarios');
$routes->get('usuarios/get/(:num)', 'Usuarios::getUsuario/$1');
$routes->post('usuarios/delete/(:num)', 'Usuarios::delete/$1');

$routes->get('permisos', 'Permisos::index');

$routes->get('modulos', 'Modulos::index');
$routes->get('modulos/list', 'Modulos::getModulos');
$routes->get('modulos/get/(:num)', 'Modulos::getModulo/$1');
$routes->get('modulos/getPadres', 'Modulos::getPadres');
$routes->post('modulos/save', 'Modulos::save');
$routes->post('modulos/delete/(:num)', 'Modulos::delete/$1');

$routes->get('acciones', 'Acciones::index');
$routes->get('acciones/list', 'Acciones::getAcciones');
$routes->get('acciones/get/(:num)', 'Acciones::getAccion/$1');
$routes->post('acciones/save', 'Acciones::save');
$routes->post('acciones/delete/(:num)', 'Acciones::delete/$1');
$routes->get('acciones/modulos-configuracion', 'Acciones::getModulosConfiguracion');
$routes->get('acciones/hijos-de-modulo/(:num)', 'Acciones::getHijosDeModulo/$1');
$routes->get('acciones/acciones-de-modulo/(:num)', 'Acciones::getAccionesDeModulo/$1');
$routes->post('acciones/save-acciones-modulo', 'Acciones::saveAccionesModulo');
$routes->get('configuracion-acciones-modulos', 'Acciones::configuracionAccionesModulos');

$routes->get('permisos', 'Permisos::index');
$routes->get('permisos/roles', 'Permisos::getRoles');
$routes->get('permisos/rol/(:num)', 'Permisos::getRol/$1');
$routes->post('permisos/save-rol', 'Permisos::saveRol');
$routes->post('permisos/delete-rol/(:num)', 'Permisos::deleteRol/$1');
$routes->get('permisos/matrix/(:num)', 'Permisos::getMatrixPermisos/$1');
$routes->post('permisos/toggle', 'Permisos::togglePermiso');

$routes->get('universidades', 'Universidad::index');
$routes->get('carreras', 'Carreras::index');
$routes->get('feriados', 'Feriados::index');
$routes->get('origen_contactos', 'OrigenContactos::index');
$routes->get('nivel_academico', 'NivelAcademico::index');
