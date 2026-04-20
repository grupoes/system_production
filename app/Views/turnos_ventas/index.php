<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight text-left">Turnos de Ventas</h1>
            <p class="text-sm text-slate-500 mt-1 text-left">Asignación semanal de personal para atención de ventas.</p>
        </div>
        <button onclick="openModalAsignar()" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95 text-sm font-bold">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Asignar Usuario
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        <!-- Sidebar: Días -->
        <div class="lg:col-span-1 flex flex-col gap-3" id="dias-container">
            <!-- Cargando días... -->
            <div class="p-4 bg-white rounded-2xl border border-slate-100 animate-pulse">
                <div class="h-4 bg-slate-100 rounded w-1/2"></div>
            </div>
        </div>

        <!-- Main: Asignados -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
                    <div>
                        <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight" id="dia-seleccionado-titulo">Seleccione un día</h2>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Personal asignado para este turno</p>
                    </div>
                    <div id="loading-asignados" class="hidden">
                        <i data-lucide="loader-2" class="w-5 h-5 text-indigo-500 animate-spin"></i>
                    </div>
                </div>

                <div class="p-8">
                    <div id="asignados-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="col-span-full py-20 text-center flex flex-col items-center gap-4 text-slate-300">
                            <i data-lucide="calendar-days" class="w-12 h-12 opacity-20"></i>
                            <p class="text-xs font-bold uppercase tracking-widest">Selecciona un día para ver las asignaciones</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Asignar Usuario -->
<div id="modal_asignar" class="modal-backdrop">
    <div class="modal-container modal-md">
        <div class="modal-header">
            <h2 class="modal-title">Asignar a Turno</h2>
            <button onclick="closeModal('modal_asignar')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-asignar" onsubmit="saveAsignacion(event)">
            <input type="hidden" name="dia_id" id="modal_dia_id">
            <div class="modal-body space-y-6">
                <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-indigo-600 shadow-sm">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Día de Turno</span>
                        <p class="text-sm font-black text-indigo-900 uppercase" id="modal_dia_nombre">-</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Seleccionar Usuario</label>
                    <select name="usuario_id" id="usuario_id" class="form-input" required>
                        <option value="">Cargando usuarios...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal_asignar')" class="btn-secondary">Cancelar</button>
                <button type="submit" id="btn-save-asignar" class="btn-primary">Confirmar Asignación</button>
            </div>
        </form>
    </div>
</div>

