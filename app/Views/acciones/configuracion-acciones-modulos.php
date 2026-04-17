<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight text-left">Capacidad de Módulos</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium text-left">Configuración técnica de operaciones permitidas por submódulo.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-6 py-2.5 bg-white border border-slate-100 text-slate-500 rounded-xl hover:bg-slate-50 transition-all text-xs font-bold shadow-sm">
                Descartar Cambios
            </button>
            <button class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 text-xs font-bold">
                Guardar Configuración
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 text-left">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-1.5">
            <h3 class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Módulos del Sistema</h3>
            <button class="w-full flex items-center justify-between px-5 py-3.5 bg-indigo-600 text-white rounded-2xl shadow-xl shadow-indigo-100 transition-all group">
                <div class="flex items-center gap-3">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span class="text-xs font-bold tracking-tight">Configuración</span>
                </div>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-50"></i>
            </button>
            <button class="w-full flex items-center justify-between px-5 py-3.5 bg-white text-slate-500 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group">
                <div class="flex items-center gap-3">
                    <i data-lucide="file-text" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600"></i>
                    <span class="text-xs font-bold tracking-tight">Facturación</span>
                </div>
            </button>
            <button class="w-full flex items-center justify-between px-5 py-3.5 bg-white text-slate-500 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group">
                <div class="flex items-center gap-3">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600"></i>
                    <span class="text-xs font-bold tracking-tight">Ventas</span>
                </div>
            </button>
        </div>

        <!-- Configuration Grid -->
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php 
                $submodulos = [
                    ['name' => 'Usuarios', 'desc' => 'Gestión de cuentas y perfiles'],
                    ['name' => 'Roles', 'desc' => 'Niveles de acceso y jerarquías'],
                    ['name' => 'Permisos', 'desc' => 'Matriz de acciones por rol'],
                    ['name' => 'Módulos', 'desc' => 'Estructura del menú lateral'],
                ];
                foreach ($submodulos as $sub): ?>
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm hover:border-indigo-100 transition-all group/card">
                    <div class="flex flex-col gap-1 mb-6">
                        <h4 class="text-base font-black text-slate-900 tracking-tight group-hover/card:text-indigo-600 transition-colors"><?= $sub['name'] ?></h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?= $sub['desc'] ?></p>
                    </div>

                    <!-- Resumen Compacto Profesional (Color del Sistema) -->
                    <div class="flex flex-wrap gap-2 mb-6 text-left">
                        <span class="px-2.5 py-1 bg-indigo-600 text-white rounded-lg text-[9px] font-bold uppercase tracking-widest">Ver Acceso</span>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-bold uppercase tracking-widest">Crear</span>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-bold uppercase tracking-widest">Editar</span>
                        <span class="px-2.5 py-1 bg-slate-50 text-slate-400 rounded-lg text-[9px] font-bold uppercase tracking-widest">+2 más</span>
                    </div>
                    
                    <div class="pt-5 border-t border-slate-50">
                        <button onclick="openModal('modal_gestionar_acciones')" class="w-full py-3 bg-white hover:bg-indigo-600 hover:text-white text-slate-600 border border-slate-100 hover:border-indigo-600 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all shadow-sm flex items-center justify-center gap-2">
                            <i data-lucide="layers" class="w-3.5 h-3.5 transition-transform group-hover/card:scale-110"></i>
                            Configurar Capacidad
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gestionar Capacidad (System Blue Style) -->
<div id="modal_gestionar_acciones" class="modal-backdrop">
    <div class="modal-container modal-xl">
        <div class="modal-header border-b border-slate-50">
            <div>
                <h2 class="text-lg font-black text-slate-900 tracking-tight">Capacidad Técnica</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Define las acciones operativas disponibles</p>
            </div>
            <button onclick="closeModal('modal_gestionar_acciones')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="modal-body space-y-6 py-8">
            <!-- Buscador Minimalista -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" placeholder="Buscar operación..." class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none text-slate-600">
            </div>

            <!-- Grid de Acciones (Blue Focus) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                <?php 
                $acciones_pool = [
                    ['name' => 'Ver / Listar', 'checked' => true],
                    ['name' => 'Crear', 'checked' => true],
                    ['name' => 'Editar', 'checked' => true],
                    ['name' => 'Eliminar', 'checked' => false],
                    ['name' => 'Exportar PDF', 'checked' => false],
                    ['name' => 'Exportar Excel', 'checked' => false],
                    ['name' => 'Importar Datos', 'checked' => false],
                    ['name' => 'Imprimir', 'checked' => false],
                    ['name' => 'Aprobar', 'checked' => false],
                    ['name' => 'Anular', 'checked' => false],
                    ['name' => 'Enviar Email', 'checked' => false],
                ];
                foreach ($acciones_pool as $acc): ?>
                <label class="cursor-pointer group/chip text-left">
                    <input type="checkbox" class="sr-only peer" <?= $acc['checked'] ? 'checked' : '' ?>>
                    <div class="p-4 rounded-2xl border border-slate-100 bg-white text-slate-500 transition-all peer-checked:text-white peer-checked:border-indigo-600 peer-checked:bg-indigo-600 hover:border-indigo-200">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-[11px] font-bold uppercase tracking-widest"><?= $acc['name'] ?></span>
                            <div class="w-4 h-4 rounded-full border border-slate-200 flex items-center justify-center peer-checked:border-white transition-all">
                                <i data-lucide="check" class="w-2.5 h-2.5 opacity-0 peer-checked:opacity-100"></i>
                            </div>
                        </div>
                        <span class="text-[9px] opacity-60 font-medium normal-case block leading-tight">Acción operativa del módulo</span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="modal-footer border-t border-slate-50">
            <button type="button" onclick="closeModal('modal_gestionar_acciones')" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors mr-auto pl-4">Cancelar</button>
            <button type="button" class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all">Confirmar Capacidad</button>
        </div>
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
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #e2e8f0;
    }
</style>
<?= $this->endSection() ?>