<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Usuarios</h1>
            <p class="text-sm text-slate-500 mt-1">Gestión centralizada de cuentas y accesos.</p>
        </div>
        <button onclick="openModal('modal_usuario')" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nuevo Usuario
        </button>
    </div>

    <!-- Reusable Table Container -->
    <div class="table-container">
        <!-- Top Toolbar -->
        <div class="table-toolbar">
            <div class="table-search-box">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                <input type="text" placeholder="Buscar usuarios..." class="table-search-input">
            </div>
            <div class="flex items-center gap-2">
                <div class="table-action-btn">
                    <span>10</span>
                    <i data-lucide="chevrons-up-down" class="w-2.5 h-2.5 text-slate-400"></i>
                </div>
                <button class="table-action-btn">
                    <span>Filtrar</span>
                    <i data-lucide="filter" class="w-3 h-3 text-slate-400"></i>
                </button>
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
                        <th class="table-th">Nombre <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Edad <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th">Dirección <i data-lucide="chevrons-up-down" class="inline w-3 h-3 ml-1 opacity-50"></i></th>
                        <th class="table-th text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $mockUsers = [
                        ['Christina Bersh', 45, '4222 River Rd, Columbus'],
                        ['David Harrison', 27, '2952 S Peoria Ave, Tulsa'],
                        ['Ana Richard', 31, '255 Dock Ln, New Tazewell'],
                        ['Samia Dibujos animados', 45, '4970 Park Ave W, Ohio'],
                        ['David Harrison', 27, '4222 River Rd, Columbus'],
                        ['Brian Halligan', 31, '2952 S Peoria Ave, Tulsa'],
                        ['Andy Clerk', 45, '1818 H St NW, Washington'],
                        ['Bart Simpson', 27, '3 Grace Dr, Nuevo México'],
                        ['Camila Welters', 45, '4531 W Saile Dr, Dakota del Norte'],
                        ['Oliver Schevich', 27, '2126 N Eagle, Meridian, Illinois'],
                    ];
                    foreach ($mockUsers as $u): ?>
                        <tr class="table-row-hover">
                            <td class="table-td text-center">
                                <input type="checkbox" class="w-3.5 h-3.5 rounded-none border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="table-td table-td-bold"><?= $u[0] ?></td>
                            <td class="table-td table-td-indigo"><?= $u[1] ?></td>
                            <td class="table-td table-td-muted"><?= $u[2] ?></td>
                            <td class="table-td text-right">
                                <button class="btn-table-action border-slate-200/70">Borrar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="table-footer">
            <p class="table-info-text">Mostrando del 1 al 10 de 20 usuarios</p>

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

    <!-- Standardized Modal: Nuevo Usuario -->
    <div id="modal_usuario" class="modal-backdrop">
        <div class="modal-container modal-xl">
            <div class="modal-header">
                <h2 class="modal-title">Registrar Nuevo Usuario</h2>
                <button onclick="closeModal('modal_usuario')" class="modal-close">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="<?= base_url('usuarios/save') ?>" method="POST">
                <div class="modal-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Tipo de Documento</label>
                            <select name="tipo_doc" class="form-input">
                                <option value="DNI">DNI</option>
                                <option value="CE">Carnet de Extranjería</option>
                                <option value="PASSPORT">Pasaporte</option>
                            </select>
                        </div>
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Nro. de Documento</label>
                            <input type="text" name="num_doc" placeholder="Ej: 77123456" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" placeholder="Nombres y Apellidos" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" placeholder="ejemplo@correo.com" class="form-input">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Rol del Sistema</label>
                            <select name="rol" class="form-input">
                                <option value="ADMIN">Administrador</option>
                                <option value="ESTANDAR">Usuario Estándar</option>
                                <option value="OPERADOR">Operador</option>
                            </select>
                        </div>
                        <div class="form-group col-span-2 md:col-span-1">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" placeholder="••••••••" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('modal_usuario')" class="btn-secondary transition-all">Cancelar</button>
                    <button type="submit" class="btn-primary transition-all">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>