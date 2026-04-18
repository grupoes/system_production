<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Universidades</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Administración de instituciones de educación superior.</p>
        </div>
        <button onclick="openModal('modal_universidad')" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Universidad
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" placeholder="Buscar universidades..." class="table-search-input">
            </div>
            <div class="flex items-center gap-2">
                <!-- Records per Page Selector -->
                <div class="relative text-left">
                    <select class="table-action-btn appearance-none pr-8 bg-slate-50/50 border border-slate-100 rounded-lg outline-none cursor-pointer text-xs font-bold text-slate-600">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <i data-lucide="chevrons-up-down" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-2.5 h-2.5 text-slate-400 pointer-events-none"></i>
                </div>

                <!-- Filter Container -->
                <div class="relative text-left">
                    <button onclick="toggleFilter(event)" class="table-action-btn flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-600">Filtrar</span>
                        <i data-lucide="filter" class="w-3 h-3 text-slate-400"></i>
                    </button>

                    <!-- Filter Dropdown Menu -->
                    <div id="filter-dropdown" class="filter-dropdown">
                        <div class="mb-4">
                            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Opciones de Filtro</h3>
                            <p class="text-[10px] text-slate-400 mt-1">Refina la lista de universidades</p>
                        </div>

                        <div class="space-y-4">
                            <div class="form-group mb-0">
                                <label class="form-label text-[10px]">Estado</label>
                                <div class="flex gap-2">
                                    <button type="button" onclick="selectStatus(this)" class="filter-pill active">Activos</button>
                                    <button type="button" onclick="selectStatus(this)" class="filter-pill">Inactivos</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between gap-3">
                            <button onclick="toggleFilter(event)" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 transition-colors">Limpiar</button>
                            <button onclick="toggleFilter(event)" class="flex-1 py-2.5 bg-indigo-600 text-white rounded-xl text-[10px] font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">Aplicar Filtros</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid Table -->
        <div class="table-grid-wrapper">
            <table class="table-grid text-left">
                <thead>
                    <tr>
                        <th class="table-th w-10 text-center">
                            <input type="checkbox" class="w-3.5 h-3.5 rounded-none border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="table-th w-16">#</th>
                        <th class="table-th">Nombre de Universidad <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Sigla</th>
                        <th class="table-th text-right px-6">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $universidades_mock = [
                        ['id' => 1, 'nombre' => 'Universidad Nacional Mayor de San Marcos', 'sigla' => 'UNMSM'],
                        ['id' => 2, 'nombre' => 'Pontificia Universidad Católica del Perú', 'sigla' => 'PUCP'],
                        ['id' => 3, 'nombre' => 'Universidad Nacional de Ingeniería', 'sigla' => 'UNI'],
                        ['id' => 4, 'nombre' => 'Universidad de Lima', 'sigla' => 'ULIMA'],
                        ['id' => 5, 'nombre' => 'Universidad Peruana de Ciencias Aplicadas', 'sigla' => 'UPC'],
                    ];
                    foreach ($universidades_mock as $u): ?>
                    <tr class="group">
                        <td class="table-td text-center border-slate-50">
                            <input type="checkbox" class="w-3.5 h-3.5 rounded-none border-slate-200 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="table-td border-slate-50">
                            <span class="text-xs font-bold text-slate-400">#<?= str_pad($u['id'], 2, '0', STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="table-td border-slate-50">
                            <span class="text-sm font-bold text-slate-800 tracking-tight"><?= $u['nombre'] ?></span>
                        </td>
                        <td class="table-td border-slate-50 text-left">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest border border-slate-200/50">
                                <?= $u['sigla'] ?>
                            </span>
                        </td>
                        <td class="table-td text-right px-6 border-slate-50">
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
            <p class="table-info-text text-left">Mostrando del 1 al 5 de 12 universidades registradas</p>
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

<!-- Modal: Nueva/Editar Universidad -->
<div id="modal_universidad" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header border-b border-slate-50">
            <h2 class="modal-title">Configurar Universidad</h2>
            <button onclick="closeModal('modal_universidad')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="#" method="POST">
            <div class="modal-body space-y-4 py-6 text-left">
                <div class="form-group">
                    <label class="form-label">Nombre de la Universidad</label>
                    <input type="text" class="form-input" placeholder="Ej: Universidad Nacional de San Agustín">
                </div>
                <div class="form-group">
                    <label class="form-label">Sigla</label>
                    <input type="text" class="form-input" placeholder="Ej: UNSA">
                </div>
            </div>
            <div class="modal-footer border-t border-slate-50">
                <button type="button" onclick="closeModal('modal_universidad')" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary px-8">Guardar Universidad</button>
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
        event.stopPropagation();
        const dropdown = document.getElementById('filter-dropdown');
        dropdown.classList.toggle('active');
    }

    function selectStatus(btn) {
        const parent = btn.parentElement;
        parent.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
    }

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('filter-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
</script>
<?= $this->endSection() ?>