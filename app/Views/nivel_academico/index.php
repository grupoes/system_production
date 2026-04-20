<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Niveles Académicos</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Gestiona los niveles de estudio disponibles en el sistema.</p>
        </div>
        <button onclick="openModalNivel()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Nivel
        </button>
    </div>

    <!-- Table Container -->
    <div class="table-container bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <!-- Top Toolbar -->
        <div class="table-toolbar p-6 flex items-center justify-between border-b border-slate-50">
            <div class="table-search-box relative w-full max-md">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="search-nivel" placeholder="Buscar niveles..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all" oninput="loadNiveles()">
            </div>
            <div class="flex items-center gap-3">
                <select id="limit-nivel" class="bg-slate-50 border-none rounded-xl text-xs font-bold px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 transition-all" onchange="loadNiveles()">
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
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nombre</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Descripción</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-nivel">
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
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nivel -->
<div id="modal_nivel" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Nivel</h2>
            <button onclick="closeModal('modal_nivel')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-nivel" onsubmit="saveNivel(event)">
            <input type="hidden" name="id_nivel" id="id_nivel">
            <div class="modal-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre del Nivel</label>
                    <input type="text" name="nombre" id="nombre_nivel" class="form-input" placeholder="Ej: Pregrado, Postgrado, Doctorado" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción (Opcional)</label>
                    <textarea name="descripcion" id="descripcion_nivel" class="form-input min-h-[100px]" placeholder="Breve descripción del nivel académico..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_nivel')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-nivel" class="btn-primary">Guardar Nivel</button>
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

    function loadNiveles(page = 1) {
        currentPage = page;
        const search = document.getElementById('search-nivel').value;
        const limit = document.getElementById('limit-nivel').value;
        const tbody = document.getElementById('tbody-nivel');

        fetch(`${window.location.origin}/nivel_academico/list?page=${page}&limit=${limit}&search=${search}`)
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
        const tbody = document.getElementById('tbody-nivel');
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
                        <p class="text-xs text-slate-500 font-medium line-clamp-1">${item.descripcion || '-'}</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editNivel(${item.id})" class="p-2 hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-all">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteNivel(${item.id}, '${item.nombre}')" class="p-2 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-xl transition-all">
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
        html += `<button onclick="loadNiveles(${current - 1})" ${current == 1 ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= current - 1 && i <= current + 1)) {
                html += `<button onclick="loadNiveles(${i})" class="w-9 h-9 rounded-lg text-xs font-black uppercase transition-all ${i == current ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'hover:bg-white text-slate-500 border border-transparent hover:border-slate-100'}">${i}</button>`;
            } else if (i === current - 2 || i === current + 2) {
                html += `<span class="text-slate-300">...</span>`;
            }
        }

        html += `<button onclick="loadNiveles(${parseInt(current) + 1})" ${current == totalPages ? 'disabled' : ''} class="p-2 rounded-lg border border-slate-100 hover:bg-white text-slate-400 disabled:opacity-30 transition-all">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>`;

        btns.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalNivel() {
        const form = document.getElementById('form-nivel');
        form.reset();
        document.getElementById('id_nivel').value = '';
        document.querySelector('.modal-title').innerText = 'Registrar Nivel';
        openModal('modal_nivel');
    }

    function editNivel(id) {
        fetch(`${window.location.origin}/nivel_academico/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;
                    document.getElementById('id_nivel').value = data.id;
                    document.getElementById('nombre_nivel').value = data.nombre;
                    document.getElementById('descripcion_nivel').value = data.descripcion || '';
                    document.querySelector('.modal-title').innerText = 'Editar Nivel';
                    openModal('modal_nivel');
                }
            });
    }

    function saveNivel(e) {
        e.preventDefault();
        const form = document.getElementById('form-nivel');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-nivel');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        fetch(`${window.location.origin}/nivel_academico/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_nivel');
                loadNiveles(currentPage);
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function deleteNivel(id, nombre) {
        showConfirm('¿Eliminar Nivel?', `¿Estás seguro de eliminar <b>${nombre}</b>?`, 'Sí, eliminar', () => {
            fetch(`${window.location.origin}/nivel_academico/delete/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        loadNiveles(currentPage);
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadNiveles();
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
