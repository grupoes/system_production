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
                        <button onclick="openModalRol()" class="flex items-center gap-1 bg-indigo-50 text-indigo-600 text-[9px] px-2.5 py-1 rounded-full font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all active:scale-95 border border-indigo-100 shadow-sm shadow-indigo-100/20">
                            <i data-lucide="plus" class="w-2.5 h-2.5"></i>
                            Nuevo Rol
                        </button>
                    </div>

                    <div id="lista-roles" class="p-4 space-y-2 overflow-y-auto custom-scrollbar">
                        <!-- Roles cargados por JS -->
                        <div class="flex flex-col items-center justify-center py-8 text-slate-400 gap-2">
                            <i data-lucide="loader-2" class="w-6 h-6 animate-spin"></i>
                            <span class="text-xs font-bold">Cargando roles...</span>
                        </div>
                    </div>
                </div>

                <!-- Tab: Usuarios -->
                <div id="tab-content-usuarios" class="hidden flex-1 flex flex-col overflow-hidden">
                    <div class="px-6 py-2 flex items-center justify-between">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Lista de Usuarios</h3>
                        <span class="bg-emerald-50 text-emerald-600 text-[9px] px-2 py-0.5 rounded-full font-bold">Activos</span>
                    </div>

                    <div id="lista-usuarios-permisos" class="p-4 space-y-2 overflow-y-auto custom-scrollbar">
                        <!-- Usuarios cargados por JS o estáticos si no se requiere lógica aún -->
                        <p class="text-center py-8 text-slate-400 text-xs font-bold">Funcionalidad de usuarios próximamente</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Módulos y Permisos -->
        <div class="col-span-12 lg:col-span-8">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden h-full flex flex-col min-h-[600px]">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-white sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100">
                            <i data-lucide="key-round" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight">Matriz de Permisos</h3>
                            <p class="text-[11px] text-slate-600 mt-0.5 uppercase tracking-wider font-black">Editando: <span id="nombre-rol-editando" class="text-indigo-600">Seleccione un rol</span></p>
                        </div>
                    </div>
                </div>

                <div id="matrix-permisos" class="p-6 space-y-4 flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
                    <!-- Matrix cargada por JS -->
                    <div class="flex flex-col items-center justify-center h-full py-20 text-slate-400 gap-4">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                            <i data-lucide="shield-alert" class="w-8 h-8 opacity-20"></i>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-black text-slate-500 uppercase tracking-widest">Sin Selección</p>
                            <p class="text-xs font-medium text-slate-400 mt-1">Selecciona un rol del panel izquierdo para gestionar sus permisos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Nuevo/Editar Rol -->
<div id="modal_rol" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Perfil</h2>
            <button onclick="closeModal('modal_rol')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-rol" onsubmit="saveRol(event)">
            <input type="hidden" name="id_rol" id="id_rol">
            <div class="modal-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre del Perfil</label>
                    <input type="text" name="nombre" id="nombre_rol" class="form-input" placeholder="Ej: Administrador, Supervisor..." required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_rol')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-rol" class="btn-primary">Guardar Perfil</button>
            </div>
        </form>
    </div>
</div>

