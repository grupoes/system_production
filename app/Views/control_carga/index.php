<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- FullCalendar Styles -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
<style>
    .fc {
        --fc-border-color: #f1f5f9;
        --fc-today-bg-color: #f8fafc;
        --fc-button-bg-color: #6366f1;
        --fc-button-border-color: #6366f1;
        --fc-button-hover-bg-color: #4f46e5;
        --fc-button-hover-border-color: #4f46e5;
        --fc-button-active-bg-color: #4338ca;
        --fc-button-active-border-color: #4338ca;
        font-family: inherit;
    }
    .fc .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }
    .fc .fc-col-header-cell-cushion {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        padding: 10px 0;
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f1f5f9;
    }
    .external-event {
        cursor: move;
    }
    .external-event:hover {
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
</style>

<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Control de Carga</h1>
            <p class="text-sm text-slate-500 mt-1">Gestión de actividades y asignación de tiempos.</p>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <!-- Sidebar: Pending Activities -->
        <div class="col-span-12 lg:col-span-4 xl:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
                <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-indigo-500"></i>
                        Actividades Pendientes
                    </h3>
                </div>
                <div class="p-2 max-h-[calc(100vh-250px)] overflow-y-auto" id="external-events">
                    <!-- Activities will be loaded here -->
                    <div id="loading-activities" class="p-8 text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-indigo-500 border-t-transparent"></div>
                        <p class="text-xs text-slate-400 mt-2 font-medium">Cargando actividades...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="col-span-12 lg:col-span-8 xl:col-span-9">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <!-- User Filter Header -->
                <div class="flex items-center justify-between mb-6 gap-4">
                    <div class="flex-1 max-w-sm">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Auxiliar Responsable</label>
                        <div class="relative">
                            <select id="main-usuario-id" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl text-sm font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 transition-all appearance-none cursor-pointer">
                                <option value="">Seleccionar un usuario para ver su horario...</option>
                            </select>
                            <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                            <i data-lucide="chevron-down" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-100">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight">Tiempo Real</span>
                        </div>
                    </div>
                </div>

                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Asignar Actividad -->
<div id="modal-asignar" class="hidden fixed inset-0 z-[150] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeAsignarModal()"></div>
        
        <div class="relative bg-white rounded-3xl shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full overflow-hidden border border-slate-100">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">Asignar Actividad</h3>
                        <p class="text-xs text-slate-500 mt-1">Configure los detalles de la asignación para el auxiliar.</p>
                    </div>
                    <button onclick="closeAsignarModal()" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 rounded-xl transition-all">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form id="form-asignar" class="space-y-6 text-left">
                    <input type="hidden" id="asig-actividad-id">
                    
                    <!-- Prospect Info Section (Informational) -->
                    <div class="p-5 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Título del Proyecto</label>
                                <div id="asig-titulo" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-bold text-slate-700 shadow-sm"></div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Universidad</label>
                                <div id="asig-universidad" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-semibold text-slate-600 shadow-sm"></div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Carrera</label>
                                <div id="asig-carrera" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-semibold text-slate-600 shadow-sm"></div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nivel Académico</label>
                                <div id="asig-nivel" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-semibold text-slate-600 shadow-sm"></div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Origen</label>
                                <div id="asig-origen" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-semibold text-slate-600 shadow-sm"></div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Prioridad</label>
                                <div id="asig-prioridad" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-bold shadow-sm"></div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Contactos</label>
                                <div id="asig-contactos" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm font-semibold text-slate-600 shadow-sm"></div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Link Drive</label>
                                <a id="asig-link-drive" href="#" target="_blank" class="flex items-center gap-2 px-4 py-2.5 bg-indigo-50 border border-indigo-100 rounded-xl text-sm font-bold text-indigo-600 hover:bg-indigo-100 transition-all">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                    Abrir Documento
                                </a>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Observaciones</label>
                                <div id="asig-observaciones" class="px-4 py-2.5 bg-white border border-slate-100 rounded-xl text-sm text-slate-600 min-h-[60px] italic shadow-sm"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Fields -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Actividad a Realizar</label>
                                <input type="text" id="asig-actividad-nombre" readonly class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Usuario Asignado</label>
                                <select id="asig-usuario-id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer">
                                    <option value="">Seleccionar auxiliar...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hora Inicio</label>
                                <div class="relative">
                                    <input type="time" id="asig-hora-inicio" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    <i data-lucide="clock" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hora Fin</label>
                                <div class="relative">
                                    <input type="time" id="asig-hora-fin" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    <i data-lucide="clock-alert" class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="closeAsignarModal()" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl hover:bg-slate-200 transition-all font-bold text-sm">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all active:scale-95 font-bold text-sm">
                            Confirmar Asignación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    const DEFAULT_USER_ID = <?= json_encode($defaultUserId) ?>;
    const BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('js/pages/control_carga/index.js') ?>"></script>

<?= $this->endSection() ?>
