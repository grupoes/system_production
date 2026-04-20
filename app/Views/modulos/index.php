<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Módulos</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Administra la estructura de menús y accesos del sistema.</p>
        </div>
        <button onclick="openModalModulo()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Módulo
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="search-input" placeholder="Buscar módulos..." class="table-search-input">
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

                <!-- Filter Container -->
                <div class="relative">
                    <button onclick="toggleFilter(event)" class="table-action-btn">
                        <span>Filtrar</span>
                        <i data-lucide="filter" class="w-3 h-3 text-slate-400"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Grid Table -->
        <div class="table-grid-wrapper">
            <table class="table-grid">
                <thead>
                    <tr>
                        <th class="table-th w-16">#</th>
                        <th class="table-th">Módulo <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th text-center">Icono</th>
                        <th class="table-th">URL / Ruta <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Módulo Padre</th>
                        <th class="table-th text-right px-6">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-modulos">
                    <!-- Los datos se cargarán aquí por JS -->
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="table-footer border-t border-slate-50">
            <p id="pagination-info" class="table-info-text">Mostrando 0 resultados</p>
            <div id="pagination-controls" class="flex items-center gap-1.5">
                <!-- Controles paginación JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo/Editar Módulo -->
<div id="modal_modulo" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Módulo</h2>
            <button onclick="closeModal('modal_modulo')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-modulo" onsubmit="saveModulo(event)">
            <input type="hidden" name="id_modulo" id="id_modulo" value="">
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group col-span-2 md:col-span-1">
                        <label class="form-label">Nombre del Módulo</label>
                        <input type="text" name="modulo" id="modulo" class="form-input" placeholder="Ej: Reportes de Inventario" required>
                    </div>
                    <div class="form-group col-span-2 md:col-span-1">
                        <label class="form-label">Icono (Lucide)</label>
                        <input type="text" name="icono" id="icono" class="form-input" placeholder="Ej: box, settings, user">
                    </div>
                    <div class="form-group col-span-2 md:col-span-1">
                        <label class="form-label">Módulo Padre</label>
                        <select name="idpadre" id="idpadre" class="form-input">
                            <option value="0">Ninguno (Principal)</option>
                        </select>
                    </div>
                    <div class="form-group col-span-2 md:col-span-1">
                        <label class="form-label">Orden</label>
                        <input type="number" name="orden" id="orden" class="form-input" placeholder="Ej: 1" value="1" required>
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">URL / Ruta</label>
                        <input type="text" name="url" id="url" class="form-input" placeholder="Ej: inventario/reportes">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_modulo')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-modulo" class="btn-primary flex items-center justify-center gap-2">Guardar Módulo</button>
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

    function toggleFilter(event) {
        const dropdown = document.getElementById('filter-dropdown');
        if (dropdown) dropdown.classList.toggle('active');
    }

    // --- MÓDULOS LOGIC ---

    function loadPadres(selectedValue = null) {
        const selectPadre = document.getElementById('idpadre');
        if (!selectPadre) return;
        
        selectPadre.innerHTML = '<option value="0">Cargando...</option>';
        
        fetch(`${window.location.origin}/modulos/getPadres`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    let html = '<option value="0">Ninguno (Principal)</option>';
                    res.data.forEach(padre => {
                        html += `<option value="${padre.id}">${padre.modulo}</option>`;
                    });
                    selectPadre.innerHTML = html;

                    if (selectedValue !== null) {
                        selectPadre.value = selectedValue;
                    }
                } else {
                    selectPadre.innerHTML = '<option value="0">Ninguno (Principal)</option>';
                }
            })
            .catch(err => {
                console.error('Error fetching padres:', err);
                selectPadre.innerHTML = '<option value="0">Ninguno (Principal)</option>';
            });
    }

    function openModalModulo() {
        const form = document.getElementById('form-modulo');
        const modalTitle = document.querySelector('.modal-title');
        
        if (form) form.reset();
        document.getElementById('id_modulo').value = '';
        if (modalTitle) modalTitle.innerText = 'Nuevo Módulo';
        
        loadPadres(); // Siempre cargar módulos padre al abrir
        openModal('modal_modulo');
    }

    window.editModulo = function(id) {
        fetch(`${window.location.origin}/modulos/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;
                    document.getElementById('id_modulo').value = data.id;
                    document.getElementById('modulo').value = data.modulo;
                    document.getElementById('icono').value = data.icono;
                    document.getElementById('url').value = data.url;
                    document.getElementById('orden').value = data.orden;
                    
                    const modalTitle = document.querySelector('.modal-title');
                    if (modalTitle) modalTitle.innerText = 'Editar Módulo';

                    // Cargar padres y seleccionar el correspondiente
                    loadPadres(data.idpadre);
                    openModal('modal_modulo');
                } else {
                    if (typeof showToast !== 'undefined') showToast(res.message, 'error');
                }
            })
            .catch(err => {
                console.error('Error fetching modulo:', err);
                if (typeof showToast !== 'undefined') showToast('Error al obtener datos del módulo.', 'error');
            });
    };

    window.deleteModulo = function(id, nombre, force = false) {
        let title = force ? '¿Eliminar módulo y sus submódulos?' : '¿Eliminar módulo?';
        let message = force 
            ? `El módulo <b class="text-slate-800">${nombre}</b> y <b class="text-rose-500">TODOS sus submódulos</b> serán desactivados. ¿Deseas continuar?` 
            : `El módulo <b class="text-slate-800">${nombre}</b> será desactivado. ¿Deseas continuar?`;

        showConfirm(
            title,
            message,
            force ? 'Sí, eliminar todo' : 'Sí, eliminar',
            function() {
                const url = force ? `${window.location.origin}/modulos/delete/${id}?force=true` : `${window.location.origin}/modulos/delete/${id}`;
                fetch(url, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        if (typeof showToast !== 'undefined') showToast(res.message, 'success');
                        window.loadModulos(currentPage);
                    } else if (res.has_children) {
                        // Si el backend dice que tiene hijos, disparamos de nuevo la misma función con force=true
                        window.deleteModulo(id, nombre, true);
                    } else {
                        if (typeof showToast !== 'undefined') showToast(res.message, 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    if (typeof showToast !== 'undefined') showToast('Hubo un error al intentar eliminar el módulo.', 'error');
                });
            }
        );
    };

    function saveModulo(e) {
        e.preventDefault();
        const form = document.getElementById('form-modulo');
        const btnSave = document.getElementById('btn-save-modulo');
        const originalText = btnSave.innerHTML;
        
        btnSave.disabled = true;
        btnSave.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Guardando...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const formData = new FormData(form);

        fetch(`${window.location.origin}/modulos/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (typeof showToast !== 'undefined') showToast(res.message, 'success');
                closeModal('modal_modulo');
                window.loadModulos(currentPage);
            } else {
                if (typeof showToast !== 'undefined') showToast(res.message, 'error');
            }
        })
        .catch(err => {
            console.error('Error saving modulo:', err);
            if (typeof showToast !== 'undefined') showToast('Error de red al guardar el módulo', 'error');
        })
        .finally(() => {
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }

    // --- CARGA DE DATOS DE TABLA ---
    let currentPage = 1;
    let searchTimeout = null;
    
    const tbody = document.getElementById('tbody-modulos');
    const searchInput = document.getElementById('search-input');
    const limitSelect = document.getElementById('limit-select');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');

    window.loadModulos = function(page = 1) {
        if (!tbody) return;
        
        currentPage = page;
        const limit = limitSelect ? limitSelect.value : 10;
        const search = searchInput ? searchInput.value.trim() : '';

        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8"><i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-indigo-500"></i></td></tr>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`${window.location.origin}/modulos/list?page=${page}&limit=${limit}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    renderTable(res.data, page, limit);
                    renderPagination(res.total, res.page, res.limit, Math.ceil(res.total / res.limit));
                }
            })
            .catch(error => {
                console.error('Error cargando módulos:', error);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-rose-500">Error al cargar los datos</td></tr>`;
            });
    };

    function renderTable(data, page, limit) {
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-slate-500">No se encontraron módulos</td></tr>`;
            return;
        }

        let html = '';
        data.forEach((m, index) => {
            const rowNum = (page - 1) * limit + (index + 1);
            const padNum = String(rowNum).padStart(2, '0');
            const iconHtml = m.icono ? m.icono : 'box';
            
            let padreHtml = '';
            if (m.nombre_padre) {
                padreHtml = `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider">
                                <i data-lucide="corner-down-right" class="w-3 h-3"></i>
                                ${m.nombre_padre}
                            </span>`;
            } else {
                padreHtml = `<span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Principal</span>`;
            }

            html += `
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    <td class="table-td text-center">
                        <span class="text-xs font-bold text-slate-400">#${padNum}</span>
                    </td>
                    <td class="table-td text-center text-xs font-bold text-slate-500">
                        ${m.orden}
                    </td>
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-white group-hover:shadow-sm transition-all">
                                <i data-lucide="${iconHtml}" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-800">${m.modulo}</span>
                        </div>
                    </td>
                    <td class="table-td text-center">
                        <code class="text-[10px] font-black px-2 py-1 bg-slate-50 text-slate-500 rounded-md border border-slate-100">
                            ${iconHtml}
                        </code>
                    </td>
                    <td class="table-td">
                        <div class="flex items-center gap-1 text-xs font-medium text-slate-500">
                            <span class="text-slate-300">/</span>
                            <span class="font-bold text-slate-600">${m.url}</span>
                        </div>
                    </td>
                    <td class="table-td">
                        ${padreHtml}
                    </td>
                    <td class="table-td text-right px-6">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="window.editModulo(${m.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors border border-slate-200/60 cursor-pointer" title="Editar">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="window.deleteModulo(${m.id}, '${m.modulo.replace(/'/g, "\\'")}')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors border border-slate-200/60 cursor-pointer" title="Eliminar">
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

        paginationInfo.innerText = `Mostrando del ${start} al ${end} de ${total} módulos`;

        let html = '';

        // Prev Button
        if (page > 1) {
            html += `<button onclick="window.loadModulos(${page - 1})" class="pagination-nav-btn"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        } else {
            html += `<button disabled class="pagination-nav-btn opacity-50 cursor-not-allowed"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        }

        // Pages
        for (let i = 1; i <= lastPage; i++) {
            if (i === page) {
                html += `<div class="pagination-dot pagination-dot-active">${i}</div>`;
            } else if (i === 1 || i === lastPage || (i >= page - 1 && i <= page + 1)) {
                html += `<button onclick="window.loadModulos(${i})" class="pagination-dot pagination-dot-inactive">${i}</button>`;
            } else if (i === page - 2 || i === page + 2) {
                html += `<span class="px-1 text-slate-400">...</span>`;
            }
        }

        // Next Button
        if (page < lastPage) {
            html += `<button onclick="window.loadModulos(${page + 1})" class="pagination-nav-btn rotate-180"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        } else {
            html += `<button disabled class="pagination-nav-btn rotate-180 opacity-50 cursor-not-allowed"><i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i></button>`;
        }

        paginationControls.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // --- EVENT LISTENERS ---
    document.addEventListener('DOMContentLoaded', () => {
        if (limitSelect) {
            limitSelect.addEventListener('change', () => window.loadModulos(1));
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    window.loadModulos(1);
                }, 500);
            });
        }

        // Carga Inicial
        if (tbody) {
            window.loadModulos(1);
        }
    });
</script>
<?= $this->endSection() ?>