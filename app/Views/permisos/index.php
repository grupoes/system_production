<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Permisos del Sistema</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Asigna y gestiona los accesos por cada rol de usuario.</p>
        </div>

    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-12 gap-6">

        <!-- Left Column: Roles/Perfiles + Usuarios -->
        <div class="col-span-12 lg:col-span-4 space-y-4">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full min-h-[600px]">

                <!-- Tabs Header -->
                <div class="flex border-b border-slate-50 bg-slate-100/50 p-1.5 m-4 rounded-[1.2rem]">
                    <button onclick="switchTab('perfiles')" id="tab-btn-perfiles" class="flex-1 py-2.5 text-xs font-black uppercase tracking-widest rounded-[0.9rem] transition-all bg-indigo-600 text-white shadow-lg shadow-indigo-100 cursor-pointer">
                        Perfiles
                    </button>
                    <button onclick="switchTab('usuarios')" id="tab-btn-usuarios" class="flex-1 py-2.5 text-xs font-black uppercase tracking-widest rounded-[0.9rem] transition-all text-slate-500 hover:text-slate-700 cursor-pointer">
                        Usuarios
                    </button>
                </div>

                <!-- Tab: Perfiles -->
                <div id="tab-content-perfiles" class="flex-1 flex flex-col overflow-hidden">
                    <div class="px-6 py-2 flex items-center justify-between">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Lista de Perfiles</h3>
                        <button onclick="openModal('modal_rol')" class="flex items-center gap-1 bg-indigo-50 text-indigo-600 text-[9px] px-2.5 py-1 rounded-full font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all active:scale-95 border border-indigo-100 shadow-sm shadow-indigo-100/20">
                            <i data-lucide="plus" class="w-2.5 h-2.5"></i>
                            Nuevo Rol
                        </button>
                    </div>

                    <div class="p-4 space-y-2 overflow-y-auto custom-scrollbar">
                        <?php
                        $roles = [
                            ['id' => 1, 'nombre' => 'Administrador Global', 'color' => 'bg-indigo-500', 'active' => true, 'desc' => 'Acceso total al sistema'],
                            ['id' => 2, 'nombre' => 'Supervisor de Ventas', 'color' => 'bg-emerald-500', 'active' => false, 'desc' => 'Gestión de equipo y reportes'],
                            ['id' => 3, 'nombre' => 'Operador Logístico', 'color' => 'bg-amber-500', 'active' => false, 'desc' => 'Control de inventario'],
                            ['id' => 4, 'nombre' => 'Atención al Cliente', 'color' => 'bg-rose-500', 'active' => false, 'desc' => 'Soporte y tickets'],
                        ];
                        foreach ($roles as $rol): ?>
                            <div class="group flex items-center justify-between p-4 rounded-2xl transition-all <?= $rol['active'] ? 'bg-slate-100/50 border border-slate-200 shadow-sm' : 'hover:bg-slate-50/50 border border-transparent' ?> cursor-pointer relative">

                                <div class="flex items-center gap-3">
                                    <?php if ($rol['active']): ?>
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-800"></div>
                                    <?php else: ?>
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                                    <?php endif; ?>

                                    <div>
                                        <span class="text-sm font-black block <?= $rol['active'] ? 'text-slate-900' : 'text-slate-600' ?>"><?= $rol['nombre'] ?></span>
                                        <span class="text-[10px] text-slate-500 font-bold"><?= $rol['desc'] ?></span>
                                    </div>
                                </div>

                                <div class="relative">
                                    <button onclick="toggleRoleMenu(event, 'menu-<?= $rol['id'] ?>')" class="p-2 hover:bg-white rounded-lg transition-colors border border-transparent hover:border-slate-100">
                                        <i data-lucide="more-horizontal" class="w-4 h-4 text-slate-400"></i>
                                    </button>

                                    <div id="menu-<?= $rol['id'] ?>" class="hidden absolute right-0 mt-2 w-40 bg-white border border-slate-100 rounded-xl shadow-xl z-20 py-2 animate-in fade-in zoom-in duration-200">
                                        <div class="px-4 py-1 border-b border-slate-50 mb-1">
                                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Opciones</p>
                                        </div>
                                        <button onclick="openModal('modal_rol')" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-2">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-indigo-500"></i> Editar Nombre
                                        </button>
                                        <div class="h-px bg-slate-50 my-1"></div>
                                        <button class="w-full text-left px-4 py-2 text-xs font-bold text-rose-500 hover:bg-rose-50 flex items-center gap-2">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tab: Usuarios -->
                <div id="tab-content-usuarios" class="hidden flex-1 flex flex-col overflow-hidden">
                    <div class="px-6 py-2 flex items-center justify-between">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Lista de Usuarios</h3>
                        <span class="bg-emerald-50 text-emerald-600 text-[9px] px-2 py-0.5 rounded-full font-bold">12 Activos</span>
                    </div>

                    <div class="p-4 space-y-2 overflow-y-auto custom-scrollbar">
                        <?php
                        $usuariosMock = [
                            ['name' => 'Juan Perez', 'role' => 'Administrador', 'email' => 'juan@sistema.com'],
                            ['name' => 'Maria Garcia', 'role' => 'Ventas', 'email' => 'maria@sistema.com'],
                            ['name' => 'Carlos Ruiz', 'role' => 'Logística', 'email' => 'carlos@sistema.com'],
                            ['name' => 'Ana Lopez', 'role' => 'Soporte', 'email' => 'ana@sistema.com'],
                        ];
                        foreach ($usuariosMock as $idx => $user): ?>
                            <div class="group flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 border border-transparent transition-all cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 border-2 border-white shadow-sm flex items-center justify-center text-slate-500 font-black text-xs">
                                        <?= substr($user['name'], 0, 2) ?>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold block text-slate-800"><?= $user['name'] ?></span>
                                        <span class="text-[10px] text-slate-600 font-bold"><?= $user['email'] ?></span>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black px-2 py-1 bg-slate-100 text-slate-700 rounded-lg uppercase"><?= $user['role'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Módulos y Permisos -->
        <div class="col-span-12 lg:col-span-8">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden h-full flex flex-col">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-white sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100">
                            <i data-lucide="key-round" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight">Matriz de Permisos</h3>
                            <p class="text-[11px] text-slate-600 mt-0.5 uppercase tracking-wider font-black">Editando: <span class="text-indigo-600">Administrador Global</span></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="px-4 py-2 text-slate-400 hover:text-slate-600 text-xs font-bold transition-all">Descartar</button>
                        <button class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-black hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center gap-2">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar bg-slate-50/30">
                    <!-- Modulo Loop -->
                    <?php
                    $modulos = [
                        'Configuración' => [
                            ['name' => 'Usuarios', 'icon' => 'users'],
                            ['name' => 'Roles', 'icon' => 'shield'],
                            ['name' => 'Permisos', 'icon' => 'lock'],
                            ['name' => 'Módulos', 'icon' => 'layout-grid']
                        ],
                        'Ventas' => [
                            ['name' => 'Nueva Venta', 'icon' => 'shopping-cart'],
                            ['name' => 'Listado de Boletas', 'icon' => 'file-text'],
                            ['name' => 'Notas de Crédito', 'icon' => 'undo-2'],
                            ['name' => 'Cierre de Caja', 'icon' => 'banknote']
                        ],
                        'Inventario' => [
                            ['name' => 'Productos', 'icon' => 'package'],
                            ['name' => 'Categorías', 'icon' => 'tags'],
                            ['name' => 'Stock Actual', 'icon' => 'archive'],
                            ['name' => 'Movimientos', 'icon' => 'arrow-left-right']
                        ],
                    ];
                    foreach ($modulos as $mod => $subs): ?>
                        <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm shadow-slate-100/50">
                            <!-- Header Modulo (Collapsible) -->
                            <div onclick="toggleModule(this)" class="flex items-center justify-between p-5 bg-white cursor-pointer hover:bg-slate-50/50 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all">
                                        <i data-lucide="layers" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-black text-slate-800 uppercase tracking-widest"><?= $mod ?></span>
                                        <span class="text-[10px] text-slate-600 block font-black"><?= count($subs) ?> submódulos configurables</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-all">Click para expandir</span>
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-300 transition-transform duration-300"></i>
                                </div>
                            </div>

                            <!-- Submodulos Container -->
                            <div class="p-6 bg-slate-50/30 border-t border-slate-50 grid grid-cols-1 md:grid-cols-2 gap-6 animate-in slide-in-from-top-2 duration-300">
                                <?php foreach ($subs as $sub): ?>
                                    <div class="bg-white p-5 rounded-[2rem] border border-slate-100 flex flex-col gap-5 hover:border-indigo-100 transition-all group/sub shadow-sm shadow-slate-100/50">
                                        <!-- Header: Nombre + Toggle Ver -->
                                        <div class="flex items-center justify-between">
                                            <span class="text-[13px] font-black text-slate-800 uppercase tracking-widest"><?= $sub['name'] ?></span>
                                            
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ver Acceso</span>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" class="sr-only peer" checked>
                                                    <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-500"></div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Acciones: Chips de Crear, Editar, Eliminar -->
                                        <div class="flex items-center gap-2 border-t border-slate-50 pt-4">
                                            <label class="cursor-pointer group/chip">
                                                <input type="checkbox" class="sr-only peer" checked>
                                                <div class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-100 bg-slate-50 text-slate-400 transition-all peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-lg peer-checked:shadow-indigo-100">
                                                    Crear
                                                </div>
                                            </label>
                                            <label class="cursor-pointer group/chip">
                                                <input type="checkbox" class="sr-only peer" checked>
                                                <div class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-100 bg-slate-50 text-slate-400 transition-all peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-lg peer-checked:shadow-indigo-100">
                                                    Editar
                                                </div>
                                            </label>
                                            <label class="cursor-pointer group/chip">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-100 bg-slate-50 text-slate-400 transition-all peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 peer-checked:shadow-lg peer-checked:shadow-rose-100">
                                                    Eliminar
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Nuevo/Editar Rol -->
    <div id="modal_rol" class="modal-backdrop">
        <div class="modal-container modal-md">
            <div class="modal-header">
                <h2 class="modal-title">Configurar Rol</h2>
                <button onclick="closeModal('modal_rol')" class="modal-close">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="#" method="POST">
                <div class="modal-body">
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label">Nombre del Perfil / Rol</label>
                            <input type="text" name="nombre_rol" placeholder="Ej: Supervisor de Ventas" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion_rol" rows="3" placeholder="Describe brevemente los alcances de este rol..." class="form-input resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('modal_rol')" class="btn-secondary transition-all">Cancelar</button>
                    <button type="submit" class="btn-primary transition-all">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>

