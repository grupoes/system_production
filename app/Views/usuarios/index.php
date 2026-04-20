<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Usuarios</h1>
            <p class="text-sm text-slate-500 mt-1">Gestión centralizada de cuentas y accesos.</p>
        </div>
        <button onclick="openModalUser()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Usuario
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Buscar usuarios..." class="table-search-input">
            </div>
            <div class="flex items-center gap-2">
                <!-- Records per Page Selector -->
                <div class="relative">
                    <select id="limitSelect" class="table-action-btn appearance-none pr-8 bg-slate-50/50 border border-slate-100 rounded-lg outline-none cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <i data-lucide="chevrons-up-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-2.5 h-2.5 text-slate-400 pointer-events-none"></i>
                </div>

                <!-- Filter Container -->
                <div class="relative">
                    <button onclick="toggleFilter(event)" class="table-action-btn">
                        <span>Filtrar</span>
                        <i data-lucide="filter" class="w-3 h-3 text-slate-400"></i>
                    </button>

                    <!-- Filter Dropdown Menu -->
                    <div id="filter-dropdown" class="filter-dropdown">
                        <div class="mb-4">
                            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Opciones de Filtro</h3>
                            <p class="text-[10px] text-slate-400 mt-1">Refina la lista de usuarios</p>
                        </div>

                        <div class="space-y-4">
                            <div class="form-group mb-0">
                                <label class="form-label text-[10px]">Rol del Sistema</label>
                                <select class="form-input py-2 px-4 text-[12px] rounded-xl">
                                    <option>Todos los roles</option>
                                    <option>Administrador</option>
                                    <option>Usuario Estándar</option>
                                    <option>Operador</option>
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label class="form-label text-[10px]">Estado de Cuenta</label>
                                <div class="flex gap-2">
                                    <button type="button" onclick="selectStatus(this)" class="filter-pill active">Activos</button>
                                    <button type="button" onclick="selectStatus(this)" class="filter-pill">Inactivos</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between gap-3">
                            <button onclick="toggleFilter(event)" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 transition-colors">Limpiar</button>
                            <button onclick="toggleFilter(event)" class="flex-1 py-2.5 bg-indigo-600 text-white rounded-xl text-[10px] font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">Aplicar Filtros</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid Table -->
        <div class="table-grid-wrapper">
            <table class="table-grid">
                <thead>
                    <tr>
                        <th class="table-th w-10 text-center">#</th>
                        <th class="table-th">Documento <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Nombres <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Rol <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Correo <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody">
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="loader-2" class="w-8 h-8 text-indigo-500 animate-spin mb-3"></i>
                                <p class="text-sm font-bold text-slate-700">Cargando usuarios...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="table-footer">
            <p id="pagination-info" class="table-info-text">Mostrando resultados...</p>

            <div id="pagination-controls" class="flex items-center gap-1.5">
                <!-- Se llenará vía JS -->
            </div>
        </div>
    </div>

    <!-- Standardized Modal: Nuevo Usuario -->
    <div id="modal_usuario" class="modal-backdrop">
        <div class="modal-container modal-2xl">
            <div class="modal-header">
                <h2 id="modal-title-user" class="modal-title">Registrar Nuevo Usuario</h2>
                <button onclick="closeModalUser()" class="modal-close">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="form-usuario" action="<?= base_url('usuarios/save') ?>" method="POST">
                <input type="hidden" name="id_usuario" id="id_usuario" value="">
                <input type="hidden" name="id_persona" id="id_persona" value="">
                <div class="modal-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Tipo de Documento</label>
                            <select id="tipo_doc" name="tipo_doc" class="form-input">
                                <option value="DNI">DNI</option>
                                <option value="CE">Carnet de Extranjería</option>
                                <option value="PASSPORT">Pasaporte</option>
                            </select>
                        </div>
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Nro. de Documento</label>
                            <div class="relative">
                                <input id="num_doc" type="text" name="num_doc" placeholder="Ej: 77123456" class="form-input pr-14">
                                <button id="btn-search-dni" type="button" class="absolute right-0 top-0 h-full w-12 flex items-center justify-center bg-indigo-600 text-white rounded-r-2xl hover:bg-indigo-700 transition-all active:scale-95 cursor-pointer">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Nombres</label>
                            <input id="nombre" type="text" name="nombre" placeholder="Nombres" class="form-input">
                        </div>

                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Apellidos</label>
                            <input id="apellidos" type="text" name="apellidos" placeholder="Apellidos" class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-input">
                        </div>

                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" placeholder="Teléfono" class="form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" placeholder="Ciudad" class="form-input">
                        </div>

                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Rol del Sistema</label>
                            <select name="rol" class="form-input">
                                <option value="">Seleccionar rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>"><?= $rol['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Tipo de Jornada -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2">
                            <label class="form-label">Tipo de Jornada</label>
                            <select id="tipo_jornada" name="tipo_jornada" class="form-input">
                                <option value="">Seleccionar jornada...</option>
                                <?php foreach ($tiposJornada as $tj): ?>
                                    <option value="<?= $tj['id'] ?>" data-nombre="<?= strtoupper($tj['nombre_jornada']) ?>"><?= $tj['nombre_jornada'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Horario Informativo (Condicional) -->
                    <div id="container-horario" class="hidden">
                        <div class="bg-slate-50 border border-slate-100 rounded-[2.5rem] p-6 mb-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
                                    <i data-lucide="clock-cog" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 tracking-tight">Configuración de Cronograma Semanal</h3>
                                    <p class="text-[11px] text-slate-500">Define los turnos específicos para cada día de la semana</p>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-x-auto">
                                <table class="w-full text-left border-collapse min-w-[650px]">
                                    <thead class="bg-slate-50/80">
                                        <tr>
                                            <th class="p-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Día</th>
                                            <th class="p-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center bg-indigo-50/30" colspan="2">Turno Mañana</th>
                                            <th class="p-3 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center bg-emerald-50/30" colspan="2">Turno Tarde</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                        foreach ($dias as $dia): 
                                            $diaLower = strtolower($dia);
                                            // Por defecto, sábado tarde desactivado
                                            $defaultActive2 = ($dia !== 'Sábado');
                                        ?>
                                        <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/30 transition-colors">
                                            <td class="p-3 align-middle">
                                                <span class="text-[11px] font-bold text-slate-700"><?= $dia ?></span>
                                            </td>
                                            
                                            <!-- Turno Mañana -->
                                            <td class="p-2 border-r border-slate-50">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-1.5 mb-1">
                                                        <input type="checkbox" name="active1_<?= $diaLower ?>" checked class="shift-toggle w-3 h-3 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                                        <span class="text-[9px] font-bold text-slate-400 uppercase">Habilitar</span>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2 shift-container">
                                                        <div class="space-y-1">
                                                            <label class="text-[8px] font-black text-slate-400 uppercase ml-1">Inicio</label>
                                                            <input type="time" name="start1_<?= $diaLower ?>" value="08:00" class="form-input py-1.5 px-2 text-[11px] text-center border-slate-200">
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-[8px] font-black text-slate-400 uppercase ml-1">Salida</label>
                                                            <input type="time" name="end1_<?= $diaLower ?>" value="13:00" class="form-input py-1.5 px-2 text-[11px] text-center border-slate-200">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hidden"></td> <!-- Spacer for colspan compatibility if needed, but we used 2 cols in header -->

                                            <!-- Turno Tarde -->
                                            <td class="p-2" colspan="2">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-1.5 mb-1">
                                                        <input type="checkbox" name="active2_<?= $diaLower ?>" <?= $defaultActive2 ? 'checked' : '' ?> class="shift-toggle w-3 h-3 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                                        <span class="text-[9px] font-bold text-slate-400 uppercase">Habilitar</span>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2 shift-container <?= !$defaultActive2 ? 'opacity-40 pointer-events-none' : '' ?>">
                                                        <div class="space-y-1">
                                                            <label class="text-[8px] font-black text-slate-400 uppercase ml-1">Inicio</label>
                                                            <input type="time" name="start2_<?= $diaLower ?>" value="15:00" <?= !$defaultActive2 ? 'disabled' : '' ?> class="form-input py-1.5 px-2 text-[11px] text-center border-slate-200">
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-[8px] font-black text-slate-400 uppercase ml-1">Salida</label>
                                                            <input type="time" name="end2_<?= $diaLower ?>" value="19:00" <?= !$defaultActive2 ? 'disabled' : '' ?> class="form-input py-1.5 px-2 text-[11px] text-center border-slate-200">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Correo</label>
                            <input type="email" name="email" placeholder="Correo electrónico" class="form-input">
                        </div>
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Contraseña <span class="text-[10px] text-slate-400 font-normal ml-1">(Dejar en blanco para mantener actual)</span></label>
                            <input type="password" id="password" name="password" placeholder="••••••••" class="form-input">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModalUser()" class="btn-secondary transition-all">Cancelar</button>
                    <button type="submit" id="btn-save-user" class="btn-primary transition-all flex items-center justify-center gap-2">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('js/pages/usuarios/lista.js') ?>"></script>
<?= $this->endSection() ?>