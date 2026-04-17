<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Acciones</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Define las operaciones disponibles (Crear, Editar, etc.) para los módulos.</p>
        </div>
        <button onclick="openModal('modal_accion')" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Acción
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" placeholder="Buscar acciones..." class="table-search-input">
            </div>
            <div class="flex items-center gap-2">
                <!-- Records per Page Selector -->
                <div class="relative">
                    <select class="table-action-btn appearance-none pr-8 bg-slate-50/50 border border-slate-100 rounded-lg outline-none cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
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
                        <th class="table-th w-10 text-center">
                            <input type="checkbox" class="w-3.5 h-3.5 rounded-none border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="table-th w-16">#</th>
                        <th class="table-th">Nombre Acción <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Descripción</th>
                        <th class="table-th text-right px-6">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $acciones_mock = [
                        ['id' => 1, 'nombre' => 'Ver / Listar', 'desc' => 'Permite visualizar el contenido y listados del módulo.', 'color' => 'bg-emerald-50 text-emerald-600'],
                        ['id' => 2, 'nombre' => 'Crear', 'desc' => 'Habilita el registro de nuevos datos en el sistema.', 'color' => 'bg-indigo-50 text-indigo-600'],
                        ['id' => 3, 'nombre' => 'Editar', 'desc' => 'Permite la modificación de registros existentes.', 'color' => 'bg-amber-50 text-amber-600'],
                        ['id' => 4, 'nombre' => 'Eliminar', 'desc' => 'Habilita la baja lógica o física de información.', 'color' => 'bg-rose-50 text-rose-600'],
                        ['id' => 5, 'nombre' => 'Exportar', 'desc' => 'Permite descargar reportes en formatos PDF o Excel.', 'color' => 'bg-slate-50 text-slate-600'],
                    ];
                    foreach ($acciones_mock as $a): ?>
                    <tr class="group">
                        <td class="table-td text-center">
                            <input type="checkbox" class="w-3.5 h-3.5 rounded-none border-slate-200 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="table-td">
                            <span class="text-xs font-bold text-slate-400">#<?= str_pad($a['id'], 2, '0', STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="table-td">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg <?= $a['color'] ?> text-[11px] font-black uppercase tracking-widest">
                                <?= $a['nombre'] ?>
                            </span>
                        </td>
                        <td class="table-td">
                            <p class="text-xs font-medium text-slate-600 max-w-md"><?= $a['desc'] ?></p>
                        </td>
                        <td class="table-td text-right px-6">
                            <div class="flex items-center justify-end gap-2">
                                <button class="p-2 hover:bg-indigo-50 rounded-lg text-indigo-600 transition-all" title="Editar">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <button class="p-2 hover:bg-rose-50 rounded-lg text-rose-600 transition-all" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="table-footer border-t border-slate-50">
            <p class="table-info-text">Mostrando del 1 al 5 de 8 acciones registradas</p>
            <div class="flex items-center gap-1.5">
                <button class="pagination-nav-btn">
                    <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
                </button>
                <div class="pagination-dot pagination-dot-active">1</div>
                <button class="pagination-dot pagination-dot-inactive">2</button>
                <button class="pagination-nav-btn rotate-180">
                    <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nueva/Editar Acción -->
<div id="modal_accion" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Acción</h2>
            <button onclick="closeModal('modal_accion')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="#" method="POST">
            <div class="modal-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombre de la Acción</label>
                    <input type="text" class="form-input" placeholder="Ej: Imprimir Ticket">
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-input resize-none" rows="3" placeholder="Define qué permite hacer esta acción en el sistema..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_accion')" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Acción</button>
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
</script>
<?= $this->endSection() ?>