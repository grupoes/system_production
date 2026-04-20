function toggleFilter(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('filter-dropdown');
    dropdown.classList.toggle('show');
}

/**
 * Handles status selection in filter
 */
function selectStatus(btn) {
    const buttons = btn.parentElement.querySelectorAll('.filter-pill');
    buttons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

/**
 * Closes the dropdown when clicking outside of it
 */
window.addEventListener('click', function (e) {
    const dropdown = document.getElementById('filter-dropdown');
    if (dropdown && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

/**
 * Standard Modal Functions (Existing)
 */
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

/**
 * Conditional Visibility for DNI Search Button
 */
document.addEventListener('DOMContentLoaded', function () {
    const tipoDocSelect = document.getElementById('tipo_doc');
    const btnSearchDni = document.getElementById('btn-search-dni');
    const numDocInput = document.getElementById('num_doc');

    if (tipoDocSelect && btnSearchDni && numDocInput) {
        tipoDocSelect.addEventListener('change', function () {
            if (this.value === 'DNI') {
                btnSearchDni.classList.remove('hidden');
                numDocInput.classList.add('pr-14');
            } else {
                btnSearchDni.classList.add('hidden');
                numDocInput.classList.remove('pr-14');
            }
        });
    }

    /**
     * Logic for Jornada Schedule visibility
     */
    const tipoJornadaSelect = document.getElementById('tipo_jornada');
    const containerHorario = document.getElementById('container-horario');

    if (tipoJornadaSelect) {
        tipoJornadaSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const nombre = selectedOption.getAttribute('data-nombre');

            // Si la jornada existe y no es FREELANCE, mostramos el horario
            if (nombre && nombre !== 'FREELANCE') {
                containerHorario.classList.remove('hidden');
            } else {
                containerHorario.classList.add('hidden');
            }
        });
    }

    /**
     * Handle Shift Toggles
     */
    document.querySelectorAll('.shift-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const container = this.closest('div').nextElementSibling; // The .shift-container
            const inputs = container.querySelectorAll('input[type="time"]');

            if (this.checked) {
                container.classList.remove('opacity-40', 'pointer-events-none');
                inputs.forEach(input => input.disabled = false);
            } else {
                container.classList.add('opacity-40', 'pointer-events-none');
                inputs.forEach(input => input.disabled = true);
            }
        });
    });

    /**
     * DNI/RUC Search Logic
     */
    if (btnSearchDni && numDocInput) {
        btnSearchDni.addEventListener('click', function () {
            const tipo = tipoDocSelect.value.toLowerCase();
            const numero = numDocInput.value.trim();

            if (numero === '') {
                showToast('Por favor, ingrese un número de documento.', 'warning');
                return;
            }

            // Visual feedback: Loading
            const originalContent = btnSearchDni.innerHTML;
            btnSearchDni.disabled = true;
            btnSearchDni.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
            lucide.createIcons();

            // Referencias a los campos
            const elNombre = document.getElementById('nombre');
            const elApellidos = document.getElementById('apellidos');
            const elFecha = document.querySelector('input[name="fecha_nacimiento"]');

            // Mostrar estado de búsqueda en los inputs con color verdecito
            if (elNombre) {
                elNombre.value = 'Estamos buscando...';
                elNombre.classList.add('text-emerald-600', 'font-bold');
            }
            if (elApellidos) {
                elApellidos.value = 'Estamos buscando...';
                elApellidos.classList.add('text-emerald-600', 'font-bold');
            }
            if (elFecha) {
                elFecha.type = 'text'; // Cambiamos a texto temporalmente
                elFecha.value = 'Estamos buscando...';
                elFecha.classList.add('text-emerald-600', 'font-bold');
            }

            // Make the request to our local proxy
            fetch(`${window.location.origin}/consultas/documento/${tipo}/${numero}`)
                .then(response => response.json())
                .then(res => {
                    if (res.respuesta === 'ok' && res.encontrado) {
                        const data = res.data;

                        if (tipo === 'dni') {
                            if (elNombre) elNombre.value = data.nombres || '';
                            const apellidos = `${data.ap_paterno || ''} ${data.ap_materno || ''}`.trim();
                            if (elApellidos) elApellidos.value = apellidos;

                            // Restaurar campo de fecha
                            if (elFecha) {
                                elFecha.type = 'date';
                                if (data.fecha_nacimiento) {
                                    const parts = data.fecha_nacimiento.split('/');
                                    if (parts.length === 3) {
                                        elFecha.value = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                    } else {
                                        elFecha.value = '';
                                    }
                                } else {
                                    elFecha.value = '';
                                }
                            }
                        } else if (tipo === 'ruc') {
                            // Si es RUC
                            if (elNombre) elNombre.value = data.razon_social || data.nombre_o_razon_social || data.nombre || '';
                            if (elApellidos) elApellidos.value = '';
                            if (elFecha) {
                                elFecha.type = 'date';
                                elFecha.value = '';
                            }
                        }
                    } else {
                        // Limpiar si no se encuentra
                        if (elNombre) elNombre.value = '';
                        if (elApellidos) elApellidos.value = '';
                        if (elFecha) {
                            elFecha.type = 'date';
                            elFecha.value = '';
                        }
                        showToast(res.mensaje || 'No se encontraron resultados para el documento ingresado.', 'info');
                    }
                })
                .catch(err => {
                    console.error('Error Api:', err);
                    // Limpiar en caso de error
                    if (elNombre) elNombre.value = '';
                    if (elApellidos) elApellidos.value = '';
                    if (elFecha) {
                        elFecha.type = 'date';
                        elFecha.value = '';
                    }
                    showToast('Ocurrió un error al conectar con el servicio de búsqueda.', 'error');
                })
                .finally(() => {
                    btnSearchDni.disabled = false;
                    btnSearchDni.innerHTML = originalContent;
                    lucide.createIcons();

                    // Remover clases de color verde temporal
                    if (elNombre) elNombre.classList.remove('text-emerald-600', 'font-bold');
                    if (elApellidos) elApellidos.classList.remove('text-emerald-600', 'font-bold');
                    if (elFecha) elFecha.classList.remove('text-emerald-600', 'font-bold');
                });
        });
    }

    /**
     * AJAX Users List Logic
     */
    let currentPage = 1;
    const searchInput = document.getElementById('searchInput');
    const limitSelect = document.getElementById('limitSelect');
    const tbody = document.getElementById('usuarios-tbody');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');

    window.loadUsers = function (page = 1) {
        if (!tbody) return;

        currentPage = page;
        const limit = limitSelect ? limitSelect.value : 10;
        const search = searchInput ? searchInput.value.trim() : '';

        // Show loading
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="p-8 text-center text-slate-500">
                    <div class="flex flex-col items-center justify-center">
                        <i data-lucide="loader-2" class="w-8 h-8 text-indigo-500 animate-spin mb-3"></i>
                        <p class="text-sm font-bold text-slate-700">Cargando usuarios...</p>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();

        const url = `${window.location.origin}/usuarios/list?page=${page}&limit=${limit}&search=${encodeURIComponent(search)}`;

        fetch(url)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    renderTable(response.data);
                    renderPagination(response.total, response.page, response.limit, response.lastPage);
                }
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="p-8 text-center text-red-500">
                            Error al cargar los datos.
                        </td>
                    </tr>
                `;
            });
    };

    function renderTable(users) {
        if (!users || users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="users" class="w-12 h-12 text-slate-300 mb-3"></i>
                            <p class="text-sm font-bold text-slate-700">No se encontraron usuarios</p>
                        </div>
                    </td>
                </tr>
            `;
            lucide.createIcons();
            return;
        }

        let html = '';
        const limit = limitSelect ? parseInt(limitSelect.value) : 10;
        let startIndex = (currentPage - 1) * limit + 1;

        users.forEach((u, index) => {
            const rowNum = startIndex + index;
            // Escape nulls
            const doc = u.numero_documento || '';
            const nombreCompleto = `${u.nombres || ''} ${u.apellidos || ''}`.trim();
            const rol = u.rol_nombre || 'Sin Rol';
            const correo = u.correo || '';

            html += `
                <tr class="table-row-hover border-b border-slate-50">
                    <td class="table-td text-center">
                        <span class="text-xs font-bold text-slate-400">${rowNum}</span>
                    </td>
                    <td class="table-td text-slate-500 font-mono text-xs">${doc}</td>
                    <td class="table-td table-td-bold">${nombreCompleto}</td>
                    <td class="table-td table-td-indigo">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            ${rol}
                        </span>
                    </td>
                    <td class="table-td text-slate-500 text-sm">${correo}</td>
                    <td class="table-td text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="window.editUser(${u.id})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors border border-slate-200/60 cursor-pointer" title="Editar">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button onclick="window.deleteUser(${u.id}, '${nombreCompleto.replace(/'/g, "\\'")}')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors border border-slate-200/60 cursor-pointer" title="Eliminar">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        lucide.createIcons();
    }

    function renderPagination(total, page, limit, lastPage) {
        if (!paginationInfo || !paginationControls) return;

        page = parseInt(page, 10) || 1;
        limit = parseInt(limit, 10) || 10;
        total = parseInt(total, 10) || 0;
        lastPage = parseInt(lastPage, 10) || 1;

        const start = (page - 1) * limit + 1;
        const end = Math.min(page * limit, total);

        if (total === 0) {
            paginationInfo.innerText = 'Mostrando 0 resultados';
            paginationControls.innerHTML = '';
            return;
        }

        paginationInfo.innerText = `Mostrando del ${start} al ${end} de ${total} usuarios`;

        let controlsHtml = '';

        // Botón Anterior
        controlsHtml += `
            <button class="pagination-nav-btn ${page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${page === 1 ? 'disabled' : `onclick="window.loadUsers(${page - 1})"`}>
                <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
            </button>
        `;

        // Páginas
        for (let i = 1; i <= lastPage; i++) {
            if (i === page) {
                controlsHtml += `<div class="pagination-dot pagination-dot-active">${i}</div>`;
            } else {
                controlsHtml += `<button class="pagination-dot pagination-dot-inactive" onclick="window.loadUsers(${i})">${i}</button>`;
            }
        }

        // Botón Siguiente
        controlsHtml += `
            <button class="pagination-nav-btn rotate-180 ${page === lastPage ? 'opacity-50 cursor-not-allowed' : ''}" ${page === lastPage ? 'disabled' : `onclick="window.loadUsers(${page + 1})"`}>
                <i data-lucide="chevrons-left" class="w-3.5 h-3.5"></i>
            </button>
        `;

        paginationControls.innerHTML = controlsHtml;
        lucide.createIcons();
    }

    // Event Listeners for Search and Limit
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                window.loadUsers(1);
            }, 500);
        });
    }

    if (limitSelect) {
        limitSelect.addEventListener('change', () => {
            window.loadUsers(1);
        });
    }

    // Initial Load
    if (tbody) {
        window.loadUsers(1);
    }

    /**
     * AJAX Save User Logic
     */
    const formUsuario = document.getElementById('form-usuario');
    if (formUsuario) {
        formUsuario.addEventListener('submit', function (e) {
            e.preventDefault(); // Evita recargar la página

            const btnSave = document.getElementById('btn-save-user');
            if (!btnSave) return;

            // Bloquear botón para evitar clics dobles
            const originalText = btnSave.innerHTML;
            btnSave.disabled = true;
            btnSave.classList.add('opacity-70', 'cursor-not-allowed');
            btnSave.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Guardando...';
            lucide.createIcons();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        // Cerrar modal, mostrar mensaje y recargar lista
                        showToast(res.message, 'success');
                        window.closeModalUser();
                        window.loadUsers(1);
                    } else {
                        showToast(res.message || 'Ocurrió un error al intentar guardar.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Hubo un error de red o en el servidor.', 'error');
                })
                .finally(() => {
                    // Restaurar botón
                    btnSave.disabled = false;
                    btnSave.classList.remove('opacity-70', 'cursor-not-allowed');
                    btnSave.innerHTML = originalText;
                    lucide.createIcons();
                });
        });
    }

    /**
     * Modal Helper Functions for Edit/New
     */
    window.openModalUser = function () {
        document.getElementById('modal-title-user').innerText = 'Registrar Nuevo Usuario';
        document.getElementById('form-usuario').reset();
        document.getElementById('id_usuario').value = '';
        document.getElementById('id_persona').value = '';

        // Hide schedule
        const containerHorario = document.getElementById('container-horario');
        if (containerHorario) containerHorario.classList.add('hidden');

        openModal('modal_usuario');
    };

    window.closeModalUser = function () {
        closeModal('modal_usuario');
        document.getElementById('form-usuario').reset();
    };

    /**
     * Fetch User Data for Editing
     */
    window.editUser = function (id) {
        fetch(`${window.location.origin}/usuarios/get/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const u = res.usuario;
                    const p = res.persona;
                    const h = res.horarios;

                    document.getElementById('modal-title-user').innerText = 'Editar Usuario';
                    document.getElementById('id_usuario').value = u.id;
                    document.getElementById('id_persona').value = p.id;

                    const docMap = { 1: 'DNI', 2: 'CE', 3: 'PASSPORT' };
                    document.getElementById('tipo_doc').value = docMap[p.tipoDocumento_id] || 'DNI';
                    document.getElementById('num_doc').value = p.numero_documento;
                    document.getElementById('nombre').value = p.nombres;
                    document.getElementById('apellidos').value = p.apellidos;
                    document.querySelector('input[name="fecha_nacimiento"]').value = p.fecha_nacimiento || '';
                    document.querySelector('input[name="telefono"]').value = p.celular || '';
                    document.querySelector('input[name="ciudad"]').value = p.direccion || '';

                    document.querySelector('input[name="email"]').value = u.usuario;
                    document.querySelector('select[name="rol"]').value = u.rol_id;

                    const selectJornada = document.getElementById('tipo_jornada');
                    selectJornada.value = u.tipo_jornada_id;
                    const event = new Event('change');
                    selectJornada.dispatchEvent(event);

                    // Clear previous checkboxes in schedule
                    document.querySelectorAll('.shift-toggle').forEach(el => {
                        el.checked = false;
                        const container = el.closest('div').nextElementSibling;
                        container.classList.add('opacity-40', 'pointer-events-none');
                        container.querySelectorAll('input').forEach(inp => inp.disabled = true);
                    });

                    // Populate Horarios
                    if (h && h.length > 0) {
                        const diasMap = { 1: 'lunes', 2: 'martes', 3: 'miércoles', 4: 'jueves', 5: 'viernes', 6: 'sábado' };
                        let shiftCount = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0, 6: 0 };

                        h.forEach(horario => {
                            const diaInt = horario.dia_semana;
                            const diaLower = diasMap[diaInt];
                            if (!diaLower) return;

                            shiftCount[diaInt]++;
                            const shiftNum = shiftCount[diaInt];

                            if (shiftNum <= 2) {
                                const checkbox = document.querySelector(`input[name="active${shiftNum}_${diaLower}"]`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                    const container = checkbox.closest('div').nextElementSibling;
                                    container.classList.remove('opacity-40', 'pointer-events-none');
                                    container.querySelectorAll('input').forEach(inp => inp.disabled = false);

                                    document.querySelector(`input[name="start${shiftNum}_${diaLower}"]`).value = horario.hora_inicio.substring(0, 5);
                                    document.querySelector(`input[name="end${shiftNum}_${diaLower}"]`).value = horario.hora_fin.substring(0, 5);
                                }
                            }
                        });
                    }

                    openModal('modal_usuario');
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(err => {
                console.error('Error fetching user:', err);
                showToast('Error al obtener datos del usuario.', 'error');
            });
    };

    /**
     * Delete User Logic
     */
    window.deleteUser = function(id, nombre) {
        showConfirm(
            '¿Eliminar usuario?',
            `El usuario <b class="text-slate-800">${nombre}</b> será desactivado y ya no tendrá acceso al sistema. ¿Deseas continuar?`,
            'Sí, eliminar',
            function() {
                fetch(`${window.location.origin}/usuarios/delete/${id}`, {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast(res.message, 'success');
                        window.loadUsers(currentPage); // Recargar misma página
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    showToast('Hubo un error al intentar eliminar el usuario.', 'error');
                });
            }
        );
    };
});