<script>
    let diaSeleccionado = null;
    let diaNombreSeleccionado = '';

    function openModal(id) {
        if (id === 'modal_asignar' && !diaSeleccionado) {
            showToast('Por favor, selecciona un día primero', 'warning');
            return;
        }
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function loadDias() {
        fetch(`${window.location.origin}/turnos-ventas/dias`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderDias(res.data);
                }
            });
    }

    function renderDias(data) {
        const container = document.getElementById('dias-container');
        let html = '';
        data.forEach(item => {
            html += `
                <button onclick="seleccionarDia(${item.id}, '${item.dia}')" id="btn-dia-${item.id}" class="dia-btn group flex items-center justify-between p-5 bg-white rounded-3xl border border-slate-100 transition-all hover:border-indigo-200 hover:bg-slate-50/50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all shadow-sm border border-slate-100">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">${item.dia}</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0"></i>
                </button>
            `;
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function seleccionarDia(id, nombre) {
        diaSeleccionado = id;
        diaNombreSeleccionado = nombre;
        
        // UI Classes
        document.querySelectorAll('.dia-btn').forEach(btn => {
            btn.classList.remove('active-dia', 'border-indigo-600', 'bg-indigo-50/50');
            btn.classList.add('bg-white', 'border-slate-100');
        });
        const activeBtn = document.getElementById(`btn-dia-${id}`);
        activeBtn.classList.add('active-dia', 'border-indigo-600', 'bg-indigo-50/50');
        activeBtn.classList.remove('bg-white', 'border-slate-100');

        document.getElementById('dia-seleccionado-titulo').innerText = `Turno: ${nombre}`;
        document.getElementById('modal_dia_id').value = id;
        document.getElementById('modal_dia_nombre').innerText = nombre;

        loadAsignaciones(id);
    }

    function loadAsignaciones(diaId) {
        document.getElementById('loading-asignados').classList.remove('hidden');
        fetch(`${window.location.origin}/turnos-ventas/asignaciones/${diaId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderAsignaciones(res.data);
                }
            })
            .finally(() => {
                document.getElementById('loading-asignados').classList.add('hidden');
            });
    }

    function renderAsignaciones(data) {
        const container = document.getElementById('asignados-grid');
        if (data.length === 0) {
            container.innerHTML = `
                <div class="col-span-full py-20 text-center flex flex-col items-center gap-4 text-slate-300">
                    <i data-lucide="users" class="w-12 h-12 opacity-20"></i>
                    <p class="text-xs font-bold uppercase tracking-widest">No hay personal asignado para el ${diaNombreSeleccionado}</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        let html = '';
        data.forEach(item => {
            html += `
                <div class="group/card relative bg-white p-6 rounded-[2rem] border border-slate-100 hover:border-indigo-100 transition-all hover:shadow-xl hover:shadow-indigo-500/5 overflow-hidden">
                    <!-- Delete Button -->
                    <button onclick="removeAsignacion(${item.id}, '${item.nombres} ${item.apellidos}')" 
                        class="absolute top-4 right-4 p-2 bg-rose-50 text-rose-500 rounded-xl opacity-0 group-hover/card:opacity-100 transition-all hover:bg-rose-500 hover:text-white z-10 shadow-sm">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>

                    <!-- Card Content -->
                    <div class="flex flex-col min-w-0">
                        <h3 class="text-base font-black text-slate-800 truncate tracking-tight leading-tight">${item.nombres}</h3>
                        <p class="text-sm font-bold text-slate-500 truncate mb-2">${item.apellidos}</p>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest group-hover:card:bg-indigo-600 group-hover:card:text-white transition-all">
                                ${item.rol}
                            </span>
                        </div>
                    </div>

                    <!-- Decoration -->
                    <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-slate-50/50 rounded-full blur-2xl group-hover/card:bg-indigo-100/50 transition-all"></div>
                </div>
            `;
        });
        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalAsignar() {
        if (!diaSeleccionado) {
            showToast('Primero selecciona un día en la columna izquierda', 'warning');
            return;
        }
        openModal('modal_asignar');
    }

    function loadUsuarios() {
        fetch(`${window.location.origin}/turnos-ventas/usuarios`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const select = document.getElementById('usuario_id');
                    let html = '<option value="">Seleccione un usuario...</option>';
                    res.data.forEach(item => {
                        html += `<option value="${item.id}">${item.nombres} ${item.apellidos} (${item.rol})</option>`;
                    });
                    select.innerHTML = html;
                }
            });
    }

    function saveAsignacion(e) {
        e.preventDefault();
        const form = document.getElementById('form-asignar');
        const formData = new FormData(form);
        const btn = document.getElementById('btn-save-asignar');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Asignando...';

        fetch(`${window.location.origin}/turnos-ventas/save`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                closeModal('modal_asignar');
                loadAsignaciones(diaSeleccionado);
            } else {
                showToast(res.message, 'error');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    function removeAsignacion(id, nombre) {
        showConfirm('Remover Asignación', `¿Deseas quitar a <b>${nombre}</b> de este turno?`, 'Sí, remover', () => {
            fetch(`${window.location.origin}/turnos-ventas/remove/${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        loadAsignaciones(diaSeleccionado);
                    } else {
                        showToast(res.message, 'error');
                    }
                });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadDias();
        loadUsuarios();
    });
</script>

<style>
    .dia-btn.active-dia {
        @apply shadow-lg shadow-indigo-100/50 ring-2 ring-indigo-500/20;
    }
    .form-input {
        @apply w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all;
    }
    .form-label {
        @apply text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 block;
    }
    .btn-primary {
        @apply px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 active:scale-95;
    }
    .btn-secondary {
        @apply px-6 py-2.5 bg-white text-slate-500 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all;
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
<?= $this->endSection() ?>
