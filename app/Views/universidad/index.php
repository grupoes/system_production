<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Universidades</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Administración de instituciones de educación superior.</p>
        </div>
        <button onclick="openModalUniversidad()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Universidad
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <!-- Top Toolbar -->
        <div class="table-toolbar p-6 flex items-center justify-between border-b border-slate-50">
            <div class="table-search-box relative w-full max-w-md">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="search-universidad" placeholder="Buscar universidades..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" oninput="loadUniversidades()">
            </div>
            <div class="flex items-center gap-3">
                <select id="limit-universidad" class="bg-slate-50 border-none rounded-xl text-xs font-bold px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 transition-all" onchange="loadUniversidades()">
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
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Universidad / Institución</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Abreviatura</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sector / Tipo</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-universidad">
                    <!-- Cargando... -->
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
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

<!-- Modal: Universidad -->
<div id="modal_universidad" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Universidad</h2>
            <button onclick="closeModal('modal_universidad')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-universidad" onsubmit="saveUniversidad(event)">
            <input type="hidden" name="id_universidad" id="id_universidad">
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group col-span-2">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" id="nombre_uni" class="form-input" placeholder="Ej: Universidad Nacional Mayor de San Marcos" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Abreviatura / Siglas</label>
                        <input type="text" name="abreviatura" id="abreviatura_uni" class="form-input" placeholder="Ej: UNMSM" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sector</label>
                        <select name="sector" id="sector_uni" class="form-input" required>
                            <option value="">Seleccione...</option>
                            <option value="Pública">Pública</option>
                            <option value="Privada">Privada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo de Institución</label>
                        <select name="tipo" id="tipo_uni" class="form-input" required>
                            <option value="">Seleccione...</option>
                            <option value="UNIVERSIDAD">UNIVERSIDAD</option>
                            <option value="INSTITUTO">INSTITUTO</option>
                            <option value="ESCUELA">ESCUELA</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_universidad')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-uni" class="btn-primary">Guardar Universidad</button>
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

    function loadUniversidades(page = 1) {
        currentPage = page;
        const search = document.getElementById('search-universidad').value;
        const limit = document.getElementById('limit-universidad').value;
        const tbody = document.getElementById('tbody-universidad');

        fetch(`${window.location.origin}/universidades/list?page=${page}&limit=${limit}&search=${search}`)
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
        const tbody = document.getElementById('tbody-universidad');
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron resultados</td></tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            const sectorColor = (item.sector === 'Pública' || item.sector === 'ESTATAL') ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600';
            html += `
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-black text-slate-400">#${item.id}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 tracking-tight">${item.nombre}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${item.tipo}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest">${item.abreviatura}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 ${sectorColor} rounded-lg text-[9px] font-black uppercase tracking-widest">${item.sector}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editUniversidad(${item.id})" class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-all">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteUniversidad(${item.id}, '${item.nombre}')" class="p-2 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-xl transition-all">
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
        // Botón anterior
        html += `<button onclick="loadUniversidades(${current - 1})" ${current == 1 ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>`;

        // Páginas (simplificado)
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
                html += `<button onclick="loadUniversidades(${i})" class="w-9 h-9 rounded-lg text-xs font-black uppercase transition-all ${i == current ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'hover:bg-white text-slate-500 border border-transparent hover:border-slate-100'}">${i}</button>`;
            } else if (i === current - 2 || i === current + 2) {
                html += `<span class="text-slate-300">...</span>`;
            }
        }

        // Botón siguiente
        html += `<button onclick="loadUniversidades(${parseInt(current) + 1})" ${current == totalPages ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>`;

        btns.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalUniversidad() {
        const form = document.getElementById('form-universidad');
        form.reset();
        document.getElementById('id_universidad').value = '';
        document.querySelector('.modal-title').innerText = 'Registrar Universidad';
        openModal('modal_universidad');
    }

    function editUniversidad(id) {
        fetch(`${window.location.origin}/universidades/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const uni = res.data;
                    document.getElementById('id_universidad').value = uni.id;
                    document.getElementById('nombre_uni').value = uni.nombre;
                    document.getElementById('abreviatura_uni').value = uni.abreviatura;
                    document.getElementById('sector_uni').value = uni.sector;
                    document.getElementById('tipo_uni').value = uni.tipo;
                    document.querySelector('.modal-title').innerText = 'Editar Universidad';
                    openModal('modal_universidad');
                }
            });
    }

    function saveUniversidad(e) {
        e.preventDefault();
        const form = document.getElementById('form-universidad');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-uni');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        fetch(`${window.location.origin}/universidades/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_universidad');
                loadUniversidades(currentPage);
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function deleteUniversidad(id, nombre) {
        showConfirm('¿Eliminar Universidad?', `¿Estás seguro de eliminar <b>${nombre}</b>?`, 'Sí, eliminar', () => {
            fetch(`${window.location.origin}/universidades/delete/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        loadUniversidades(currentPage);
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadUniversidades();
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