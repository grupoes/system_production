<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-6 py-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button id="toggle-sidebar" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-white transition-colors">
                <i data-lucide="align-left" class="w-6 h-6"></i>
            </button>

            <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-400">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                <span>/</span>
                <span class="text-slate-600 font-bold"><?= $title ?? 'Dashboard' ?></span>
            </div>
        </div>

        <div class="flex items-center gap-1 md:gap-3">
            <div class="relative">
                <button id="notifications-toggle" class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-600 transition-all">
                    <i data-lucide="bell-ring" class="w-5 h-5"></i>
                    <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">3</span>
                </button>

                <!-- Notifications Dropdown -->
                <div id="notifications-dropdown" class="notifications-dropdown">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-800">Notificaciones</span>
                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">3 NUEVAS</span>
                    </div>
                    <div class="max-h-[350px] overflow-y-auto">
                        <a href="#" class="notification-item">
                            <div class="notification-icon bg-blue-50 text-blue-600">
                                <i data-lucide="user-plus" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Nuevo usuario registrado</p>
                                <p class="text-[11px] text-slate-500 mt-1">Alex se ha unido al equipo.</p>
                                <p class="text-[9px] text-slate-400 mt-2">Hace 5 minutos</p>
                            </div>
                        </a>
                        <a href="#" class="notification-item">
                            <div class="notification-icon bg-amber-50 text-amber-600">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Tarea por vencer</p>
                                <p class="text-[11px] text-slate-500 mt-1">El reporte mensual vence hoy.</p>
                                <p class="text-[9px] text-slate-400 mt-2">Hace 2 horas</p>
                            </div>
                        </a>
                        <a href="#" class="notification-item">
                            <div class="notification-icon bg-emerald-50 text-emerald-600">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Cierre de mes exitoso</p>
                                <p class="text-[11px] text-slate-500 mt-1">Todos los proyectos fueron facturados.</p>
                                <p class="text-[9px] text-slate-400 mt-2">Ayer</p>
                            </div>
                        </a>
                    </div>
                    <a href="#" class="px-4 py-3 block text-center text-xs font-bold text-indigo-600 hover:bg-slate-50 border-t border-slate-100 transition-colors">
                        Ver todas las notificaciones
                    </a>
                </div>
            </div>

            <button id="theme-toggle" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-600 transition-all">
                <i data-lucide="moon" id="theme-icon" class="w-5 h-5"></i>
            </button>

            <div class="h-6 w-[1px] bg-slate-200 mx-1"></div>

            <div class="relative">
                <button id="user-dropdown-toggle" class="flex items-center gap-3 p-1.5 px-3 rounded-xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">
                    <div class="hidden sm:flex flex-col items-end text-right">
                        <span class="text-xs font-bold text-slate-800 leading-none">Admin User</span>
                        <span class="text-[10px] font-medium text-slate-400 mt-1 uppercase tracking-tighter">Administrador</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs ring-2 ring-white">AD</div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="user-dropdown" class="user-dropdown-menu">
                    <a href="<?= base_url('perfil') ?>" class="dropdown-item">
                        <i data-lucide="user"></i>
                        Mi Perfil
                    </a>
                    <a href="<?= base_url('configuracion') ?>" class="dropdown-item">
                        <i data-lucide="settings"></i>
                        Configuración
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('logout') ?>" class="dropdown-item text-rose-500 hover:bg-rose-50 hover:text-rose-600">
                        <i data-lucide="log-out"></i>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>