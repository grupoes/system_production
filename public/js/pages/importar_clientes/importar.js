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
                <td class="import-td font-semibold text-slate-700" title="${descripcion}">${descripcion || '<span class="text-slate-300">—</span>'}</td>
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
    const search = (document.getElementById('searchInput').value ?? '').trim().toLowerCase();
    const nivel  = (document.getElementById('filterNivel').value ?? '').trim().toUpperCase();

    filteredData = allData.filter(row => {
        const rowStr = row.join(' ').toLowerCase();
        const matchSearch = !search || rowStr.includes(search);
        const matchNivel  = !nivel || (row[4] ?? '').toUpperCase().includes(nivel);
        return matchSearch && matchNivel;
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
   INIT
   ===================================================== */
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    document.getElementById('searchInput')?.addEventListener('input', applyFilters);
    document.getElementById('filterNivel')?.addEventListener('change', applyFilters);
});
