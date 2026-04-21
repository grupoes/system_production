<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Lista de Tareas</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Gestión de tareas operativas y asignación de responsabilidades por rol.</p>
        </div>
        <button onclick="openModalTarea()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Tarea
        </button>
    </div>

    <!-- Table Container -->
    <div class="table-container bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <!-- Top Toolbar -->
        <div class="table-toolbar p-6 flex items-center justify-between border-b border-slate-50">
            <div class="table-search-box relative w-full max-w-md">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="search-tarea" placeholder="Buscar tareas..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" oninput="loadTareas()">
            </div>
            <div class="flex items-center gap-3">
                <select id="limit-tarea" class="bg-slate-50 border-none rounded-xl text-xs font-bold px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 transition-all" onchange="loadTareas()">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/30">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">ID</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tarea</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tiempo Est.</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Roles Responsables</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-tarea">
                    <!-- Cargando... -->
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">Cargando tareas...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-6 border-t border-slate-50 flex items-center justify-between bg-slate-50/20">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest" id="pagination-info">Mostrando 0 de 0 registros</p>
            <div class="flex items-center gap-2" id="pagination-btns"></div>
        </div>
    </div>
</div>

<!-- Modal: Tarea -->
<div id="modal_tarea" class="modal-backdrop">
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Tarea</h2>
            <button onclick="closeModal('modal_tarea')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-tarea" onsubmit="saveTarea(event)">
            <input type="hidden" name="id_tarea" id="id_tarea">
            <div class="modal-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group col-span-2">
                        <label class="form-label">Nombre de la Tarea</label>
                        <input type="text" name="nombre" id="nombre_tarea" class="form-input" placeholder="Ej: Redacción de informe técnico" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tiempo Estimado</label>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 relative">
                                <input type="number" min="0" name="estimado_hrs" id="estimado_hrs" class="form-input pr-10" placeholder="0">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">hrs</span>
                            </div>
                            <div class="flex-1 relative">
                                <input type="number" min="0" max="59" name="estimado_min" id="estimado_min" class="form-input pr-10" placeholder="0">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">min</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Categoría / Tipo</label>
                        <select name="tipo_tarea" id="tipo_tarea" class="form-input" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <label class="form-label !mb-0">Roles y Responsabilidades</label>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Selecciona los roles que ejecutan esta tarea</span>
                    </div>
                    <div id="roles-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Roles dinámicos -->
                        <div class="col-span-2 py-4 text-center text-slate-400 italic text-xs">Cargando roles...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_tarea')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-tarea" class="btn-primary">Guardar Tarea</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentPage = 1;
    let rolesList = [];

    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    async function loadRoles() {
        try {
            const res = await fetch(`${window.location.origin}/lista-tareas/roles`);
            const data = await res.json();
            if (data.status === 'success') {
                rolesList = data.data;
                renderRolesInModal();
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function loadTipos() {
        try {
            const res = await fetch(`${window.location.origin}/lista-tareas/tipos`);
            const data = await res.json();
            if (data.status === 'success') {
                const select = document.getElementById('tipo_tarea');
                let html = '<option value="">Seleccione...</option>';
                data.data.forEach(item => {
                    html += `<option value="${item.id}">${item.tipo}</option>`;
                });
                select.innerHTML = html;
            }
        } catch (err) {
            console.error(err);
        }
    }

    function renderRolesInModal(selectedRoles = []) {
        const container = document.getElementById('roles-container');
        if (rolesList.length === 0) {
            container.innerHTML = '<div class="col-span-2 py-4 text-center text-slate-400 text-xs">No hay roles activos</div>';
            return;
        }

        let html = '';
        rolesList.forEach(rol => {
            const selected = selectedRoles.find(sr => sr.rol_id == rol.id);
            const isChecked = selected ? 'checked' : '';
            const priority = selected ? selected.prioridad : '1';

            html += `
                <div class="p-4 bg-slate-50/50 rounded-[1.5rem] border border-slate-100 flex flex-col gap-3 transition-all hover:border-indigo-100 group">
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="roles[]" value="${rol.id}" class="sr-only peer" ${isChecked} onchange="toggleRolPriority(this, ${rol.id})">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">${rol.nombre}</span>
                    </div>
                    <div id="priority-select-${rol.id}" class="${isChecked ? '' : 'hidden'} animate-fade-in">
                        <div class="flex gap-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="prioridades[${rol.id}]" value="1" class="hidden peer" ${priority == '1' ? 'checked' : ''}>
                                <div class="py-1.5 px-3 rounded-lg bg-white border border-slate-100 text-center text-[9px] font-black text-slate-400 uppercase tracking-widest peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">Primaria</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="prioridades[${rol.id}]" value="0" class="hidden peer" ${priority == '0' ? 'checked' : ''}>
                                <div class="py-1.5 px-3 rounded-lg bg-white border border-slate-100 text-center text-[9px] font-black text-slate-400 uppercase tracking-widest peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all">Complementaria</div>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function toggleRolPriority(checkbox, rolId) {
        const priorityDiv = document.getElementById(`priority-select-${rolId}`);
        if (checkbox.checked) {
            priorityDiv.classList.remove('hidden');
        } else {
            priorityDiv.classList.add('hidden');
        }
    }

    function loadTareas(page = 1) {
        currentPage = page;
        const search = document.getElementById('search-tarea').value;
        const limit = document.getElementById('limit-tarea').value;

        fetch(`${window.location.origin}/lista-tareas/list?page=${page}&limit=${limit}&search=${search}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderTable(res.data);
                    renderPagination(res.total, res.limit, res.page);
                }
            })
            .catch(err => console.error(err));
    }

    function renderTable(data) {
        const tbody = document.getElementById('tbody-tarea');
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs font-bold uppercase tracking-widest">No hay tareas registradas</td></tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            let tiempoTexto = '<span class="text-slate-300">N/A</span>';
            if (item.horas_estimadas) {
                const h = Math.floor(item.horas_estimadas / 60);
                const m = item.horas_estimadas % 60;
                tiempoTexto = `<span class="text-slate-600 font-bold">${h}h ${m}m</span>`;
            }
            
            const rolesHtml = item.roles.map(r => {
                const color = r.prioridad == '1' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-500';
                const nombrePrio = r.prioridad == '1' ? 'PRIMARIA' : 'COMPLEMENTARIA';
                return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg ${color} text-[9px] font-black uppercase tracking-widest" title="${nombrePrio}">${r.rol_nombre}</span>`;
            }).join(' ') || '<span class="text-[9px] text-slate-300 italic font-bold">Sin roles asignados</span>';

            html += `
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-black text-slate-400">#${item.id}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 tracking-tight">${item.nombre}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${item.tipo_tarea_nombre || item.tipo_tarea}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        ${tiempoTexto}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5">
                            ${rolesHtml}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editTarea(${item.id})" class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-all">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteTarea(${item.id}, '${item.nombre}')" class="p-2 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-xl transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function renderPagination(total, limit, current) {
        const totalPages = Math.ceil(total / limit);
        const info = document.getElementById('pagination-info');
        const btns = document.getElementById('pagination-btns');
        
        info.innerText = `Mostrando ${(current - 1) * limit + 1} a ${Math.min(current * limit, total)} de ${total} registros`;
        
        let html = '';
        html += `<button onclick="loadTareas(${current - 1})" ${current == 1 ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
                html += `<button onclick="loadTareas(${i})" class="w-9 h-9 rounded-lg text-xs font-black uppercase transition-all ${i == current ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'hover:bg-white text-slate-500 border border-transparent hover:border-slate-100'}">${i}</button>`;
            } else if (i === current - 2 || i === current + 2) {
                html += `<span class="text-slate-300">...</span>`;
            }
        }

        html += `<button onclick="loadTareas(${parseInt(current) + 1})" ${current == totalPages ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>`;

        btns.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalTarea() {
        const form = document.getElementById('form-tarea');
        form.reset();
        document.getElementById('id_tarea').value = '';
        document.querySelector('.modal-title').innerText = 'Registrar Tarea';
        renderRolesInModal();
        openModal('modal_tarea');
    }

    async function editTarea(id) {
        try {
            const res = await fetch(`${window.location.origin}/lista-tareas/get/${id}`);
            const data = await res.json();
            if (data.status === 'success') {
                const t = data.data;
                document.getElementById('id_tarea').value = t.id;
                document.getElementById('nombre_tarea').value = t.nombre;
                
                if (t.horas_estimadas) {
                    document.getElementById('estimado_hrs').value = Math.floor(t.horas_estimadas / 60);
                    document.getElementById('estimado_min').value = t.horas_estimadas % 60;
                } else {
                    document.getElementById('estimado_hrs').value = '';
                    document.getElementById('estimado_min').value = '';
                }

                document.getElementById('tipo_tarea').value = t.tipo_tarea || 'GENERAL';
                
                document.querySelector('.modal-title').innerText = 'Editar Tarea';
                renderRolesInModal(t.roles);
                openModal('modal_tarea');
            }
        } catch (err) {
            console.error(err);
        }
    }

    function saveTarea(e) {
        e.preventDefault();
        const form = document.getElementById('form-tarea');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-tarea');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        fetch(`${window.location.origin}/lista-tareas/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_tarea');
                loadTareas(currentPage);
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function deleteTarea(id, nombre) {
        showConfirm('¿Eliminar Tarea?', `¿Estás seguro de eliminar la tarea <b>${nombre}</b>?`, 'Sí, eliminar', () => {
            fetch(`${window.location.origin}/lista-tareas/delete/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        loadTareas(currentPage);
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadRoles();
        loadTipos();
        loadTareas();
    });
</script>

<style>
    .form-input {
        @apply w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all;
    }
    .form-label {
        @apply text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 block;
    }
    .btn-primary {
        @apply px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 active:scale-95;
    }
    .btn-secondary {
        @apply px-6 py-2.5 bg-white text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all;
    }
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
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?= $this->endSection() ?>
