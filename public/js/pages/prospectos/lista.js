/**
 * AJAX Prospects List Logic
 */
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const searchInput = document.getElementById('searchInput');
    const limitSelect = document.getElementById('limitSelect');
    const tbody = document.getElementById('prospectos-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');

    window.loadProspectos = function (page = 1) {
        if (!tbody) return;

        currentPage = page;
        const limit = limitSelect ? limitSelect.value : 10;
        const search = searchInput ? searchInput.value.trim() : '';

        // Show loading
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="p-8 text-center text-slate-500">
                    <div class="flex flex-col items-center justify-center">
                        <i data-lucide="loader-2" class="w-8 h-8 text-indigo-500 animate-spin mb-3"></i>
                        <p class="text-sm font-bold text-slate-700">Cargando prospectos...</p>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();

        const url = `${window.location.origin}/lista-potenciales-clientes/list?page=${page}&limit=${limit}&search=${encodeURIComponent(search)}`;

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    renderTable(response.data);
                    renderPagination(response.total, response.page, response.limit, response.lastPage);
                }
            })
            .catch(error => {
                console.error('Error fetching prospects:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="p-8 text-center text-red-500">
                            Error al cargar los datos.
                        </td>
                    </tr>
                `;
            });
    };

    function renderTable(prospects) {
        if (!prospects || prospects.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="p-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="users" class="w-12 h-12 text-slate-300 mb-3"></i>
                            <p class="text-sm font-bold text-slate-700">No se encontraron prospectos</p>
                        </div>
                    </td>
                </tr>
            `;
            lucide.createIcons();
            return;
        }

        let html = '';
        const limit = limitSelect ? parseInt(limitSelect.value) : 10;
        let startIndex = (currentPage - 1) * limit + 1;

        prospects.forEach((p, index) => {
            const rowNum = startIndex + index;
            const univ = p.universidad || 'N/A';
            const carr = p.carrera || 'N/A';
            const vend = p.vendedor || 'Sistema';
            const contactos = p.contactos || '<span class="text-slate-300 italic text-[10px]">Sin contactos</span>';
            
            const fecha = p.created_at ? new Date(p.created_at).toLocaleDateString() : 'N/A';
            const hora = p.created_at ? new Date(p.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

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
                    <td class="table-td text-slate-500 text-[11px] font-bold">${p.tarea || 'N/A'}</td>
                    <td class="table-td">
                        <span class="text-[11px] font-bold text-slate-700">${vend}</span>
                    </td>
                    <td class="table-td">
                        <div class="flex flex-col">
                            <span class="text-slate-700 font-bold text-[11px]">${fecha}</span>
                            <span class="text-[9px] text-slate-400 font-medium">${hora}</span>
                        </div>
                    </td>
                    <td class="table-td text-right">
                        <div class="relative inline-block text-left action-dropdown">
                            <button onclick="toggleActionMenu(event, ${p.id})" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors border border-transparent hover:border-slate-200 cursor-pointer">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                            </button>
                            <div id="dropdown-${p.id}" class="hidden absolute right-0 w-36 rounded-xl bg-white shadow-lg ring-1 ring-slate-200 z-[100] overflow-hidden">
                                <div class="py-1">
                                    <button onclick="window.editProspecto(${p.id})" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600 w-full text-left transition-colors cursor-pointer">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                        Editar
                                    </button>
                                    <button onclick="window.deleteProspecto(${p.id})" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-rose-500 hover:bg-rose-50 w-full text-left transition-colors cursor-pointer">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        lucide.createIcons();
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

        paginationInfo.innerText = `Mostrando del ${start} al ${end} de ${total} prospectos`;

        let controlsHtml = '';

        // Botón Anterior
        controlsHtml += `
            <button class="pagination-nav-btn ${page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page === 1 ? 'disabled' : `onclick="window.loadProspectos(${page - 1})"`}>
                <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
            </button>
        `;

        // Páginas
        for (let i = 1; i <= lastPage; i++) {
            if (i === page) {
                controlsHtml += `<div class="pagination-dot pagination-dot-active">${i}</div>`;
            } else {
                controlsHtml += `<button class="pagination-dot pagination-dot-inactive" onclick="window.loadProspectos(${i})">${i}</button>`;
            }
        }

        // Botón Siguiente
        controlsHtml += `
            <button class="pagination-nav-btn rotate-180 ${page === lastPage ? 'opacity-50 cursor-not-allowed' : ''}" ${page === lastPage ? 'disabled' : `onclick="window.loadProspectos(${page + 1})"`}>
                <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
            </button>
        `;

        paginationControls.innerHTML = controlsHtml;
        lucide.createIcons();
    }

    // Event Listeners
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                window.loadProspectos(1);
            }, 500);
        });
    }

    if (limitSelect) {
        limitSelect.addEventListener('change', () => {
            window.loadProspectos(1);
        });
    }

    // Actions
    window.editProspecto = function(id) {
        window.location.href = `${window.location.origin}/registrar-potencial-cliente?id=${id}`;
    };

    window.deleteProspecto = function(id) {
        // Implement confirm and delete
        console.log('Delete', id);
    };

    // Global click listener to close dropdowns
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        }
    });

    window.toggleActionMenu = function(event, id) {
        event.stopPropagation();
        const dropdown = document.getElementById(`dropdown-${id}`);
        const isHidden = dropdown.classList.contains('hidden');
        
        // Cerrar todos los demás
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        if (isHidden) {
            // Verificar si el dropdown se saldría por abajo
            const rect = event.currentTarget.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            
            if (spaceBelow < 150) { // Si hay menos de 150px abajo
                dropdown.classList.remove('mt-2', 'top-full');
                dropdown.classList.add('bottom-full', 'mb-2');
            } else {
                dropdown.classList.remove('bottom-full', 'mb-2');
                dropdown.classList.add('mt-2', 'top-full');
            }
            
            dropdown.classList.remove('hidden');
        }
    };

    // Initial Load
    window.loadProspectos(1);
});
