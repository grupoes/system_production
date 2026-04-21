<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- DataTables Styles -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Potenciales Clientes</h1>
            <p class="text-sm text-slate-500 mt-1">Gestión y seguimiento de nuevos prospectos registrados.</p>
        </div>
        <a href="<?= base_url('registrar-potencial-cliente') ?>" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Prospecto
        </a>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Buscar prospectos..." class="table-search-input">
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
                        <th class="table-th">Vendedor</th>
                        <th class="table-th">Registro</th>
                        <th class="table-th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="prospectos-tbody">
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="loader-2" class="w-8 h-8 text-indigo-500 animate-spin mb-3"></i>
                                <p class="text-sm font-bold text-slate-700">Cargando prospectos...</p>
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

<!-- Modal Detalle Prospecto -->
<div id="modal-detalle" class="hidden fixed inset-0 z-[200] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDetalleModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl border border-slate-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-violet-600 p-7">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-indigo-200 text-[10px] font-bold uppercase tracking-widest mb-1">Detalle del Prospecto</p>
                        <h3 id="det-titulo" class="text-xl font-black text-white leading-tight"></h3>
                        <div class="flex items-center gap-2 mt-2">
                            <span id="det-estado" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-white/20 text-white"></span>
                            <span id="det-prioridad" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-white/20 text-white"></span>
                        </div>
                    </div>
                    <button onclick="closeDetalleModal()" class="w-9 h-9 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-xl text-white transition-all">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="p-7 space-y-6 max-h-[65vh] overflow-y-auto">
                <!-- Info General -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 bg-slate-50 rounded-2xl p-4 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Universidad</p>
                            <p id="det-universidad" class="text-sm font-bold text-slate-700"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Carrera</p>
                            <p id="det-carrera" class="text-sm font-semibold text-slate-600"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nivel Académico</p>
                            <p id="det-nivel" class="text-sm font-semibold text-slate-600"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Origen</p>
                            <p id="det-origen" class="text-sm font-semibold text-slate-600"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Vendedor</p>
                            <p id="det-vendedor" class="text-sm font-semibold text-slate-600"></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Fecha Entrega</p>
                            <p id="det-fecha-entrega" class="text-sm font-semibold text-slate-600"></p>
                        </div>
                    </div>
                    <!-- Contactos -->
                    <div class="col-span-2">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Contactos</p>
                        <div id="det-contactos" class="flex flex-col gap-1.5"></div>
                    </div>
                    <!-- Observaciones -->
                    <div class="col-span-2">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Observaciones</p>
                        <p id="det-observaciones" class="text-sm text-slate-500 bg-slate-50 rounded-xl p-3 italic"></p>
                    </div>
                </div>

                <!-- Actividades / Tareas -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-black text-slate-700 uppercase tracking-wider">Actividades Asignadas</p>
                        <span id="det-actividades-count" class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black"></span>
                    </div>
                    <div id="det-actividades" class="space-y-2"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-7 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                <a id="det-link-drive" href="#" target="_blank" class="hidden items-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Ver en Drive
                </a>
                <div></div>
                <button onclick="closeDetalleModal()" class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-all text-sm font-bold">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/pages/prospectos/lista.js') ?>"></script>



<style>
    .btn-primary {
        @apply px-6 py-3 bg-indigo-600 text-white rounded-[1.25rem] text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 active:scale-95;
    }

    /* Custom DataTables Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        @apply rounded-xl border-none font-bold text-xs !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        @apply bg-indigo-600 text-white !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        @apply bg-slate-50 border-none rounded-2xl px-5 py-2.5 text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all ml-2 !important;
        min-width: 250px;
    }

    .dataTables_wrapper .dataTables_length select {
        @apply bg-slate-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all mx-2 !important;
    }

    table.dataTable {
        @apply border-separate border-spacing-y-3 !important;
    }

    table.dataTable thead th {
        @apply border-none pb-4 !important;
    }

    table.dataTable tbody tr {
        @apply bg-white shadow-sm rounded-3xl transition-all duration-300 hover:shadow-md hover:translate-y-[-2px] !important;
    }

    table.dataTable tbody td {
        @apply py-5 px-6 border-none first:rounded-l-[2rem] last:rounded-r-[2rem] !important;
    }
</style>
<?= $this->endSection() ?>
