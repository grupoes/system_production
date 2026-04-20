<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight text-left">Capacidad de Módulos</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium text-left">Configuración técnica de operaciones permitidas por módulo.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 text-left">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-1.5">
            <h3 class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Módulos del Sistema</h3>
            <div id="sidebar-modulos">
                <!-- Cargado por JS -->
                <div class="px-4 py-3 text-xs text-slate-400 flex items-center gap-2">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Cargando módulos...
                </div>
            </div>
        </div>

        <!-- Configuration Grid -->
        <div class="lg:col-span-3">
            <div id="grid-submodulos" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cargado por JS al seleccionar un módulo padre -->
                <div class="md:col-span-2 flex flex-col items-center justify-center py-16 text-slate-400 gap-3">
                    <i data-lucide="mouse-pointer-click" class="w-8 h-8"></i>
                    <p class="text-sm font-bold">Selecciona un módulo del panel izquierdo</p>
                    <p class="text-xs">para ver y configurar sus acciones disponibles.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gestionar Capacidad -->
<div id="modal_gestionar_acciones" class="modal-backdrop">
    <div class="modal-container modal-xl">
        <div class="modal-header border-b border-slate-50">
            <div>
                <h2 class="text-lg font-black text-slate-900 tracking-tight" id="modal-modulo-title">Capacidad Técnica</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5" id="modal-modulo-desc">Define las acciones operativas disponibles</p>
            </div>
            <button onclick="closeModal('modal_gestionar_acciones')" class="modal-close">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="modal-body space-y-6 py-8">
            <!-- Buscador -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" id="modal-search-accion" placeholder="Buscar operación..." class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none text-slate-600">
            </div>

            <!-- Grid de Acciones -->
            <div id="modal-acciones-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                <div class="col-span-3 text-center py-8 text-slate-400">
                    <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto"></i>
                </div>
            </div>
        </div>

        <div class="modal-footer border-t border-slate-50">
            <button type="button" onclick="closeModal('modal_gestionar_acciones')" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors mr-auto pl-4">Cancelar</button>
            <button type="button" id="btn-confirmar-acciones" class="px-8 py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <i data-lucide="save" class="w-3.5 h-3.5"></i>
                Confirmar Capacidad
            </button>
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

    // --------------------------------------------------
    // Estado global
    // --------------------------------------------------
    let moduloActivoId = null;
    let moduloActivoNombre = null;
    let todasLasAcciones = [];

    // --------------------------------------------------
    // Cargar sidebar de módulos
    // --------------------------------------------------
    function loadSidebar() {
        fetch(`${window.location.origin}/acciones/modulos-configuracion`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return;
                renderSidebar(res.data);
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(err => console.error('Error cargando módulos:', err));
    }

    function renderSidebar(modulos) {
        const sidebar = document.getElementById('sidebar-modulos');
        if (modulos.length === 0) {
            sidebar.innerHTML = '<p class="px-4 text-xs text-slate-400">No hay módulos registrados.</p>';
            return;
        }

        let html = '';
        modulos.forEach(m => {
            const icon = m.icono || 'box';
            const badge = m.total_acciones > 0
                ? `<span class="text-[9px] font-black px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-full">${m.total_acciones}</span>`
                : '';
            html += `
                <button onclick="selectModulo(${m.id}, '${m.modulo}')"
                    id="sidebar-btn-${m.id}"
                    class="sidebar-modulo-btn w-full flex items-center justify-between px-5 py-3.5 bg-white text-slate-500 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group mb-1.5">
                    <div class="flex items-center gap-3">
                        <i data-lucide="${icon}" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600"></i>
                        <span class="text-xs font-bold tracking-tight">${m.modulo}</span>
                    </div>
                    ${badge}
                </button>
            `;
        });

        sidebar.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Activar automáticamente el primer módulo
        if (modulos.length > 0) {
            selectModulo(modulos[0].id, modulos[0].modulo);
        }
    }

    // --------------------------------------------------
    // Seleccionar módulo → cargar grid de submódulos
    // --------------------------------------------------
    function selectModulo(id, nombre) {
        moduloActivoId = id;
        moduloActivoNombre = nombre;

        // Estilos sidebar
        document.querySelectorAll('.sidebar-modulo-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xl', 'shadow-indigo-100');
            btn.classList.add('bg-white', 'text-slate-500');
        });
        const activeBtn = document.getElementById(`sidebar-btn-${id}`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-slate-500');
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-xl', 'shadow-indigo-100');
        }

        // Cargar grid de módulos hijos del padre
        loadGridModulosHijos(id, nombre);
    }

    // --------------------------------------------------
    // Cargar módulos hijos (submódulos) del padre seleccionado
    // --------------------------------------------------
    function loadGridModulosHijos(padreId, padreNombre) {
        const grid = document.getElementById('grid-submodulos');
        grid.innerHTML = `<div class="md:col-span-2 text-center py-12"><i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-indigo-500"></i></div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`${window.location.origin}/acciones/hijos-de-modulo/${padreId}`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return;
                renderGridModulos(res.data);
                if (typeof lucide !== 'undefined') lucide.createIcons();
            })
            .catch(err => {
                console.error('Error:', err);
                grid.innerHTML = `<div class="col-span-2 text-rose-500 text-sm">Error al cargar módulos.</div>`;
            });
    }

    function renderGridModulos(modulos) {
        const grid = document.getElementById('grid-submodulos');
        if (modulos.length === 0) {
            grid.innerHTML = `<div class="md:col-span-2 text-center py-12 text-slate-400 text-sm">No hay submódulos configurados para este módulo.</div>`;
            return;
        }

        let html = '';
        modulos.forEach(m => {
            const urlLabel = (m.url && m.url !== '-') ? '/' + m.url : 'Módulo principal';
            html += `
                <div class="bg-white p-7 rounded-[2rem] border border-slate-100 shadow-sm hover:border-indigo-100 transition-all group/card">
                    <div class="flex flex-col gap-1 mb-6">
                        <h4 class="text-base font-black text-slate-900 tracking-tight group-hover/card:text-indigo-600 transition-colors">${m.modulo}</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">${urlLabel}</p>
                    </div>
                    <div id="acciones-preview-${m.id}" class="flex flex-wrap gap-2 mb-6 text-left min-h-[28px]">
                        <span class="text-[10px] text-slate-400">Cargando...</span>
                    </div>
                    <div class="pt-5 border-t border-slate-50">
                        <button
                            class="btn-configurar-acciones w-full py-3 bg-white hover:bg-indigo-600 hover:text-white text-slate-600 border border-slate-100 hover:border-indigo-600 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all shadow-sm flex items-center justify-center gap-2"
                            data-modulo-id="${m.id}"
                            data-modulo-nombre="${m.modulo}">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            Configurar Acciones
                        </button>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Cargamos el preview de acciones para cada módulo
        modulos.forEach(m => loadPreviewAcciones(m.id));

        // Event delegation para los botones de configurar
        grid.querySelectorAll('.btn-configurar-acciones').forEach(btn => {
            btn.addEventListener('click', function() {
                const mid = this.getAttribute('data-modulo-id');
                const mnombre = this.getAttribute('data-modulo-nombre');
                openConfigurarModal(mid, mnombre);
            });
        });
    }

    function loadPreviewAcciones(moduloId) {
        fetch(`${window.location.origin}/acciones/acciones-de-modulo/${moduloId}`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return;
                // PostgreSQL retorna 't'/'f', MySQL retorna 1/0
                const asignadas = res.data.filter(a => a.asignada === true || a.asignada === 't' || a.asignada == 1 || a.asignada === '1');
                const preview = document.getElementById(`acciones-preview-${moduloId}`);
                if (!preview) return;

                if (asignadas.length === 0) {
                    preview.innerHTML = `<span class="text-[10px] text-slate-400">Sin acciones configuradas</span>`;
                    return;
                }

                let html = '';
                asignadas.forEach(a => {
                    html += `<span class="px-2.5 py-1 bg-indigo-600 text-white rounded-lg text-[9px] font-bold uppercase tracking-widest">${a.nombre_accion}</span>`;
                });
                preview.innerHTML = html;
            })
            .catch(err => console.error('Error preview:', err));
    }

    // --------------------------------------------------
    // Abrir Modal de Configuración de un submódulo
    // --------------------------------------------------
    let moduloModalId = null;

    function openConfigurarModal(moduloId, moduloNombre) {
        moduloModalId = moduloId;
        document.getElementById('modal-modulo-title').innerText = moduloNombre;
        document.getElementById('modal-modulo-desc').innerText = 'Selecciona las acciones disponibles para este módulo';

        const grid = document.getElementById('modal-acciones-grid');
        grid.innerHTML = `<div class="col-span-3 text-center py-8"><i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-indigo-500"></i></div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`${window.location.origin}/acciones/acciones-de-modulo/${moduloId}`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return;
                todasLasAcciones = res.data;
                renderAccionesModal(todasLasAcciones);
                openModal('modal_gestionar_acciones');
            })
            .catch(err => {
                console.error('Error:', err);
                if (typeof showToast !== 'undefined') showToast('Error al cargar las acciones.', 'error');
            });
    }

    function renderAccionesModal(acciones) {
        const grid = document.getElementById('modal-acciones-grid');

        if (acciones.length === 0) {
            grid.innerHTML = `<div class="col-span-3 text-center py-8 text-slate-400 text-sm">No hay acciones registradas en el sistema.</div>`;
            return;
        }

        let html = '';
        acciones.forEach(a => {
            const isChecked = a.asignada == true || a.asignada === 't' || a.asignada === '1';
            html += `
                <label class="cursor-pointer group/chip text-left">
                    <input type="checkbox" class="sr-only peer accion-check" value="${a.id}" ${isChecked ? 'checked' : ''}>
                    <div class="p-4 rounded-2xl border border-slate-100 bg-white text-slate-500 transition-all peer-checked:text-white peer-checked:border-indigo-600 peer-checked:bg-indigo-600 hover:border-indigo-200">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-[11px] font-bold uppercase tracking-widest">${a.nombre_accion}</span>
                            <div class="w-4 h-4 rounded-full border border-current flex items-center justify-center peer-checked:border-white transition-all">
                                <i data-lucide="check" class="w-2.5 h-2.5 ${isChecked ? '' : 'opacity-0'}"></i>
                            </div>
                        </div>
                        <span class="text-[9px] opacity-60 font-medium normal-case block leading-tight">${a.descripcion || 'Acción operativa del módulo'}</span>
                    </div>
                </label>
            `;
        });

        grid.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Búsqueda dentro del modal
    document.getElementById('modal-search-accion').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const filtradas = todasLasAcciones.filter(a => a.nombre_accion.toLowerCase().includes(q));
        renderAccionesModal(filtradas);
    });

    // --------------------------------------------------
    // Guardar configuración del modal
    // --------------------------------------------------
    document.getElementById('btn-confirmar-acciones').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;

        const checks = document.querySelectorAll('.accion-check:checked');
        const accionIds = Array.from(checks).map(c => c.value);

        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin"></i> Guardando...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const formData = new FormData();
        formData.append('modulo_id', moduloModalId);
        accionIds.forEach(id => formData.append('accion_ids[]', id));

        fetch(`${window.location.origin}/acciones/save-acciones-modulo`, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                if (typeof showToast !== 'undefined') showToast(res.message, 'success');
                closeModal('modal_gestionar_acciones');
                // Refrescar el preview del card
                loadPreviewAcciones(moduloModalId);
                // Refrescar sidebar con conteos actualizados
                loadSidebar();
            } else {
                if (typeof showToast !== 'undefined') showToast(res.message, 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            if (typeof showToast !== 'undefined') showToast('Error de red al guardar.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });

    // --------------------------------------------------
    // Init
    // --------------------------------------------------
    document.addEventListener('DOMContentLoaded', () => {
        loadSidebar();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
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