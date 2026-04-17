<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Principal
<?= $this->endSection() ?>

<?= $this->section('actions') ?>
<button class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
    <i data-lucide="plus" class="w-4 h-4"></i>
    Nuevo Registro
</button>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Usuarios Activos</p>
        <h3 class="text-2xl font-bold text-slate-800 mt-1">1,248</h3>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+5.4%</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Ventas Totales</p>
        <h3 class="text-2xl font-bold text-slate-800 mt-1">$45,210</h3>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded-full">Pendiente</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Tareas Pendientes</p>
        <h3 class="text-2xl font-bold text-slate-800 mt-1">24</h3>
    </div>

    <!-- Stat Card 4 -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-full">-2.1%</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Tasa de Conversión</p>
        <h3 class="text-2xl font-bold text-slate-800 mt-1">3.15%</h3>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Chart/Table Placeholder -->
    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-slate-800">Actividad Reciente</h4>
            <button class="text-indigo-600 text-sm font-semibold hover:underline">Ver todo</button>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-wider">
                            <th class="pb-4 font-semibold">Usuario</th>
                            <th class="pb-4 font-semibold">Acción</th>
                            <th class="pb-4 font-semibold">Fecha</th>
                            <th class="pb-4 font-semibold">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b border-slate-50 group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Alex+Johnson" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-slate-700">Alex Johnson</span>
                            </td>
                            <td class="py-4 text-slate-500">Creó un nuevo cliente</td>
                            <td class="py-4 text-slate-400">Hace 5 min</td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold uppercase">Completado</span>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-50 group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Sarah+M" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-slate-700">Sarah Miller</span>
                            </td>
                            <td class="py-4 text-slate-500">Actualizó un reporte</td>
                            <td class="py-4 text-slate-400">Hace 12 min</td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase">Procesando</span>
                            </td>
                        </tr>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Mike+Ross" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-slate-700">Mike Ross</span>
                            </td>
                            <td class="py-4 text-slate-500">Error de inicio de sesión</td>
                            <td class="py-4 text-slate-400">Hace 45 min</td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-bold uppercase">Fallido</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar/Info cards -->
    <div class="space-y-6">
        <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="font-bold text-lg mb-2 text-indigo-100 italic font-display">Upgrade to Pro</h4>
                <p class="text-indigo-100 text-sm mb-6 opacity-80">Obtén acceso a reportes avanzados y soporte prioritario 24/7.</p>
                <button class="w-full bg-white text-indigo-600 py-3 rounded-xl font-bold text-sm hover:bg-indigo-50 transition-colors shadow-lg">
                    Saber más
                </button>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-sm">
            <h4 class="font-bold text-slate-800 mb-4">Próximos Eventos</h4>
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex flex-col items-center justify-center min-w-[48px]">
                        <span class="text-[10px] font-bold text-slate-400">ABR</span>
                        <span class="text-sm font-bold text-slate-700 leading-none">18</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Reunión de Equipo</p>
                        <p class="text-xs text-slate-400">09:00 AM - Oficina Central</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex flex-col items-center justify-center min-w-[48px]">
                        <span class="text-[10px] font-bold text-slate-400">ABR</span>
                        <span class="text-sm font-bold text-slate-700 leading-none">20</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Lanzamiento v2.0</p>
                        <p class="text-xs text-slate-400">Todo el día</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
