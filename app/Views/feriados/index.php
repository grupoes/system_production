<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Calendario de Feriados</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Administración de días festivos nacionales, regionales y locales.</p>
        </div>
        <button onclick="openModalFeriado()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Feriado
        </button>
    </div>

    <!-- Table Container -->
    <div class="table-container bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <!-- Top Toolbar -->
        <div class="table-toolbar p-6 flex items-center justify-between border-b border-slate-50">
            <div class="flex items-center gap-4 w-full max-w-2xl">
                <div class="table-search-box relative flex-1">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                    <input type="text" id="search-feriado" placeholder="Buscar por nombre..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" oninput="loadFeriados()">
                </div>
                <select id="filter-anio" class="bg-slate-50 border-none rounded-xl text-xs font-bold px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 transition-all" onchange="loadFeriados()">
                    <option value="">Todos los años</option>
                    <?php 
                    $currentYear = date('Y');
                    for($i = $currentYear + 1; $i >= $currentYear - 2; $i--): ?>
                        <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <select id="limit-feriado" class="bg-slate-50 border-none rounded-xl text-xs font-bold px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 transition-all" onchange="loadFeriados()">
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
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Feriado / Festividad</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipo</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Laborable</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-feriado">
                    <!-- Cargando... -->
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">Cargando calendario...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-6 border-t border-slate-50 flex items-center justify-between bg-slate-50/20">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest" id="pagination-info">Mostrando 0 de 0 registros</p>
            <div class="flex items-center gap-2" id="pagination-btns">
            </div>
        </div>
    </div>
</div>

<!-- Modal: Feriado -->
<div id="modal_feriado" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Feriado</h2>
            <button onclick="closeModal('modal_feriado')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-feriado" onsubmit="saveFeriado(event)">
            <input type="hidden" name="id_feriado" id="id_feriado">
            <div class="modal-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre del Feriado</label>
                    <input type="text" name="nombre" id="nombre_feriado" class="form-input" placeholder="Ej: Año Nuevo, Jueves Santo..." required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Fecha Específica</label>
                        <input type="date" name="fecha" id="fecha_feriado" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ámbito / Tipo</label>
                        <select name="tipo" id="tipo_feriado" class="form-input" required>
                            <option value="Nacional">Nacional</option>
                            <option value="Regional">Regional</option>
                            <option value="Local">Local</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 mt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="es_laborable" id="es_laborable" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                    <div class="flex flex-col">
                        <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest">¿Es Laborable?</span>
                        <span class="text-[9px] text-slate-500 font-bold uppercase">Si se activa, el día se cuenta como trabajado a pesar del feriado.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_feriado')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-feriado" class="btn-primary">Guardar Feriado</button>
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

    function loadFeriados(page = 1) {
        currentPage = page;
        const search = document.getElementById('search-feriado').value;
        const anio = document.getElementById('filter-anio').value;
        const limit = document.getElementById('limit-feriado').value;

        fetch(`${window.location.origin}/feriados/list?page=${page}&limit=${limit}&search=${search}&anio=${anio}`)
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
        const tbody = document.getElementById('tbody-feriado');
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs font-bold uppercase tracking-widest">No hay feriados registrados para este periodo</td></tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            const fechaObj = new Date(item.fecha + 'T00:00:00');
            const dia = fechaObj.getDate();
            const mes = fechaObj.toLocaleString('es-ES', { month: 'short' }).replace('.', '');
            const anio = fechaObj.getFullYear();
            
            const tipoColor = {
                'Nacional': 'bg-indigo-50 text-indigo-600',
                'Regional': 'bg-amber-50 text-amber-600',
                'Local': 'bg-emerald-50 text-emerald-600'
            }[item.tipo] || 'bg-slate-50 text-slate-600';

            const laborableBadge = item.es_laborable == 1 
                ? '<span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[9px] font-black uppercase">Sí</span>'
                : '<span class="px-2 py-0.5 bg-rose-50 text-rose-600 rounded text-[9px] font-black uppercase">No</span>';

            html += `
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm">
                                <span class="text-[10px] font-black text-rose-500 uppercase leading-none">${mes}</span>
                                <span class="text-sm font-black text-slate-800 leading-tight">${dia}</span>
                            </div>
                            <span class="text-[10px] font-black text-slate-400">${anio}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-black text-slate-800 tracking-tight">${item.nombre}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 ${tipoColor} rounded-lg text-[9px] font-black uppercase tracking-widest">${item.tipo}</span>
                    </td>
                    <td class="px-6 py-4">
                        ${laborableBadge}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editFeriado(${item.id})" class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-all">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteFeriado(${item.id}, '${item.nombre}')" class="p-2 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-xl transition-all">
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
        html += `<button onclick="loadFeriados(${current - 1})" ${current == 1 ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
                html += `<button onclick="loadFeriados(${i})" class="w-9 h-9 rounded-lg text-xs font-black uppercase transition-all ${i == current ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'hover:bg-white text-slate-500 border border-transparent hover:border-slate-100'}">${i}</button>`;
            } else if (i === current - 2 || i === current + 2) {
                html += `<span class="text-slate-300">...</span>`;
            }
        }

        html += `<button onclick="loadFeriados(${parseInt(current) + 1})" ${current == totalPages ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>`;

        btns.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalFeriado() {
        const form = document.getElementById('form-feriado');
        form.reset();
        document.getElementById('id_feriado').value = '';
        document.querySelector('.modal-title').innerText = 'Registrar Feriado';
        openModal('modal_feriado');
    }

    function editFeriado(id) {
        fetch(`${window.location.origin}/feriados/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;
                    document.getElementById('id_feriado').value = data.id;
                    document.getElementById('nombre_feriado').value = data.nombre;
                    document.getElementById('fecha_feriado').value = data.fecha;
                    document.getElementById('tipo_feriado').value = data.tipo;
                    document.getElementById('es_laborable').checked = data.es_laborable == 1;
                    document.querySelector('.modal-title').innerText = 'Editar Feriado';
                    openModal('modal_feriado');
                }
            });
    }

    function saveFeriado(e) {
        e.preventDefault();
        const form = document.getElementById('form-feriado');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-feriado');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        fetch(`${window.location.origin}/feriados/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_feriado');
                loadFeriados(currentPage);
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function deleteFeriado(id, nombre) {
        showConfirm('¿Eliminar Feriado?', `¿Estás seguro de eliminar el feriado <b>${nombre}</b>?`, 'Sí, eliminar', () => {
            fetch(`${window.location.origin}/feriados/delete/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        loadFeriados(currentPage);
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadFeriados();
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
