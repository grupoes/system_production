<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Carreras Universitarias</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Administración de programas académicos por institución.</p>
        </div>
        <button onclick="openModalCarrera()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Carrera
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <!-- Top Toolbar -->
        <div class="table-toolbar p-6 flex items-center justify-between border-b border-slate-50">
            <div class="table-search-box relative w-full max-w-md">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="search-carrera" placeholder="Buscar por carrera o universidad..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" oninput="loadCarreras()">
            </div>
            <div class="flex items-center gap-3">
                <select id="limit-carrera" class="bg-slate-50 border-none rounded-xl text-xs font-bold px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 transition-all" onchange="loadCarreras()">
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
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Carrera Profesional</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Universidad / Institución</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-carrera">
                    <!-- Cargando... -->
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i data-lucide="loader-2" class="w-6 h-6 animate-spin"></i>
                                <span class="text-xs font-bold uppercase tracking-widest">Cargando datos...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-6 border-t border-slate-50 flex items-center justify-between bg-slate-50/20">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest" id="pagination-info">Mostrando 0 de 0 registros</p>
            <div class="flex items-center gap-2" id="pagination-btns">
                <!-- Botones de paginación -->
            </div>
        </div>
    </div>
</div>

<!-- Modal: Carrera -->
<div id="modal_carrera" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Carrera</h2>
            <button onclick="closeModal('modal_carrera')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-carrera" onsubmit="saveCarrera(event)">
            <input type="hidden" name="id_carrera" id="id_carrera">
            <div class="modal-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre de la Carrera</label>
                    <input type="text" name="nombre" id="nombre_carrera" class="form-input" placeholder="Ej: Ingeniería de Sistemas" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Universidad / Institución</label>
                    <select name="institucion_id" id="institucion_id" class="form-input" required>
                        <option value="">Seleccione una universidad...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_carrera')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-carrera" class="btn-primary">Guardar Carrera</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentPage = 1;

    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function loadUniversidadesSelect() {
        fetch(`${window.location.origin}/universidades/all`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const select = document.getElementById('institucion_id');
                    let html = '<option value="">Seleccione una universidad...</option>';
                    res.data.forEach(item => {
                        html += `<option value="${item.id}">${item.nombre}</option>`;
                    });
                    select.innerHTML = html;
                }
            });
    }

    function loadCarreras(page = 1) {
        currentPage = page;
        const search = document.getElementById('search-carrera').value;
        const limit = document.getElementById('limit-carrera').value;
        const tbody = document.getElementById('tbody-carrera');

        fetch(`${window.location.origin}/carreras/list?page=${page}&limit=${limit}&search=${search}`)
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
        const tbody = document.getElementById('tbody-carrera');
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron resultados</td></tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            html += `
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-black text-slate-400">#${item.id}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-black text-slate-800 tracking-tight">${item.nombre}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i data-lucide="building-2" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">${item.institucion_nombre || 'No asignada'}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editCarrera(${item.id})" class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-all">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteCarrera(${item.id}, '${item.nombre}')" class="p-2 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-xl transition-all">
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
        html += `<button onclick="loadCarreras(${current - 1})" ${current == 1 ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
                html += `<button onclick="loadCarreras(${i})" class="w-9 h-9 rounded-lg text-xs font-black uppercase transition-all ${i == current ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'hover:bg-white text-slate-500 border border-transparent hover:border-slate-100'}">${i}</button>`;
            } else if (i === current - 2 || i === current + 2) {
                html += `<span class="text-slate-300">...</span>`;
            }
        }

        html += `<button onclick="loadCarreras(${parseInt(current) + 1})" ${current == totalPages ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>`;

        btns.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalCarrera() {
        const form = document.getElementById('form-carrera');
        form.reset();
        document.getElementById('id_carrera').value = '';
        document.querySelector('.modal-title').innerText = 'Registrar Carrera';
        openModal('modal_carrera');
    }

    function editCarrera(id) {
        fetch(`${window.location.origin}/carreras/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const uni = res.data;
                    document.getElementById('id_carrera').value = uni.id;
                    document.getElementById('nombre_carrera').value = uni.nombre;
                    document.getElementById('institucion_id').value = uni.institucion_id;
                    document.querySelector('.modal-title').innerText = 'Editar Carrera';
                    openModal('modal_carrera');
                }
            });
    }

    function saveCarrera(e) {
        e.preventDefault();
        const form = document.getElementById('form-carrera');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-carrera');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        fetch(`${window.location.origin}/carreras/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_carrera');
                loadCarreras(currentPage);
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function deleteCarrera(id, nombre) {
        showConfirm('¿Eliminar Carrera?', `¿Estás seguro de eliminar <b>${nombre}</b>?`, 'Sí, eliminar', () => {
            fetch(`${window.location.origin}/carreras/delete/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        loadCarreras(currentPage);
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadUniversidadesSelect();
        loadCarreras();
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
</style>
<?= $this->endSection() ?>
