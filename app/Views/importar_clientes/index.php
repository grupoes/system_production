<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">

    <!-- Header Page -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Importar Clientes</h1>
            <p class="text-sm text-slate-500 mt-1">Carga masiva de clientes históricos desde Google Sheets.</p>
        </div>
        <button id="btn-cargar" onclick="cargarDatosSheet()"
            class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="table-2" class="w-4 h-4"></i>
            Cargar datos de Google Sheets
        </button>
    </div>

    <!-- Stats Bar -->
    <div id="stats-bar" class="hidden">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total filas</p>
                    <p id="stat-total" class="text-xl font-black text-slate-800">0</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Universidades</p>
                    <p id="stat-univ" class="text-xl font-black text-slate-800">0</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 shrink-0">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nivel Posgrado</p>
                    <p id="stat-posgrado" class="text-xl font-black text-slate-800">0</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nivel Pregrado</p>
                    <p id="stat-pregrado" class="text-xl font-black text-slate-800">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="import-table-container">

        <!-- Placeholder inicial -->
        <div id="placeholder-inicial" class="flex flex-col items-center justify-center py-24 gap-4 text-slate-400">
            <div class="w-20 h-20 rounded-3xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                <i data-lucide="sheet" class="w-10 h-10 text-slate-300"></i>
            </div>
            <div class="text-center">
                <p class="font-bold text-slate-500 text-base">Aún no hay datos cargados</p>
                <p class="text-sm mt-1">Presiona el botón <span class="font-bold text-emerald-600">"Cargar datos de Google Sheets"</span> para comenzar.</p>
            </div>
        </div>

        <!-- Loading state -->
        <div id="loading-state" class="hidden flex flex-col items-center justify-center py-24 gap-4">
            <div class="w-16 h-16 rounded-full border-4 border-emerald-100 border-t-emerald-500 animate-spin"></div>
            <p class="text-sm font-bold text-slate-600">Conectando con Google Sheets...</p>
            <p class="text-xs text-slate-400">Esto puede tardar unos segundos</p>
        </div>

        <!-- Error state -->
        <div id="error-state" class="hidden flex flex-col items-center justify-center py-20 gap-3 text-center px-8">
            <div class="w-16 h-16 rounded-3xl bg-red-50 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-red-400"></i>
            </div>
            <p class="font-bold text-slate-700">Error al conectar con Google Sheets</p>
            <p id="error-msg" class="text-xs text-red-500 bg-red-50 border border-red-100 rounded-xl px-4 py-2.5 max-w-md"></p>
            <button onclick="cargarDatosSheet()" class="mt-2 px-5 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 inline mr-1"></i> Reintentar
            </button>
        </div>

        <!-- Toolbar (solo visible cuando hay datos) -->
        <div id="table-toolbar" class="hidden table-import-toolbar">
            <div class="relative flex-1 max-w-sm">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Buscar en los datos..."
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl py-2.5 pl-11 pr-4 text-sm focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none font-medium">
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-bold">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <select id="filterNivel" class="bg-slate-50 border border-slate-100 rounded-xl px-3 py-2.5 text-xs font-bold outline-none cursor-pointer">
                    <option value="">Todos los niveles</option>
                    <option value="POSGRADO">Posgrado</option>
                    <option value="PREGRADO">Pregrado</option>
                </select>
            </div>
            <p class="text-xs text-slate-400 font-bold ml-auto">
                Mostrando <span id="filas-count" class="text-slate-700">0</span> registros
            </p>
        </div>

        <!-- Data Table -->
        <div id="table-wrapper" class="hidden overflow-x-auto">
            <table class="import-table w-full border-collapse">
                <thead>
                    <tr id="table-head-row">
                        <th class="import-th w-10 text-center">#</th>
                        <th class="import-th">Descripción</th>
                        <th class="import-th">Cliente</th>
                        <th class="import-th">DNI</th>
                        <th class="import-th">Celular</th>
                        <th class="import-th">Nivel</th>
                        <th class="import-th">Carrera</th>
                        <th class="import-th">Universidad</th>
                        <th class="import-th">Link Drive</th>
                        <th class="import-th">F. Ingreso</th>
                        <th class="import-th">Jefe</th>
                        <th class="import-th">Auxiliar</th>
                        <th class="import-th">F. Entrega</th>
                        <th class="import-th">Horas</th>
                    </tr>
                </thead>
                <tbody id="sheet-tbody"></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="table-pagination" class="hidden flex items-center justify-between p-5 border-t border-slate-50 bg-slate-50/30">
            <p id="pag-info" class="text-xs font-bold text-slate-400"></p>
            <div id="pag-controls" class="flex items-center gap-1.5"></div>
        </div>

    </div>
</div>

<style>
    .import-table-container {
        background: white;
        border-radius: 2rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 20px 60px -10px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .table-import-toolbar {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        flex-wrap: wrap;
    }

    .import-table thead tr {
        background: #f8fafc;
    }

    .import-th {
        padding: 0.875rem 1.25rem;
        font-size: 10px;
        font-weight: 900;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }

    .import-td {
        padding: 0.875rem 1.25rem;
        font-size: 12px;
        color: #475569;
        border-bottom: 1px solid #f8fafc;
        white-space: nowrap;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .import-tr:hover {
        background: #f0fdf4;
        transition: background 0.15s;
    }

    .nivel-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .nivel-posgrado {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .nivel-pregrado {
        background: #fef3c7;
        color: #d97706;
    }

    .nivel-otro {
        background: #f1f5f9;
        color: #64748b;
    }

    .pag-btn {
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s;
    }

    .pag-btn:hover {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .pag-btn-active {
        background: #10b981 !important;
        color: white !important;
        border-color: #10b981 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .pag-btn-disabled {
        opacity: 0.4;
        pointer-events: none;
    }
</style>

<script src="<?= base_url('js/pages/importar_clientes/importar.js') ?>"></script>
<?= $this->endSection() ?>