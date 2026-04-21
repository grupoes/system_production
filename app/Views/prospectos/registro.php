<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Quill Editor Styles -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Tom Select Styles -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

<div class="max-w-6xl mx-auto flex flex-col gap-8 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-black text-slate-800 tracking-tight text-left"><?= $title ?></h1>
        <p class="text-sm text-slate-500 font-medium text-left">
            <?= isset($prospecto) ? 'Actualiza la información del prospecto seleccionado.' : 'Completa la información para dar seguimiento a un nuevo prospecto.' ?>
        </p>
    </div>

    <form id="form-prospecto" onsubmit="saveProspecto(event)" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <input type="hidden" name="id" value="<?= $prospecto['id'] ?? '' ?>">
        <!-- Columna 1: Contactos (Personas) -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Contactos</h2>
                    </div>
                    <button type="button" onclick="addContactField()" class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>

                <div id="contacts-container" class="space-y-6">
                    <!-- Contacto Initial -->
                    <div class="contact-entry p-5 bg-slate-50/50 rounded-3xl border border-slate-100 relative group">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="form-label">Nombres</label>
                                <input type="text" name="nombres[]" class="form-input" placeholder="Ej: Juan Pedro">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Apellidos</label>
                                <input type="text" name="apellidos[]" class="form-input" placeholder="Ej: Garcia Lopez">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Número Celular</label>
                                <input type="tel" name="celulares[]" class="form-input" placeholder="Ej: 999 888 777" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-50">
                    <div class="form-group">
                        <label class="form-label">Origen de Contacto</label>
                        <select name="origen_id" id="origen_id">
                            <option value="">Seleccione origen...</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-lg shadow-indigo-200 text-white flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="info" class="w-5 h-5 opacity-50"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest opacity-70">Sugerencia</span>
                </div>
                <p class="text-xs font-medium leading-relaxed">Puedes añadir más de un contacto si el prospecto tiene varios números o personas de contacto relacionadas.</p>
            </div>
        </div>

        <!-- Columna 2 y 3: Información del Proyecto/Tarea -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
                <!-- Academic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Información Académica</h2>
                        </div>
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">Título del Trabajo / Proyecto</label>
                        <input type="text" name="titulo_trabajo" class="form-input" placeholder="Ej: Implementación de sistema de gestión ERP en empresas comerciales">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Universidad / Institución</label>
                        <select name="universidad_id" id="universidad_id">
                            <option value="">Seleccione universidad...</option>
                        </select>
                    </div>
                    <div class="form-group relative">
                        <label class="form-label">Carrera / Programa</label>
                        <button type="button" onclick="openModalCarrera()" class="absolute top-0 right-0 text-indigo-600 hover:text-indigo-800 transition-all flex items-center gap-1 group py-1">
                            <span class="text-[9px] font-bold uppercase tracking-tight opacity-0 group-hover:opacity-100 transition-opacity">Nueva</span>
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        </button>
                        <select name="carrera_id" id="carrera_id">
                            <option value="">Seleccione carrera...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nivel Académico</label>
                        <select name="nivel_id" id="nivel_id">
                            <option value="">Seleccione nivel...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Link Drive (Opcional)</label>
                        <input type="url" name="link_drive" class="form-input" placeholder="https://drive.google.com/...">
                    </div>
                </div>

                <!-- Task Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-50">
                    <div class="form-group col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                            </div>
                            <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Detalles de la Tarea</h2>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tarea a realizar</label>
                        <select name="tarea_id" id="tarea_id" required>
                            <option value="">Seleccione tarea...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha tentativa de entrega</label>
                        <input type="date" name="fecha_entrega" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prioridad</label>
                        <select name="prioridad" id="prioridad">
                            <option value="BAJA">BAJA</option>
                            <option value="NORMAL" selected>NORMAL</option>
                            <option value="ALTA">ALTA</option>
                            <option value="URGENTE">URGENTE</option>
                        </select>
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">Observaciones y Detalles</label>
                        <div id="editor-container" class="bg-slate-50 border-none rounded-2xl h-[200px] overflow-hidden"></div>
                        <input type="hidden" name="observaciones" id="observaciones">
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-50">

                    <?php if (isset($prospecto) && $prospecto['estado_cliente'] !== 'cliente'): ?>
                        <button type="button" onclick="convertToClient(event)" class="btn-success flex items-center gap-2">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                            Convertir a Cliente
                        </button>
                    <?php endif; ?>

                    <button type="submit" id="btn-save-prospecto" class="btn-primary flex items-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <?= isset($prospecto) ? 'Actualizar Prospecto' : 'Registrar Potencial Cliente' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal: Nueva Carrera -->
