<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Acciones</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Define las operaciones disponibles (Crear, Editar, etc.) para los módulos.</p>
        </div>
        <button onclick="openModalAccion()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Acción
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="search-input" placeholder="Buscar acciones..." class="table-search-input">
            </div>
            <div class="flex items-center gap-2">
                <!-- Records per Page Selector -->
                <div class="relative">
                    <select id="limit-select" class="table-action-btn appearance-none pr-8 bg-slate-50/50 border border-slate-100 rounded-lg outline-none cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <i data-lucide="chevrons-up-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-2.5 h-2.5 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
        </div>

        <!-- Main Grid Table -->
        <div class="table-grid-wrapper">
            <table class="table-grid">
                <thead>
                    <tr>
                        <th class="table-th w-16">#</th>
                        <th class="table-th">Nombre Acción <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Descripción</th>
                        <th class="table-th text-right px-6">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-acciones">
                    <!-- Los datos se cargarán aquí por JS -->
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="table-footer border-t border-slate-50">
            <p id="pagination-info" class="table-info-text">Mostrando 0 resultados</p>
            <div id="pagination-controls" class="flex items-center gap-1.5">
                <!-- JS Pagination -->
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nueva/Editar Acción -->
<div id="modal_accion" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Acción</h2>
            <button onclick="closeModal('modal_accion')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-accion" onsubmit="saveAccion(event)">
            <input type="hidden" name="id_accion" id="id_accion" value="">
            <div class="modal-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre de la Acción</label>
                    <input type="text" name="nombre_accion" id="nombre_accion" class="form-input" placeholder="Ej: Imprimir Ticket" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="form-input resize-none" rows="3" placeholder="Define qué permite hacer esta acción en el sistema..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_accion')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-accion" class="btn-primary flex items-center justify-center gap-2">Guardar Acción</button>
            </div>
        </form>
    </div>
</div>

