<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Módulos</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">Administra la estructura de menús y accesos del sistema.</p>
        </div>
        <button onclick="openModal('modal_modulo')" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Módulo
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" placeholder="Buscar módulos..." class="table-search-input">
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

                <!-- Filter Container -->
                <div class="relative">
                    <button onclick="toggleFilter(event)" class="table-action-btn">
                        <span>Filtrar</span>
                        <i data-lucide="filter" class="w-3 h-3 text-slate-400"></i>
                    </button>
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
                        <th class="table-th">Módulo <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th text-center">Icono</th>
                        <th class="table-th">URL / Ruta <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Módulo Padre</th>
                        <th class="table-th text-right px-6">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $modulos_mock = [
                        ['id' => 1, 'nombre' => 'Configuración', 'icon' => 'settings', 'url' => '#', 'padre' => '-'],
                        ['id' => 2, 'nombre' => 'Usuarios', 'icon' => 'users', 'url' => 'usuarios', 'padre' => 'Configuración'],
                        ['id' => 3, 'nombre' => 'Roles y Permisos', 'icon' => 'shield-check', 'url' => 'permisos', 'padre' => 'Configuración'],
                        ['id' => 4, 'nombre' => 'Facturación', 'icon' => 'file-text', 'url' => 'facturacion', 'padre' => '-'],
                        ['id' => 5, 'nombre' => 'Ventas', 'icon' => 'shopping-cart', 'url' => 'ventas', 'padre' => 'Facturación'],
                    ];
                    foreach ($modulos_mock as $m): ?>
                    <tr class="group">
                        <td class="table-td text-center">
                            <input type="checkbox" class="w-3.5 h-3.5 rounded-none border-slate-200 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="table-td">
                            <span class="text-xs font-bold text-slate-400">#<?= str_pad($m['id'], 2, '0', STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-white group-hover:shadow-sm transition-all">
                                    <i data-lucide="<?= $m['icon'] ?>" class="w-4 h-4"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800"><?= $m['nombre'] ?></span>
                            </div>
                        </td>
                        <td class="table-td text-center">
                            <code class="text-[10px] font-black px-2 py-1 bg-slate-50 text-slate-500 rounded-md border border-slate-100">
                                <?= $m['icon'] ?>
                            </code>
                        </td>
                        <td class="table-td">
                            <div class="flex items-center gap-1 text-xs font-medium text-slate-500">
                                <span class="text-slate-300">/</span>
                                <span class="font-bold text-slate-600"><?= $m['url'] ?></span>
                            </div>
                        </td>
                        <td class="table-td">
                            <?php if ($m['padre'] !== '-'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-wider">
                                    <i data-lucide="corner-down-right" class="w-3 h-3"></i>
                                    <?= $m['padre'] ?>
                                </span>
                            <?php else: ?>
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Principal</span>
                            <?php endif; ?>
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
            <p class="table-info-text">Mostrando del 1 al 5 de 15 módulos</p>

            <div class="flex items-center gap-1.5">
                <button class="pagination-nav-btn">
                    <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
                </button>
                <div class="pagination-dot pagination-dot-active">1</div>
                <button class="pagination-dot pagination-dot-inactive">2</button>
                <button class="pagination-dot pagination-dot-inactive">3</button>
                <button class="pagination-nav-btn rotate-180">
                    <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo/Editar Módulo -->
<div id="modal_modulo" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Configurar Módulo</h2>
            <button onclick="closeModal('modal_modulo')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="#" method="POST">
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group col-span-2">
                        <label class="form-label">Nombre del Módulo</label>
                        <input type="text" class="form-input" placeholder="Ej: Reportes de Inventario">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Icono (Lucide)</label>
                        <input type="text" class="form-input" placeholder="Ej: box, settings, user">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Módulo Padre</label>
                        <select class="form-input">
                            <option value="">Ninguno (Principal)</option>
                            <option value="1">Configuración</option>
                            <option value="4">Facturación</option>
                        </select>
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">URL / Ruta</label>
                        <input type="text" class="form-input" placeholder="Ej: inventario/reportes">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_modulo')" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Módulo</button>
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

    function toggleFilter(event) {
        const dropdown = document.getElementById('filter-dropdown');
        if (dropdown) dropdown.classList.toggle('active');
    }
</script>
<?= $this->endSection() ?>