<script>
    let rolActivoId = null;

    function switchTab(tab) {
        const perfilesBtn = document.getElementById('tab-btn-perfiles');
        const usuariosBtn = document.getElementById('tab-btn-usuarios');
        const perfilesContent = document.getElementById('tab-content-perfiles');
        const usuariosContent = document.getElementById('tab-content-usuarios');

        if (tab === 'perfiles') {
            perfilesBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            perfilesBtn.classList.remove('text-slate-500', 'hover:text-slate-700');
            usuariosBtn.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            usuariosBtn.classList.add('text-slate-500', 'hover:text-slate-700');
            perfilesContent.classList.remove('hidden');
            usuariosContent.classList.add('hidden');
        } else {
            usuariosBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            usuariosBtn.classList.remove('text-slate-500', 'hover:text-slate-700');
            perfilesBtn.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            perfilesBtn.classList.add('text-slate-500', 'hover:text-slate-700');
            usuariosContent.classList.remove('hidden');
            perfilesContent.classList.add('hidden');
        }
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function toggleRoleMenu(event, id) {
        event.stopPropagation();
        const menu = document.getElementById(id);
        const isHidden = menu.classList.contains('hidden');
        
        // Cerrar todos los menús primero
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
        
        if (isHidden) {
            menu.classList.remove('hidden');
        }
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    });

    // --- LOGICA DE ROLES (PERFILES) ---

    function loadRoles() {
        const container = document.getElementById('lista-roles');
        fetch(`${window.location.origin}/permisos/roles`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderRoles(res.data);
                }
            })
            .catch(err => console.error('Error al cargar roles:', err));
    }

    function renderRoles(roles) {
        const container = document.getElementById('lista-roles');
        if (roles.length === 0) {
            container.innerHTML = '<p class="text-center py-8 text-slate-400 text-xs">No hay roles registrados</p>';
            return;
        }

        let html = '';
        roles.forEach(rol => {
            const isActive = rolActivoId == rol.id;
            html += `
                <div onclick="selectRol(${rol.id}, '${rol.nombre}')" 
                     class="group flex items-center justify-between p-4 rounded-2xl transition-all ${isActive ? 'bg-slate-100 border border-slate-200 shadow-sm' : 'hover:bg-slate-50/50 border border-transparent'} cursor-pointer relative">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full ${isActive ? 'bg-indigo-600' : 'bg-slate-200'}"></div>
                        <div>
                            <span class="text-sm font-black block ${isActive ? 'text-slate-900' : 'text-slate-600'}">${rol.nombre}</span>
                            <span class="text-[10px] text-slate-500 font-bold">Gestionar permisos de este rol</span>
                        </div>
                    </div>
                    <div class="relative">
                        <button onclick="toggleRoleMenu(event, 'menu-${rol.id}')" class="p-2 hover:bg-white rounded-lg transition-colors border border-transparent hover:border-slate-100">
                            <i data-lucide="more-horizontal" class="w-4 h-4 text-slate-400"></i>
                        </button>
                        <div id="menu-${rol.id}" class="hidden absolute right-0 mt-2 w-40 bg-white border border-slate-100 rounded-xl shadow-xl z-20 py-2 animate-in fade-in zoom-in duration-200">
                            <button onclick="event.stopPropagation(); openModalRol(${rol.id})" class="w-full text-left px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-2">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-indigo-500"></i> Editar Nombre
                            </button>
                            <div class="h-px bg-slate-50 my-1"></div>
                            <button onclick="event.stopPropagation(); deleteRol(${rol.id}, '${rol.nombre}')" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-500 hover:bg-rose-50 flex items-center gap-2">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalRol(id = null) {
        const form = document.getElementById('form-rol');
        form.reset();
        document.getElementById('id_rol').value = id || '';
        document.querySelector('.modal-title').innerText = id ? 'Editar Perfil' : 'Nuevo Perfil';

        if (id) {
            fetch(`${window.location.origin}/permisos/rol/${id}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        document.getElementById('nombre_rol').value = res.data.nombre;
                        openModal('modal_rol');
                    }
                });
        } else {
            openModal('modal_rol');
        }
    }

    function saveRol(e) {
        e.preventDefault();
        const form = document.getElementById('form-rol');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-rol');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        fetch(`${window.location.origin}/permisos/save-rol`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_rol');
                loadRoles();
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function deleteRol(id, nombre) {
        showConfirm('¿Eliminar Rol?', `¿Estás seguro de eliminar el rol <b>${nombre}</b>?`, 'Sí, eliminar', () => {
            fetch(`${window.location.origin}/permisos/delete-rol/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        if (rolActivoId == id) {
                            rolActivoId = null;
                            document.getElementById('nombre-rol-editando').innerText = 'Seleccione un rol';
                            document.getElementById('matrix-permisos').innerHTML = `
                                <div class="flex flex-col items-center justify-center h-full py-20 text-slate-400 gap-4">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="shield-alert" class="w-8 h-8 opacity-20"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-black text-slate-500 uppercase tracking-widest">Sin Selección</p>
                                        <p class="text-xs font-medium text-slate-400 mt-1">Selecciona un rol del panel izquierdo para gestionar sus permisos.</p>
                                    </div>
                                </div>
                            `;
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        }
                        loadRoles();
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    // --- LOGICA DE PERMISOS (MATRIX) ---

    function selectRol(id, nombre) {
        rolActivoId = id;
        document.getElementById('nombre-rol-editando').innerText = nombre;
        renderRoles([]); // Re-render para mostrar activo sin fetch extra si se desea, o simplemente fetch
        loadRoles(); // Para actualizar visualmente el activo
        loadMatrix(id);
    }

    function loadMatrix(rolId) {
        const container = document.getElementById('matrix-permisos');
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full py-20 text-slate-400 gap-4">
                <i data-lucide="loader-2" class="w-8 h-8 animate-spin text-indigo-500"></i>
                <p class="text-xs font-bold uppercase tracking-widest">Cargando matriz...</p>
            </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`${window.location.origin}/permisos/matrix/${rolId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderMatrix(res.data);
                }
            })
            .catch(err => console.error('Error al cargar matriz:', err));
    }

    function renderMatrix(data) {
        const container = document.getElementById('matrix-permisos');
        if (data.length === 0) {
            container.innerHTML = '<p class="text-center py-8 text-slate-400 text-xs font-bold">No hay módulos configurados</p>';
            return;
        }

        let html = '';
        data.forEach(padre => {
            if (padre.submodulos.length === 0) return;

            html += `
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-indigo-600">
                                <i data-lucide="${padre.icono || 'box'}" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-black text-slate-900 uppercase tracking-widest">${padre.modulo}</span>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        ${padre.submodulos.map(sub => {
                            // Separar la acción "VER" o "LEER"
                            const accionVer = sub.acciones.find(a => 
                                a.nombre_accion.toUpperCase() === 'VER' || 
                                a.nombre_accion.toUpperCase() === 'LEER' ||
                                a.nombre_accion.toUpperCase() === 'LISTAR'
                            );
                            const otrasAcciones = sub.acciones.filter(a => a !== accionVer);

                            return `
                                <div class="bg-slate-50/50 p-5 rounded-[2.5rem] border border-slate-100 flex flex-col gap-5 hover:border-indigo-100 transition-all group/sub bg-white shadow-sm">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                        <span class="text-[13px] font-black text-slate-800 uppercase tracking-widest">${sub.modulo}</span>
                                        
                                        ${accionVer ? `
                                            <label class="relative inline-flex items-center cursor-pointer group/ver">
                                                <input type="checkbox" 
                                                       class="sr-only peer" 
                                                       ${accionVer.tiene_permiso ? 'checked' : ''} 
                                                       onchange="togglePermiso(${rolActivoId}, ${sub.id}, ${accionVer.accion_id}, this)">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                <span class="ml-2 text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover/ver:text-indigo-600 transition-colors">${accionVer.nombre_accion}</span>
                                            </label>
                                        ` : ''}
                                    </div>

                                    <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                                        ${otrasAcciones.map(acc => `
                                            <label class="flex items-center gap-3 cursor-pointer group/check">
                                                <div class="relative flex items-center">
                                                    <input type="checkbox" 
                                                           ${acc.tiene_permiso ? 'checked' : ''} 
                                                           onchange="togglePermiso(${rolActivoId}, ${sub.id}, ${acc.accion_id}, this)"
                                                           class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest group-hover/check:text-slate-900 transition-colors">${acc.nombre_accion}</span>
                                                </div>
                                            </label>
                                        `).join('')}
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function togglePermiso(rolId, moduloId, accionId, checkbox) {
        const estado = checkbox.checked;

        const formData = new FormData();
        formData.append('rol_id', rolId);
        formData.append('modulo_id', moduloId);
        formData.append('accion_id', accionId);
        formData.append('estado', estado);

        fetch(`${window.location.origin}/permisos/toggle`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success') {
                showToast('Error al actualizar permiso', 'error');
                checkbox.checked = !estado; // revertir
            }
        })
        .catch(err => {
            console.error(err);
            checkbox.checked = !estado; // revertir
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadRoles();
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #e2e8f0;
    }
</style>

<?= $this->endSection() ?>