<script>
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

    // --- ACCIONES LOGIC ---

    function openModalAccion() {
        const form = document.getElementById('form-accion');
        const modalTitle = document.querySelector('.modal-title');
        
        if (form) form.reset();
        document.getElementById('id_accion').value = '';
        if (modalTitle) modalTitle.innerText = 'Nueva Acción';
        
        openModal('modal_accion');
    }

    window.editAccion = function(id) {
        fetch(`${window.location.origin}/acciones/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;
                    document.getElementById('id_accion').value = data.id;
                    document.getElementById('nombre_accion').value = data.nombre_accion;
                    document.getElementById('descripcion').value = data.descripcion;
                    
                    const modalTitle = document.querySelector('.modal-title');
                    if (modalTitle) modalTitle.innerText = 'Editar Acción';

                    openModal('modal_accion');
                } else {
                    if (typeof showToast !== 'undefined') showToast(res.message, 'error');
                }
            })
            .catch(err => {
                console.error('Error fetching accion:', err);
                if (typeof showToast !== 'undefined') showToast('Error al obtener datos de la acción.', 'error');
            });
    };

    window.deleteAccion = function(id, nombre) {
        showConfirm(
            '¿Eliminar Acción?',
            `La acción <b class="text-slate-800">${nombre}</b> será desactivada y ya no podrá asignarse. ¿Deseas continuar?`,
            'Sí, eliminar',
            function() {
                fetch(`${window.location.origin}/acciones/delete/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        if (typeof showToast !== 'undefined') showToast(res.message, 'success');
                        window.loadAcciones(currentPage);
                    } else {
                        if (typeof showToast !== 'undefined') showToast(res.message, 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    if (typeof showToast !== 'undefined') showToast('Hubo un error al intentar eliminar.', 'error');
                });
            }
        );
    };

    function saveAccion(e) {
        e.preventDefault();
        const form = document.getElementById('form-accion');
        const btnSave = document.getElementById('btn-save-accion');
        const originalText = btnSave.innerHTML;
        
        btnSave.disabled = true;
        btnSave.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Guardando...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const formData = new FormData(form);

        fetch(`${window.location.origin}/acciones/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (typeof showToast !== 'undefined') showToast(res.message, 'success');
                closeModal('modal_accion');
                window.loadAcciones(currentPage);
            } else {
                if (typeof showToast !== 'undefined') showToast(res.message, 'error');
            }
        })
        .catch(err => {
            console.error('Error saving accion:', err);
            if (typeof showToast !== 'undefined') showToast('Error de red al guardar la acción', 'error');
        })
        .finally(() => {
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }

    // --- CARGA DE DATOS ---
    let currentPage = 1;
    let searchTimeout = null;
    
    const tbody = document.getElementById('tbody-acciones');
    const searchInput = document.getElementById('search-input');
    const limitSelect = document.getElementById('limit-select');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');

    window.loadAcciones = function(page = 1) {
        if (!tbody) return;
        
        currentPage = page;
        const limit = limitSelect ? limitSelect.value : 10;
        const search = searchInput ? searchInput.value.trim() : '';

        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8"><i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-indigo-500"></i></td></tr>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`${window.location.origin}/acciones/list?page=${page}&limit=${limit}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    renderTable(res.data, page, limit);
                    renderPagination(res.total, res.page, res.limit, Math.ceil(res.total / res.limit));
                }
            })
            .catch(error => {
                console.error('Error cargando:', error);
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-rose-500">Error al cargar los datos</td></tr>`;
            });
    };

    function renderTable(data, page, limit) {
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-slate-500">No se encontraron acciones</td></tr>`;
            return;
        }

        let html = '';
        data.forEach((a, index) => {
            const padNum = String(a.id).padStart(3, '0');
            
            html += `
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="table-td">
                        <span class="text-xs font-bold text-slate-400">#${padNum}</span>
                    </td>
                    <td class="table-td">
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 font-black text-[11px] rounded-md tracking-widest uppercase">
                            ${a.nombre_accion}
                        </span>
                    </td>
                    <td class="table-td text-sm text-slate-500 font-medium">
                        ${a.descripcion || '-'}
                    </td>
                    <td class="table-td text-right px-6">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="window.editAccion(${a.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors border border-slate-200/60 cursor-pointer" title="Editar">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="window.deleteAccion(${a.id}, '${a.nombre_accion.replace(/'/g, "\\'")}')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors border border-slate-200/60 cursor-pointer" title="Eliminar">
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

    function renderPagination(total, page, limit, lastPage) {
        if (!paginationInfo || !paginationControls) return;

        page = parseInt(page, 10) || 1;
        limit = parseInt(limit, 10) || 10;
        total = parseInt(total, 10) || 0;
        lastPage = parseInt(lastPage, 10) || 1;

        const start = (page - 1) * limit + 1;
        const end = Math.min(page * limit, total);
        
        if (total === 0) {
            paginationInfo.innerText = 'Mostrando 0 resultados';
            paginationControls.innerHTML = '';
            return;
        }

        paginationInfo.innerText = `Mostrando del ${start} al ${end} de ${total} acciones`;

        let html = '';

        if (page > 1) {
            html += `<button onclick="window.loadAcciones(${page - 1})" class="pagination-nav-btn"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        } else {
            html += `<button disabled class="pagination-nav-btn opacity-50 cursor-not-allowed"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        }

        for (let i = 1; i <= lastPage; i++) {
            if (i === page) {
                html += `<div class="pagination-dot pagination-dot-active">${i}</div>`;
            } else if (i === 1 || i === lastPage || (i >= page - 1 && i <= page + 1)) {
                html += `<button onclick="window.loadAcciones(${i})" class="pagination-dot pagination-dot-inactive">${i}</button>`;
            } else if (i === page - 2 || i === page + 2) {
                html += `<span class="px-1 text-slate-400">...</span>`;
            }
        }

        if (page < lastPage) {
            html += `<button onclick="window.loadAcciones(${page + 1})" class="pagination-nav-btn rotate-180"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        } else {
            html += `<button disabled class="pagination-nav-btn rotate-180 opacity-50 cursor-not-allowed"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        }

        paginationControls.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (limitSelect) limitSelect.addEventListener('change', () => window.loadAcciones(1));

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => window.loadAcciones(1), 500);
            });
        }

        if (tbody) window.loadAcciones(1);
    });
</script>
<?= $this->endSection() ?>