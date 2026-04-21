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
$routes->get('universidades/all', 'Universidad::getAll');
$routes->get('universidades/list', 'Universidad::getUniversidades');
$routes->get('universidades/get/(:num)', 'Universidad::getUniversidad/$1');
$routes->post('universidades/save', 'Universidad::save');
$routes->post('universidades/delete/(:num)', 'Universidad::delete/$1');
$routes->get('carreras', 'Carreras::index');
$routes->get('carreras/list', 'Carreras::getCarreras');
$routes->get('carreras/get/(:num)', 'Carreras::getCarrera/$1');
$routes->post('carreras/save', 'Carreras::save');
$routes->post('carreras/delete/(:num)', 'Carreras::delete/$1');
$routes->get('feriados', 'Feriados::index');
$routes->get('feriados/list', 'Feriados::getList');
$routes->get('feriados/get/(:num)', 'Feriados::get/$1');
$routes->post('feriados/save', 'Feriados::save');
$routes->post('feriados/delete/(:num)', 'Feriados::delete/$1');
$routes->get('origen_contactos', 'OrigenContactos::index');
$routes->get('origen_contactos/list', 'OrigenContactos::getList');
$routes->get('origen_contactos/get/(:num)', 'OrigenContactos::get/$1');
$routes->post('origen_contactos/save', 'OrigenContactos::save');
$routes->post('origen_contactos/delete/(:num)', 'OrigenContactos::delete/$1');
$routes->get('nivel_academico', 'NivelAcademico::index');
$routes->get('nivel_academico/list', 'NivelAcademico::getList');
$routes->get('nivel_academico/get/(:num)', 'NivelAcademico::get/$1');
$routes->post('nivel_academico/save', 'NivelAcademico::save');
$routes->post('nivel_academico/delete/(:num)', 'NivelAcademico::delete/$1');

$routes->get('lista-tareas', 'Tareas::index');
$routes->get('lista-tareas/list', 'Tareas::getList');
$routes->get('lista-tareas/get/(:num)', 'Tareas::get/$1');
$routes->post('lista-tareas/save', 'Tareas::save');
$routes->post('lista-tareas/delete/(:num)', 'Tareas::delete/$1');
$routes->get('lista-tareas/roles', 'Tareas::getRoles');
$routes->get('lista-tareas/tipos', 'Tareas::getTipos');

$routes->get('turnos-ventas', 'TurnosVentas::index');
$routes->get('turnos-ventas/dias', 'TurnosVentas::getDias');
$routes->get('turnos-ventas/asignaciones/(:num)', 'TurnosVentas::getAsignaciones/$1');
$routes->get('turnos-ventas/usuarios', 'TurnosVentas::getUsuarios');
$routes->post('turnos-ventas/save', 'TurnosVentas::saveAsignacion');
$routes->post('turnos-ventas/remove/(:num)', 'TurnosVentas::removeAsignacion/$1');

$routes->get('control-carga', 'ControlCarga::index');
$routes->get('control-carga/pending-activities', 'ControlCarga::getPendingActivities');
$routes->get('control-carga/users', 'ControlCarga::getUsers');
$routes->get('control-carga/user-schedule', 'ControlCarga::getUserSchedule');

$routes->get('registrar-potencial-cliente', 'Prospectos::registro');
$routes->get('lista-potenciales-clientes', 'Prospectos::index');
$routes->get('lista-potenciales-clientes/list', 'Prospectos::getList');
$routes->get('prospectos/data-form', 'Prospectos::getDataForm');
$routes->get('prospectos/carreras/(:num)', 'Prospectos::getCarreras/$1');
$routes->post('prospectos/save', 'Prospectos::save');
$routes->post('prospectos/save-carrera', 'Prospectos::saveCarrera');
