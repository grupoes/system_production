/**
 * importar.js
 * Lógica para cargar y mostrar datos de Google Sheets
 * en la vista de importación masiva de clientes.
 */

const LIMIT_PER_PAGE = 25;

let allData    = [];  // todos los registros traídos del sheet
let filteredData = []; // después de aplicar búsqueda / filtro
let currentPage = 1;

/* =====================================================
   FETCH DE DATOS DESDE EL BACKEND
   ===================================================== */
async function cargarDatosSheet() {
    showState('loading');

    try {
        const res  = await fetch(window.location.origin + '/importar-clientes/fetch');
        const json = await res.json();

        if (json.status !== 'success') {
            showError(json.message ?? 'Error desconocido al leer la hoja.');
            return;
        }

        allData      = json.data;   // arreglo de filas (sin header)
        filteredData = [...allData];
        currentPage  = 1;

        renderStats();
        renderHojaFilter();
        renderAuxiliarFilter();
        renderTable();
        showState('table');

    } catch (err) {
        showError('No se pudo conectar con el servidor. ' + err.message);
    }
}

/* =====================================================
   ESTADÍSTICAS RÁPIDAS
   ===================================================== */
function renderStats() {
    const total    = allData.length;
    const univs    = new Set(allData.map(r => (r[6] ?? '').trim().toUpperCase()).filter(Boolean));
    const posgrado = allData.filter(r => (r[4] ?? '').toUpperCase().includes('POSGRADO')).length;
    const pregrado = allData.filter(r => (r[4] ?? '').toUpperCase().includes('PREGRADO')).length;

    document.getElementById('stat-total').textContent    = total;
    document.getElementById('stat-univ').textContent     = univs.size;
    document.getElementById('stat-posgrado').textContent = posgrado;
    document.getElementById('stat-pregrado').textContent = pregrado;

    document.getElementById('stats-bar').classList.remove('hidden');
    lucide.createIcons();
}

function renderHojaFilter() {
    const select = document.getElementById('filterHoja');
    if (!select) return;

    const hojas = [...new Set(allData.map(r => (r[13] ?? '').trim()).filter(Boolean))].sort();
    const valorActual = select.value;
    select.innerHTML = '<option value="">Todas las hojas</option>';
    hojas.forEach(h => {
        const opt = document.createElement('option');
        opt.value = h;
        opt.textContent = h;
        if (h === valorActual) opt.selected = true;
        select.appendChild(opt);
    });
}

function renderAuxiliarFilter() {
    const select = document.getElementById('filterAuxiliar');
    if (!select) return;

    // Auxiliar está en columna 10
    const auxiliares = [...new Set(allData.map(r => (r[10] ?? '').trim()).filter(Boolean))].sort();
    const valorActual = select.value;
    select.innerHTML = '<option value="">Todos los auxiliares</option>';
    auxiliares.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a;
        opt.textContent = a;
        if (a === valorActual) opt.selected = true;
        select.appendChild(opt);
    });
}

/* =====================================================
   RENDERIZADO DE TABLA
   ===================================================== */