<script>
    // Función para abrir modal
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Función para cerrar modal
    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Función para cambiar entre pestañas (Perfiles / Usuarios)
    function switchTab(tab) {
        const btnPerfiles = document.getElementById('tab-btn-perfiles');
        const btnUsuarios = document.getElementById('tab-btn-usuarios');
        const contentPerfiles = document.getElementById('tab-content-perfiles');
        const contentUsuarios = document.getElementById('tab-content-usuarios');

        if (tab === 'perfiles') {
            // Activar botón Perfiles
            btnPerfiles.classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            btnPerfiles.classList.remove('text-slate-500', 'hover:text-slate-700');
            
            // Desactivar botón Usuarios
            btnUsuarios.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            btnUsuarios.classList.add('text-slate-500', 'hover:text-slate-700');

            // Mostrar contenido Perfiles
            contentPerfiles.classList.remove('hidden');
            contentUsuarios.classList.add('hidden');
        } else {
            // Activar botón Usuarios
            btnUsuarios.classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            btnUsuarios.classList.remove('text-slate-500', 'hover:text-slate-700');

            // Desactivar botón Perfiles
            btnPerfiles.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            btnPerfiles.classList.add('text-slate-500', 'hover:text-slate-700');

            // Mostrar contenido Usuarios
            contentUsuarios.classList.remove('hidden');
            contentPerfiles.classList.add('hidden');
        }
    }

    // Toggle para el menú de 3 puntos de cada rol
    function toggleRoleMenu(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        const allMenus = document.querySelectorAll('[id^="menu-"]');

        allMenus.forEach(m => {
            if (m.id !== menuId) m.classList.add('hidden');
        });

        menu.classList.toggle('hidden');
    }

    // Toggle para colapsar módulos
    function toggleModule(header) {
        const container = header.nextElementSibling;
        const icon = header.querySelector('[data-lucide="chevron-down"]');

        container.classList.toggle('hidden');
        if (container.classList.contains('hidden')) {
            icon.style.transform = 'rotate(-90deg)';
        } else {
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Cerrar menús al hacer click fuera
    window.addEventListener('click', () => {
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    });
</script>

<?= $this->endSection() ?>