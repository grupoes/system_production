<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Lista de Clientes</h1>
            <p class="text-sm text-slate-500 mt-1">Gestión de clientes convertidos desde prospectos.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Buscar clientes..." class="table-search-input">
            </div>
            <div class="flex items-center gap-2">
                <!-- Records per Page Selector -->
                <div class="relative">
                    <select id="limitSelect" class="table-action-btn appearance-none pr-8 bg-slate-50/50 border border-slate-100 rounded-lg outline-none cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
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
                        <th class="table-th w-10 text-center">#</th>
                        <th class="table-th">Contactos</th>
                        <th class="table-th">Universidad</th>
                        <th class="table-th">Carrera</th>
                        <th class="table-th">Título del Proyecto</th>
                        <th class="table-th">Registro Cliente</th>
                        <th class="table-th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="clientes-tbody">
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="loader-2" class="w-8 h-8 text-emerald-500 animate-spin mb-3"></i>
                                <p class="text-sm font-bold text-slate-700">Cargando clientes...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="table-footer">
            <p id="pagination-info" class="table-info-text">Mostrando resultados...</p>
            <div id="pagination-controls" class="flex items-center gap-1.5">
                <!-- Filled via JS -->
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/pages/prospectos/clientes_lista.js') ?>"></script>

<!-- Modal: Programar Actividad -->
<div id="modal-programar" class="hidden fixed inset-0 z-[100] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeScheduleModal()"></div>
        
        <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full overflow-hidden border border-slate-100 transform transition-all">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight uppercase">Programar Actividad</h3>
                    </div>
                    <button onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form id="form-programar" class="space-y-4">
                    <input type="hidden" id="prog-prospecto-id">
                    
                    <div class="form-group">
                        <label class="form-label">Tarea a realizar</label>
                        <select id="prog-tarea-id" class="form-input"></select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Auxiliar Responsable</label>
                        <select id="prog-usuario-id" class="form-input" onchange="checkUserResponsibility()"></select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prioridad</label>
                        <select id="prog-prioridad" class="form-input">
                            <option value="BAJA">🟢 BAJA</option>
                            <option value="NORMAL" selected>🟡 NORMAL</option>
                            <option value="ALTA">🔴 ALTA</option>
                        </select>
                    </div>

                    <!-- Campos condicionales -->
                    <div id="extra-schedule-fields" class="hidden grid grid-cols-2 gap-4 animate-fade-in">
                        <div class="form-group">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="date" id="prog-fecha" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hora de Inicio</label>
                            <input type="time" id="prog-hora" class="form-input">
                        </div>
                    </div>
                    
                    <div id="responsibility-alert" class="hidden p-4 bg-emerald-50 rounded-2xl border border-emerald-100 mb-4">
                        <div class="flex gap-3">
                            <i data-lucide="info" class="w-4 h-4 text-emerald-500 mt-0.5"></i>
                            <p class="text-[10px] font-bold text-emerald-700 uppercase leading-tight">Este auxiliar es responsable hoy. La tarea se asignará a su lista de pendientes automáticamente.</p>
                        </div>
                    </div>
                </form>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeScheduleModal()" class="flex-1 btn-secondary">Cancelar</button>
                    <button type="button" onclick="confirmSchedule()" class="flex-1 btn-primary">Programar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Table Styling */
    .table-container {
        @apply bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden;
    }

    .table-toolbar {
        @apply p-6 flex items-center justify-between gap-4 border-b border-slate-50;
    }

    .table-search-box {
        @apply relative flex-1 max-w-md;
    }

    .table-search-input {
        @apply w-full bg-slate-50/50 border-none rounded-xl py-2.5 pl-11 pr-4 text-sm focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none font-medium;
    }

    .table-action-btn {
        @apply px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all outline-none;
    }

    .table-grid-wrapper {
        @apply overflow-x-auto;
    }

    .table-grid {
        @apply w-full border-collapse;
    }

    .table-th {
        @apply px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left border-b border-slate-50;
    }

    .table-td {
        @apply px-6 py-5 text-sm text-slate-600 border-b border-slate-50/50;
    }

    .table-td-bold {
        @apply font-bold text-slate-800;
    }

    .table-row-hover:hover {
        @apply bg-emerald-50/30;
    }

    .table-footer {
        @apply p-6 flex items-center justify-between bg-slate-50/30;
    }

    .table-info-text {
        @apply text-xs font-bold text-slate-400;
    }

    .pagination-nav-btn {
        @apply w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-100 text-slate-400 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all disabled:opacity-50 disabled:pointer-events-none shadow-sm;
    }

    .pagination-dot {
        @apply w-8 h-8 flex items-center justify-center rounded-lg text-xs font-black transition-all cursor-pointer;
    }

    .pagination-dot-active {
        @apply bg-emerald-500 text-white shadow-lg shadow-emerald-200;
    }

    .pagination-dot-inactive {
        @apply bg-white border border-slate-100 text-slate-400 hover:bg-slate-50 shadow-sm;
    }
</style>
<?= $this->endSection() ?>
