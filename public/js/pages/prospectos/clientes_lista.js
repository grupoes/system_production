/**
 * AJAX Clients List Logic
 */
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const searchInput = document.getElementById('searchInput');
    const limitSelect = document.getElementById('limitSelect');
    const tbody = document.getElementById('clientes-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');

    window.loadClientes = function (page = 1) {
        if (!tbody) return;

        currentPage = page;
        const limit = limitSelect ? limitSelect.value : 10;
        const search = searchInput ? searchInput.value.trim() : '';

        // Show loading
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="p-8 text-center text-slate-500">
                    <div class="flex flex-col items-center justify-center">
                        <i data-lucide="loader-2" class="w-8 h-8 text-emerald-500 animate-spin mb-3"></i>
                        <p class="text-sm font-bold text-slate-700">Cargando clientes...</p>
                    </div>
                </td>
            </tr>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const url = `${window.location.origin}/lista-clientes/list?page=${page}&limit=${limit}&search=${encodeURIComponent(search)}`;

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    renderTable(response.data);
                    renderPagination(response.total, response.page, response.limit, response.lastPage);
                }
            })
            .catch(error => {
                console.error('Error fetching clients:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="p-8 text-center text-red-500 font-bold">
                            Error al cargar los datos.
                        </td>
                    </tr>
                `;
            });
    };

    function renderTable(clientes) {
        if (!clientes || clientes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="users" class="w-12 h-12 text-slate-300 mb-3"></i>
                            <p class="text-sm font-bold text-slate-700">No se encontraron clientes</p>
                        </div>
                    </td>
                </tr>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        let html = '';
        const limit = limitSelect ? parseInt(limitSelect.value) : 10;
        let startIndex = (currentPage - 1) * limit + 1;

        clientes.forEach((c, index) => {
            const rowNum = startIndex + index;
            const univ = c.universidad || 'N/A';
            const carr = c.carrera || 'N/A';
            const titulo = c.titulo || '<span class="text-slate-300 italic">Sin título</span>';
            const contactos = c.contactos || '<span class="text-slate-300 italic text-[10px]">Sin contactos</span>';
            
            const fechaReg = c.registro_cliente ? new Date(c.registro_cliente).toLocaleDateString() : 'N/A';
            const horaReg = c.registro_cliente ? new Date(c.registro_cliente).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

            html += `
                <tr class="table-row-hover border-b border-slate-50">
                    <td class="table-td text-center">
                        <span class="text-xs font-bold text-slate-400">${rowNum}</span>
                    </td>
                    <td class="table-td">
                        <div class="leading-relaxed text-[11px] text-slate-600">${contactos}</div>
                    </td>
                    <td class="table-td table-td-bold">${univ}</td>
                    <td class="table-td text-slate-500 text-[11px]">${carr}</td>
                    <td class="table-td">
                        <div class="max-w-[200px] truncate text-[11px] font-medium text-slate-700" title="${c.titulo || ''}">
                            ${titulo}
                        </div>
                    </td>
                    <td class="table-td">
                        <div class="flex flex-col">
                            <span class="text-emerald-700 font-bold text-[11px]">${fechaReg}</span>
                            <span class="text-[9px] text-emerald-400 font-medium">${horaReg}</span>
                        </div>
                    </td>
                    <td class="table-td text-right">
                        <div class="relative inline-block text-left action-dropdown">
                            <button onclick="toggleActionMenu(event, ${c.id})" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors border border-transparent hover:border-emerald-100 cursor-pointer">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                            </button>
                            <div id="dropdown-${c.id}" class="hidden absolute right-0 w-36 rounded-xl bg-white shadow-lg ring-1 ring-slate-200 z-[100] overflow-hidden">
                                <div class="py-1">
                                    <a href="${window.location.origin}/registrar-potencial-cliente?id=${c.id}" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-indigo-600 w-full text-left transition-colors cursor-pointer">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        Ver Detalles
                                    </a>
                                    <button onclick="openScheduleModal(${c.id})" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-emerald-600 hover:bg-emerald-50 w-full text-left transition-colors cursor-pointer">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        Programar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Modal Logic
    let tsTarea, tsUsuario;
    let auxiliariesData = [];

    window.openScheduleModal = function(prospectoId) {
        document.getElementById('prog-prospecto-id').value = prospectoId;
        
        fetch(`${window.location.origin}/prospectos/schedule-data`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    populateModalData(res.tareas, res.auxiliares);
                    document.getElementById('modal-programar').classList.remove('hidden');
                }
            });
    };

    function populateModalData(tareas, auxiliares) {
        auxiliariesData = auxiliares;
        const tareaSelect = document.getElementById('prog-tarea-id');
        const usuarioSelect = document.getElementById('prog-usuario-id');

        tareaSelect.innerHTML = '<option value="">Seleccione tarea...</option>';
        tareas.forEach(t => {
            tareaSelect.innerHTML += `<option value="${t.id}">${t.nombre}</option>`;
        });

        usuarioSelect.innerHTML = '<option value="">Seleccione auxiliar...</option>';
        auxiliares.forEach(u => {
            usuarioSelect.innerHTML += `<option value="${u.id}">${u.nombre} ${u.tiene_horario == 1 ? '📅' : ''}</option>`;
        });
    }

    window.checkUserResponsibility = function() {
        const userId = document.getElementById('prog-usuario-id').value;
        const user = auxiliariesData.find(u => u.id == userId);
        const extraFields = document.getElementById('extra-schedule-fields');
        const alertBox = document.getElementById('responsibility-alert');

        if (!user) {
            extraFields.classList.add('hidden');
            alertBox.classList.add('hidden');
            return;
        }

        if (user.tiene_horario == 1) {
            extraFields.classList.add('hidden');
            alertBox.innerHTML = `
                <div class="flex gap-3">
                    <i data-lucide="info" class="w-4 h-4 text-indigo-500 mt-0.5"></i>
                    <p class="text-[10px] font-bold text-indigo-700 uppercase leading-tight">Este auxiliar ya tiene horario. La actividad se programará automáticamente desde su último horario disponible.</p>
                </div>
            `;
            alertBox.className = 'p-4 bg-indigo-50 rounded-2xl border border-indigo-100 mb-4 animate-fade-in';
            alertBox.classList.remove('hidden');
        } else {
            extraFields.classList.remove('hidden');
            alertBox.classList.add('hidden');
        }
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    window.closeScheduleModal = function() {
        document.getElementById('modal-programar').classList.add('hidden');
        document.getElementById('form-programar').reset();
        document.getElementById('extra-schedule-fields').classList.add('hidden');
        document.getElementById('responsibility-alert').classList.add('hidden');
    };

    window.confirmSchedule = function() {
        const formData = new FormData();
        formData.append('prospecto_id', document.getElementById('prog-prospecto-id').value);
        formData.append('tarea_id', document.getElementById('prog-tarea-id').value);
        formData.append('usuario_id', document.getElementById('prog-usuario-id').value);
        formData.append('prioridad', document.getElementById('prog-prioridad').value);
        formData.append('fecha', document.getElementById('prog-fecha').value);
        formData.append('hora', document.getElementById('prog-hora').value);

        if (!formData.get('tarea_id') || !formData.get('usuario_id')) {
            showToast('Por favor complete los campos obligatorios', 'error');
            return;
        }

        fetch(`${window.location.origin}/prospectos/save-schedule`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeScheduleModal();
            } else {
                showToast(res.message, 'error');
            }
        });
    };

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

        paginationInfo.innerText = `Mostrando del ${start} al ${end} de ${total} clientes`;

        let controlsHtml = '';

        controlsHtml += `
            <button class="pagination-nav-btn ${page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page === 1 ? 'disabled' : `onclick="window.loadClientes(${page - 1})"`}>
                <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
            </button>
        `;

        for (let i = 1; i <= lastPage; i++) {
            if (i === page) {
                controlsHtml += `<div class="pagination-dot pagination-dot-active">${i}</div>`;
            } else {
                controlsHtml += `<button class="pagination-dot pagination-dot-inactive" onclick="window.loadClientes(${i})">${i}</button>`;
            }
        }

        controlsHtml += `
            <button class="pagination-nav-btn rotate-180 ${page === lastPage ? 'opacity-50 cursor-not-allowed' : ''}" ${page === lastPage ? 'disabled' : `onclick="window.loadClientes(${page + 1})"`}>
                <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
            </button>
        `;

        paginationControls.innerHTML = controlsHtml;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Event Listeners
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                window.loadClientes(1);
            }, 500);
        });
    }

    if (limitSelect) {
        limitSelect.addEventListener('change', () => {
            window.loadClientes(1);
        });
    }

    // Action Menu Logic
    window.toggleActionMenu = function(event, id) {
        event.stopPropagation();
        const dropdown = document.getElementById(`dropdown-${id}`);
        const isHidden = dropdown.classList.contains('hidden');
        
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        if (isHidden) {
            dropdown.classList.remove('hidden');
        }
    };

    document.addEventListener('click', () => {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    });

    // Initial Load
    window.loadClientes(1);
});