function renderTable() {
    const tbody      = document.getElementById('sheet-tbody');
    const totalRows  = filteredData.length;
    const totalPages = Math.ceil(totalRows / LIMIT_PER_PAGE);
    const startIdx   = (currentPage - 1) * LIMIT_PER_PAGE;
    const pageData   = filteredData.slice(startIdx, startIdx + LIMIT_PER_PAGE);

    // Tbody
    if (pageData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="14" class="p-12 text-center text-slate-400 text-sm font-bold">
                    No se encontraron registros con ese filtro.
                </td>
            </tr>`;
    } else {
        tbody.innerHTML = pageData.map((row, idx) => {
            const globalIdx = startIdx + idx + 1;

            const descripcion  = escHtml(row[0]  ?? '');
            const cliente      = escHtml(row[1]  ?? '');
            const dni          = escHtml(row[2]  ?? '');
            const celular      = escHtml(row[3]  ?? '');
            const nivel        = (row[4] ?? '').trim().toUpperCase();
            const carrera      = escHtml(row[5]  ?? '');
            const universidad  = escHtml(row[6]  ?? '');
            const linkDrive    = (row[7]  ?? '').trim();
            const fIngreso     = escHtml(row[8]  ?? '');
            const jefe         = escHtml(row[9]  ?? '');
            const auxiliar     = escHtml(row[10] ?? '');
            const fEntrega     = escHtml(row[11] ?? '');
            const horas        = escHtml(row[12] ?? '');
            const hoja         = escHtml(row[13] ?? '');

            const nivelBadge = renderNivelBadge(nivel);

            const driveCell = linkDrive
                ? `<a href="${linkDrive}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 hover:text-blue-800 transition-colors underline underline-offset-2">
                       <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                       Ver Drive
                   </a>`
                : '<span class="text-slate-300 text-[10px]">—</span>';

            return `
            <tr class="import-tr" data-idx="${startIdx + idx}">
                <td class="import-td text-center font-black text-slate-400 text-[11px]">${globalIdx}</td>
                <td class="import-td">
                    <div class="flex items-center gap-2">
                        <span title="${descripcion}" class="truncate block font-semibold text-slate-700 max-w-[120px]">${descripcion || '<span class="text-slate-300">—</span>'}</span>
                        <select class="select-actividad w-28 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded px-1.5 py-1 text-[9px] font-bold uppercase outline-none cursor-pointer text-slate-600 transition-colors ts-table-actividad" onmousedown="cargarTareas(this)" onclick="event.stopPropagation()">
                            <option value="">+ Tarea</option>
                        </select>
                    </div>
                </td>
                <td class="import-td">
                    <input type="text"
                        class="input-tiempo w-20 text-center text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded px-1.5 py-1 outline-none focus:ring-2 focus:ring-emerald-400/30 focus:bg-white transition-all placeholder:text-slate-300"
                        placeholder="—"
                        title="Editable: ej. 2H 30m">
                </td>
                <td class="import-td font-bold text-slate-800" title="${cliente}">${cliente || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-slate-500 font-mono text-[11px]">${dni || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-slate-600">${celular || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td">${nivelBadge}</td>
                <td class="import-td text-slate-600" title="${carrera}">${carrera || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td font-semibold text-slate-700" title="${universidad}">${universidad || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td">${driveCell}</td>
                <td class="import-td text-slate-500 text-[11px]">${fIngreso || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-slate-600" title="${jefe}">${jefe || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-slate-600" title="${auxiliar}">${auxiliar || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-slate-500 text-[11px]">${fEntrega || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-center font-bold text-slate-700">${horas || '<span class="text-slate-300">—</span>'}</td>
                <td class="import-td text-center font-bold text-indigo-600 bg-indigo-50/30 rounded-r-xl border-l border-indigo-100/50">${hoja || '<span class="text-slate-300">—</span>'}</td>
            </tr>`;
        }).join('');
    }

    // Contador
    document.getElementById('filas-count').textContent = totalRows;

    // Paginación
    renderPagination(totalRows, totalPages);
}

function renderNivelBadge(nivel) {
    if (nivel.includes('POSGRADO')) {
        return `<span class="nivel-badge nivel-posgrado">${nivel}</span>`;
    } else if (nivel.includes('PREGRADO')) {
        return `<span class="nivel-badge nivel-pregrado">${nivel}</span>`;
    } else if (nivel) {
        return `<span class="nivel-badge nivel-otro">${nivel}</span>`;
    }
    return '<span class="text-slate-300 text-[10px]">—</span>';
}

/* =====================================================
   PAGINACIÓN
   ===================================================== */
function renderPagination(totalRows, totalPages) {
    const info     = document.getElementById('pag-info');
    const controls = document.getElementById('pag-controls');

    const startIdx = (currentPage - 1) * LIMIT_PER_PAGE + 1;
    const endIdx   = Math.min(currentPage * LIMIT_PER_PAGE, totalRows);

    info.textContent = totalRows > 0
        ? `Mostrando ${startIdx}–${endIdx} de ${totalRows} registros`
        : 'Sin resultados';

    if (totalPages <= 1) {
        controls.innerHTML = '';
        return;
    }

    let html = '';

    // Botón prev
    html += `<button class="pag-btn ${currentPage === 1 ? 'pag-btn-disabled' : ''}" onclick="goToPage(${currentPage - 1})">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
             </button>`;

    // Números de página
    const pages = buildPageRange(currentPage, totalPages);
    pages.forEach(p => {
        if (p === '...') {
            html += `<span class="pag-btn" style="pointer-events:none;border:none;color:#cbd5e1">…</span>`;
        } else {
            html += `<button class="pag-btn ${p === currentPage ? 'pag-btn-active' : ''}" onclick="goToPage(${p})">${p}</button>`;
        }
    });

    // Botón next
    html += `<button class="pag-btn ${currentPage === totalPages ? 'pag-btn-disabled' : ''}" onclick="goToPage(${currentPage + 1})">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
             </button>`;

    controls.innerHTML = html;
}

function buildPageRange(current, total) {
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
    if (current <= 4) return [1, 2, 3, 4, 5, '...', total];
    if (current >= total - 3) return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
    return [1, '...', current - 1, current, current + 1, '...', total];
}

function goToPage(page) {
    const totalPages = Math.ceil(filteredData.length / LIMIT_PER_PAGE);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderTable();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* =====================================================
   BÚSQUEDA Y FILTRO
   ===================================================== */
function applyFilters() {
    const search    = (document.getElementById('searchInput').value ?? '').trim().toLowerCase();
    const nivel     = (document.getElementById('filterNivel').value ?? '').trim().toUpperCase();
    const hoja      = (document.getElementById('filterHoja').value ?? '').trim();
    const auxiliar  = (document.getElementById('filterAuxiliar').value ?? '').trim();

    filteredData = allData.filter(row => {
        const rowStr      = row.join(' ').toLowerCase();
        const matchSearch   = !search   || rowStr.includes(search);
        const matchNivel    = !nivel    || (row[4]  ?? '').toUpperCase().includes(nivel);
        const matchHoja     = !hoja     || (row[13] ?? '') === hoja;
        const matchAuxiliar = !auxiliar || (row[10] ?? '').trim() === auxiliar;
        return matchSearch && matchNivel && matchHoja && matchAuxiliar;
    });

    currentPage = 1;
    renderTable();
}

/* =====================================================
   ESTADOS DE UI
   ===================================================== */
function showState(state) {
    const ids = ['placeholder-inicial', 'loading-state', 'error-state', 'table-toolbar', 'table-wrapper', 'table-pagination'];
    ids.forEach(id => document.getElementById(id)?.classList.add('hidden'));

    if (state === 'loading') {
        document.getElementById('loading-state').classList.remove('hidden');
    } else if (state === 'error') {
        document.getElementById('error-state').classList.remove('hidden');
    } else if (state === 'table') {
        document.getElementById('table-toolbar').classList.remove('hidden');
        document.getElementById('table-wrapper').classList.remove('hidden');
        document.getElementById('table-pagination').classList.remove('hidden');
    } else {
        document.getElementById('placeholder-inicial').classList.remove('hidden');
    }
}

function showError(msg) {
    document.getElementById('error-msg').textContent = msg;
    showState('error');
}

/* =====================================================
   HELPERS
   ===================================================== */
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* =====================================================
   TAREAS (SELECT)
   ===================================================== */
let cacheTareasOptions = null;
let isFetchingTareas = false;

async function cargarTareas(selectElement) {
    if (selectElement.tomselect) return; // Ya es TomSelect

    if (cacheTareasOptions === null && !isFetchingTareas) {
        isFetchingTareas = true;
        const originalText = selectElement.options[0].text;
        selectElement.options[0].text = 'Cargando...';
        
        try {
            const res = await fetch(window.location.origin + '/lista-tareas/list?limit=1000');
            const json = await res.json();
            if (json.status === 'success') {
                cacheTareasOptions = [{ value: '', text: '+ Tarea', minutos: null }];
                json.data.forEach(t => {
                    cacheTareasOptions.push({
                        value: t.id,
                        text: escHtml(t.nombre),
                        minutos: t.horas_estimadas ? parseInt(t.horas_estimadas) : null
                    });
                });
            } else {
                selectElement.options[0].text = 'Error';
            }
        } catch (e) {
            console.error('Error cargando tareas:', e);
            selectElement.options[0].text = 'Error';
        } finally {
            isFetchingTareas = false;
        }
    } else if (isFetchingTareas) {
        return; // Esperando que la primera petición termine
    }

    if (cacheTareasOptions) {
        // Inicializar Tom Select dinámicamente con los datos
        const ts = new TomSelect(selectElement, {
            create: false,
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            options: cacheTareasOptions,
            placeholder: '+ TAREA',
            maxOptions: 50,
            dropdownParent: 'body',
            dropdownClass: 'ts-dropdown ts-table-actividad-dropdown',
            onChange: function(value) {
                this.blur();
                // Buscar la fila padre y actualizar el input de tiempo
                const row = selectElement.closest('tr');
                if (!row) return;
                const tiempoInput = row.querySelector('.input-tiempo');
                if (!tiempoInput) return;

                if (!value) {
                    tiempoInput.value = '';
                    return;
                }
                const tarea = cacheTareasOptions.find(o => String(o.value) === String(value));
                if (tarea && tarea.minutos) {
                    tiempoInput.value = minutosAHoras(tarea.minutos);
                } else {
                    tiempoInput.value = '';
                }
            },
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results px-2 py-1 text-slate-400 text-[10px]">No se encontraron resultados</div>';
                }
            }
        });
        
        // Abrir inmediatamente ya que el usuario hizo click
        setTimeout(() => ts.open(), 50);
    }
}

function minutosAHoras(minutos) {
    if (!minutos || minutos <= 0) return '';
    const h = Math.floor(minutos / 60);
    const m = minutos % 60;
    if (h > 0 && m > 0) return `${h}H ${m}m`;
    if (h > 0) return `${h}H`;
    return `${m}m`;
}

/* =====================================================
   INIT
   ===================================================== */
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    document.getElementById('searchInput')?.addEventListener('input', applyFilters);
    document.getElementById('filterNivel')?.addEventListener('change', applyFilters);
    document.getElementById('filterHoja')?.addEventListener('change', applyFilters);
    document.getElementById('filterAuxiliar')?.addEventListener('change', applyFilters);
});
