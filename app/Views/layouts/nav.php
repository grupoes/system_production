<nav class="flex-1 overflow-y-auto pr-1">
    <p class="category-header" style="margin-top: 0;">Principal</p>

    <a href="<?= base_url('dashboard') ?>" class="sidebar-item active">
        <div class="flex items-center gap-3">
            <i data-lucide="layout-grid"></i>
            <span class="font-semibold">Dashboard</span>
        </div>
    </a>

    <div class="sidebar-group">
        <div class="sidebar-item" onclick="toggleSubmenu(this)">
            <div class="flex items-center gap-3">
                <i data-lucide="users-2"></i>
                <span class="font-semibold">Potenciales Clientes</span>
            </div>
            <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 opacity-40"></i>
        </div>
        <div class="submenu-container">
            <a href="<?= base_url('usuarios') ?>" class="submenu-item">Registro de Potencial Cliente</a>
            <a href="<?= base_url('roles') ?>" class="submenu-item">Lista de Potenciales Clientes</a>
            <a href="<?= base_url('asistencias') ?>" class="submenu-item">Documentos y Evidencias</a>
            <a href="<?= base_url('asistencias') ?>" class="submenu-item">Anotaciones</a>
        </div>
    </div>

    <div class="sidebar-group">
        <div class="sidebar-item" onclick="toggleSubmenu(this)">
            <div class="flex items-center gap-3">
                <i data-lucide="check-square"></i>
                <span class="font-semibold">Tareas</span>
            </div>
            <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 opacity-40"></i>
        </div>
        <div class="submenu-container">
            <a href="<?= base_url('clientes') ?>" class="submenu-item">Lista de Tareas</a>
            <a href="<?= base_url('proyectos') ?>" class="submenu-item">Turnos Ventas</a>
            <a href="<?= base_url('facturacion') ?>" class="submenu-item">Control de Carga</a>
            <a href="<?= base_url('facturacion') ?>" class="submenu-item">Auxiliar Antiguo</a>
            <a href="<?= base_url('facturacion') ?>" class="submenu-item">Historial de Tareas</a>
        </div>
    </div>

    <div class="sidebar-group">
        <div class="sidebar-item" onclick="toggleSubmenu(this)">
            <div class="flex items-center gap-3">
                <i data-lucide="calendar"></i>
                <span class="font-semibold">Reuniones</span>
            </div>
            <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 opacity-40"></i>
        </div>
        <div class="submenu-container">
            <a href="<?= base_url('clientes') ?>" class="submenu-item">Registro de Reuniones</a>
            <a href="<?= base_url('proyectos') ?>" class="submenu-item">Disponoibilidad de Auxiliares</a>
            <a href="<?= base_url('facturacion') ?>" class="submenu-item">Notificaciones</a>
            <a href="<?= base_url('facturacion') ?>" class="submenu-item">Historial de Reuniones</a>
        </div>
    </div>

    <div class="sidebar-group">
        <div class="sidebar-item" onclick="toggleSubmenu(this)">
            <div class="flex items-center gap-3">
                <i data-lucide="settings"></i>
                <span class="font-semibold">Mantenimiento</span>
            </div>
            <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 opacity-40"></i>
        </div>
        <div class="submenu-container">
            <a href="<?= base_url('universidades') ?>" class="submenu-item">Universidad</a>
            <a href="<?= base_url('carreras') ?>" class="submenu-item">Carrera</a>
            <a href="<?= base_url('feriados') ?>" class="submenu-item">Feriados</a>
            <a href="<?= base_url('origen_contactos') ?>" class="submenu-item">Origen de Contacto</a>
            <a href="<?= base_url('nivel_academico') ?>" class="submenu-item">Nivel Académico</a>
        </div>
    </div>

    <div class="sidebar-group">
        <div class="sidebar-item" onclick="toggleSubmenu(this)">
            <div class="flex items-center gap-3">
                <i data-lucide="lock"></i>
                <span class="font-semibold">Seguridad</span>
            </div>
            <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 opacity-40"></i>
        </div>
        <div class="submenu-container">
            <a href="<?= base_url('usuarios') ?>" class="submenu-item">Usuarios</a>
            <a href="<?= base_url('permisos') ?>" class="submenu-item">Permisos</a>
            <a href="<?= base_url('modulos') ?>" class="submenu-item">Módulos</a>
            <a href="<?= base_url('acciones') ?>" class="submenu-item">Acciones</a>
            <a href="<?= base_url('configuracion-acciones-modulos') ?>" class="submenu-item">Configuración Acciones</a>
        </div>
    </div>

</nav>