<div id="modal-carrera" class="hidden fixed inset-0 z-[100] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalCarrera()"></div>

        <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden border border-slate-100 transform transition-all">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight uppercase">Nueva Carrera</h3>
                    </div>
                    <button onclick="closeModalCarrera()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="form-group text-left">
                        <label class="form-label">Universidad Seleccionada</label>
                        <input type="text" id="carrera-uni-nombre" readonly class="form-input bg-slate-50 text-slate-500 italic">
                    </div>
                    <div class="form-group text-left">
                        <label class="form-label">Nombre de la Carrera</label>
                        <input type="text" id="carrera-nombre" class="form-input" placeholder="Ej: Ingeniería de Sistemas">
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeModalCarrera()" class="flex-1 btn-secondary">Cancelar</button>
                    <button type="button" onclick="saveNewCarrera()" class="flex-1 btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quill Editor Scripts -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<!-- Tom Select Scripts -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    let quill;
    let tsUni, tsCarrera, tsTarea, tsNivel, tsOrigen, tsPrioridad;

    const initialData = <?= json_encode([
                            'prospecto' => $prospecto ?? null,
                            'actividad' => $actividad ?? null,
                            'contactos' => $contactos ?? null
                        ]) ?>;

    document.addEventListener('DOMContentLoaded', async () => {
        // Init Quill
        quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Escribe aquí los detalles del prospecto, requerimientos específicos, etc.',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['image', 'link']
                ]
            }
        });

        loadFormData();
    });

    function initTomSelects() {
        if (tsUni) tsUni.destroy();
        if (tsTarea) tsTarea.destroy();
        if (tsCarrera) tsCarrera.destroy();
        if (tsNivel) tsNivel.destroy();
        if (tsOrigen) tsOrigen.destroy();

        const tsConfig = {
            create: false,
            dropdownParent: 'body'
        };

        tsUni = new TomSelect('#universidad_id', {
            ...tsConfig,
            placeholder: 'Buscar universidad...',
            onChange: function(val) {
                loadCarreras(val);
            }
        });

        tsTarea = new TomSelect('#tarea_id', {
            ...tsConfig,
            placeholder: 'Buscar tarea...'
        });

        tsCarrera = new TomSelect('#carrera_id', {
            ...tsConfig,
            placeholder: 'Seleccione carrera...'
        });

        tsNivel = new TomSelect('#nivel_id', {
            ...tsConfig,
            placeholder: 'Seleccione nivel...'
        });

        tsOrigen = new TomSelect('#origen_id', {
            ...tsConfig,
            placeholder: 'Seleccione origen...'
        });

        tsPrioridad = new TomSelect('#prioridad', {
            ...tsConfig,
            placeholder: 'Seleccione prioridad...'
        });
    }

    function addContactField() {
        renderContactField();
    }

    function renderContactField(data = null) {
        const container = document.getElementById('contacts-container');
        const entry = document.createElement('div');
        entry.className = 'contact-entry p-5 bg-slate-50/50 rounded-3xl border border-slate-100 relative group animate-fade-in mb-4 last:mb-0';

        // No permitir borrar si es el único contacto
        const showDelete = container.children.length > 0;

        entry.innerHTML = `
            ${showDelete ? `
            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 w-8 h-8 bg-white border border-rose-100 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm z-10">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>` : ''}
            <div class="space-y-4">
                <div class="form-group">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres[]" class="form-input" placeholder="Ej: Juan Pedro" value="${data ? (data.nombres || '') : ''}">
                </div>
                <div class="form-group">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos[]" class="form-input" placeholder="Ej: Garcia Lopez" value="${data ? (data.apellidos || '') : ''}">
                </div>
                <div class="form-group">
                    <label class="form-label">Número Celular</label>
                    <input type="tel" name="celulares[]" class="form-input" placeholder="Ej: 999 888 777" required value="${data ? (data.celular || '') : ''}">
                </div>
            </div>
        `;
        container.appendChild(entry);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    async function loadFormData() {
        try {
            const res = await fetch(`${window.location.origin}/prospectos/data-form`);
            const data = await res.json();
            if (data.status === 'success') {
                const uniSelect = document.getElementById('universidad_id');
                const nivSelect = document.getElementById('nivel_id');
                const oriSelect = document.getElementById('origen_id');
                const tarSelect = document.getElementById('tarea_id');

                data.universidades.forEach(item => {
                    uniSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                });

                data.niveles.forEach(item => {
                    nivSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                });

                data.origenes.forEach(item => {
                    oriSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                });

                data.tareas.forEach(item => {
                    tarSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                });

                initTomSelects();

                // Si hay datos iniciales (EDICIÓN), poblarlos
                if (initialData.prospecto) {
                    await populateEditData();
                }
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function populateEditData() {
        const p = initialData.prospecto;
        const a = initialData.actividad;
        const c = initialData.contactos;

        // Inputs básicos
        document.getElementsByName('titulo_trabajo')[0].value = p.titulo_prospecto || '';
        document.getElementsByName('link_drive')[0].value = p.link_drive || '';
        document.getElementsByName('fecha_entrega')[0].value = p.fecha_entrega || '';
        document.getElementById('prioridad').value = p.prioridad || 'NORMAL';

        // Quill
        if (p.contenido) {
            quill.root.innerHTML = p.contenido;
        }

        // TomSelects
        tsUni.setValue(p.universidad_id);
        tsNivel.setValue(p.nivel_academico_id);
        tsOrigen.setValue(p.origen_id);
        tsTarea.setValue(a ? a.tarea_id : '');

        // Carreras es asíncrono
        if (p.universidad_id) {
            await loadCarreras(p.universidad_id);
            tsCarrera.setValue(p.carrera_id);
        }

        // Contactos
        if (c && c.length > 0) {
            const container = document.getElementById('contacts-container');
            container.innerHTML = ''; // Limpiar el inicial
            c.forEach(contact => {
                renderContactField(contact);
            });
        }
    }

    async function loadCarreras(uniId) {
        const carreraSelect = document.getElementById('carrera_id');
        carreraSelect.innerHTML = '<option value="">Cargando...</option>';

        if (!uniId) {
            carreraSelect.innerHTML = '<option value="">Seleccione carrera...</option>';
            return;
        }

        try {
            const res = await fetch(`${window.location.origin}/prospectos/carreras/${uniId}`);
            const data = await res.json();
            if (data.status === 'success') {
                tsCarrera.clearOptions();
                data.data.forEach(item => {
                    tsCarrera.addOption({
                        value: item.id,
                        text: item.nombre
                    });
                });
                tsCarrera.refreshOptions(false);
            }
        } catch (err) {
            console.error(err);
        }
    }

    function convertToClient(e) {
        e.preventDefault();

        // Validaciones para Cliente
        const titulo = document.getElementsByName('titulo_trabajo')[0].value.trim();
        const uni = document.getElementById('universidad_id').value;
        const carrera = document.getElementById('carrera_id').value;
        const nivel = document.getElementById('nivel_id').value;
        const link = document.getElementsByName('link_drive')[0].value.trim();
        const fecha = document.getElementsByName('fecha_entrega')[0].value;
        const nombres = document.getElementsByName('nombres[]');
        const apellidos = document.getElementsByName('apellidos[]');
        const celulares = document.getElementsByName('celulares[]');

        if (!titulo || !uni || !carrera || !nivel || !link || !fecha) {
            showToast('Para convertir a cliente, todos los campos académicos, el link de drive y la fecha de entrega son obligatorios.', 'error');
            return;
        }

        let contactoIncompleto = false;
        nombres.forEach((n, i) => {
            if (!n.value.trim() || !apellidos[i].value.trim() || !celulares[i].value.trim()) {
                contactoIncompleto = true;
            }
        });

        if (contactoIncompleto) {
            showToast('Nombres, apellidos y celular son obligatorios para todos los contactos al convertir a cliente.', 'error');
            return;
        }

        if (confirm('¿Está seguro de convertir este prospecto en CLIENTE? Esta acción actualizará el estado y guardará el historial.')) {
            saveProspecto(e, 'cliente');
        }
    }

    function saveProspecto(e, forcedStatus = null) {
        e.preventDefault();

        // Validaciones manuales para campos críticos
        const origen = document.getElementById('origen_id').value;
        const tarea = document.getElementById('tarea_id').value;
        const celulares = document.getElementsByName('celulares[]');

        if (!origen) {
            showToast('El Origen de Contacto es obligatorio', 'error');
            return;
        }
        if (!tarea) {
            showToast('La Tarea a realizar es obligatoria', 'error');
            return;
        }

        let celularVacio = false;
        celulares.forEach(input => {
            if (!input.value.trim()) celularVacio = true;
        });

        if (celularVacio) {
            showToast('El Número Celular es obligatorio para todos los contactos', 'error');
            return;
        }

        // Sincronizar Quill con input oculto
        document.getElementById('observaciones').value = quill.root.innerHTML;

        const form = document.getElementById('form-prospecto');
        const formData = new FormData(form);

        if (forcedStatus) {
            formData.append('nuevo_estado_cliente', forcedStatus);
        }

        const btn = document.getElementById('btn-save-prospecto');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Registrando...';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        fetch(`${window.location.origin}/prospectos/save`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(res.message, 'success');
                    resetForm();
                    // Scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    showToast(res.message, 'error');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
    }

    function resetForm() {
        const form = document.getElementById('form-prospecto');
        form.reset();
        quill.setContents([]);
        tsUni.clear();
        tsTarea.clear();
        tsCarrera.clear();
        tsCarrera.clearOptions();
        const container = document.getElementById('contacts-container');
        container.innerHTML = `
            <div class="contact-entry p-5 bg-slate-50/50 rounded-3xl border border-slate-100 relative group">
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres[]" class="form-input" placeholder="Ej: Juan Pedro" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos[]" class="form-input" placeholder="Ej: Garcia Lopez" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Número Celular</label>
                        <input type="tel" name="celulares[]" class="form-input" placeholder="Ej: 999 888 777" required>
                    </div>
                </div>
            </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openModalCarrera() {
        const uniId = document.getElementById('universidad_id').value;
        if (!uniId) {
            showToast('Primero debe seleccionar una universidad', 'error');
            return;
        }

        // Obtener texto de TomSelect
        const uniText = tsUni.options[uniId].text;
        document.getElementById('carrera-uni-nombre').value = uniText;
        document.getElementById('modal-carrera').classList.remove('hidden');
    }

    function closeModalCarrera() {
        document.getElementById('modal-carrera').classList.add('hidden');
        document.getElementById('carrera-nombre').value = '';
    }

    async function saveNewCarrera() {
        const nombre = document.getElementById('carrera-nombre').value.trim();
        const uniId = document.getElementById('universidad_id').value;

        if (!nombre) {
            showToast('Ingrese el nombre de la carrera', 'error');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('universidad_id', uniId);

            const res = await fetch(`${window.location.origin}/prospectos/save-carrera`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.status === 'success') {
                showToast(data.message, 'success');

                // Añadir al TomSelect
                tsCarrera.addOption({
                    value: data.id,
                    text: data.nombre
                });
                tsCarrera.setValue(data.id);
                tsCarrera.refreshOptions(false);

                closeModalCarrera();
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Error al conectar con el servidor', 'error');
        }
    }
</script>

<style>
    .form-input {
        @apply w-full px-5 py-3 bg-slate-50 border-none rounded-[1.25rem] text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium;
    }

    .form-label {
        @apply text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 block ml-1;
    }

    .btn-primary {
        @apply px-8 py-3.5 bg-indigo-600 text-white rounded-[1.25rem] text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 active:scale-95;
    }

    .btn-secondary {
        @apply px-8 py-3.5 bg-slate-100 text-slate-500 rounded-[1.25rem] text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all;
    }

    .btn-success {
        background-color: #059669 !important;
        /* emerald-600 */
        color: #ffffff !important;
        padding: 0.875rem 2rem !important;
        border-radius: 1.25rem !important;
        font-size: 0.75rem !important;
        font-weight: 900 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3) !important;
        border: none !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    .btn-success:hover {
        background-color: #047857 !important;
        /* emerald-700 */
        transform: translateY(-1px) !important;
        box-shadow: 0 20px 25px -5px rgba(5, 150, 105, 0.4) !important;
    }

    .btn-success:active {
        transform: scale(0.95) !important;
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ql-toolbar.ql-snow {
        border: none !important;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9 !important;
        border-top-left-radius: 1.25rem;
        border-top-right-radius: 1.25rem;
        padding: 12px !important;
    }

    .ql-container.ql-snow {
        border: none !important;
        background: #f8fafc;
        border-bottom-left-radius: 1.25rem;
        border-bottom-right-radius: 1.25rem;
        font-family: inherit !important;
    }

    /* Tom Select Premium Overrides */
    .ts-wrapper {
        @apply w-full !important;
    }

    .ts-control {
        @apply border-none bg-slate-50 rounded-[1.25rem] px-5 py-3 text-sm font-medium shadow-none transition-all !important;
        background-color: #f8fafc !important;
        /* slate-50 */
        border-radius: 1.25rem !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 1.25rem center !important;
        background-size: 1rem !important;
        min-height: 46px !important;
        color: #1e293b !important;
        /* slate-800 */
    }

    .ts-control input {
        @apply font-medium !important;
    }

    .ts-wrapper.focus .ts-control {
        @apply ring-4 ring-indigo-500/10 bg-white shadow-sm !important;
    }

    .ts-dropdown {
        @apply border-none shadow-2xl shadow-indigo-500/10 rounded-2xl mt-2 overflow-hidden bg-white z-50 !important;
    }

    .ts-dropdown .active {
        @apply bg-indigo-50 text-indigo-600 !important;
    }

    .ts-dropdown .option {
        @apply px-6 py-3.5 text-sm transition-all !important;
    }

    .ts-wrapper.single .ts-control.has-items:after {
        display: none !important;
    }
</style>
<?= $this->endSection() ?>