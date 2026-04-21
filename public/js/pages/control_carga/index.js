document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const externalEventsEl = document.getElementById('external-events');
    const loadingEl = document.getElementById('loading-activities');

    const selectMainUsuario = document.getElementById('main-usuario-id');
    const selectModalUsuario = document.getElementById('asig-usuario-id');
    const modalAsignar = document.getElementById('modal-asignar');
    const formAsignar = document.getElementById('form-asignar');

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay,dayGridMonth'
        },
        locale: 'es',
        firstDay: 1,
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        allDaySlot: false,
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        editable: true,
        droppable: true,
        events: function(info, successCallback, failureCallback) {
            const usuarioId = selectMainUsuario.value;
            if (!usuarioId) {
                successCallback([]);
                return;
            }

            const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : window.location.origin;
            fetch(`${baseUrl}/control-carga/user-schedule?usuario_id=${usuarioId}&start=${info.startStr}&end=${info.endStr}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching schedule:', error);
                    failureCallback(error);
                });
        },
        eventReceive: function(info) {
            const activityId = info.event.id;
            const activityTitle = info.event.title;
            const startTime = info.event.start;
            
            // Obtener detalles extendidos
            const details = info.event.extendedProps;
            
            // Calcular fin basado en minutos si no existe
            let endTime = info.event.end;
            if (!endTime && details.minutos) {
                endTime = new Date(startTime.getTime() + parseInt(details.minutos) * 60000);
            } else if (!endTime) {
                endTime = new Date(startTime.getTime() + 60 * 60 * 1000);
            }

            info.revert();

            // Si hay un usuario seleccionado arriba, pre-seleccionarlo en el modal
            if (selectMainUsuario.value) {
                selectModalUsuario.value = selectMainUsuario.value;
            }

            openAsignarModal(activityId, activityTitle, startTime, endTime, details);
        }
    });

    calendar.render();

    // Listener para cambio de usuario
    selectMainUsuario.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    let currentStartDate = null;
    
    window.openAsignarModal = function(id, title, start, end, details = {}) {
        currentStartDate = start; // Store the start date
        document.getElementById('asig-actividad-id').value = id;
        document.getElementById('asig-actividad-nombre').value = title;
        
        // Cargar detalles informativos
        document.getElementById('asig-titulo').innerText = details.titulo || 'N/A';
        document.getElementById('asig-universidad').innerText = details.universidad || 'N/A';
        document.getElementById('asig-carrera').innerText = details.carrera || 'N/A';
        document.getElementById('asig-nivel').innerText = details.nivel_academico || 'N/A';
        document.getElementById('asig-origen').innerText = details.contacto_origen || 'N/A';
        
        const prioridadEl = document.getElementById('asig-prioridad');
        prioridadEl.innerText = details.prioridad || 'NORMAL';
        prioridadEl.className = `px-4 py-2.5 border rounded-xl text-sm font-bold shadow-sm ${getPriorityClass(details.prioridad)}`;
        
        document.getElementById('asig-contactos').innerText = details.prospecto_cliente || 'N/A';
        document.getElementById('asig-observaciones').innerText = details.observaciones || 'Sin observaciones';
        
        const linkDrive = document.getElementById('asig-link-drive');
        if (details.link_drive) {
            linkDrive.href = details.link_drive;
            linkDrive.style.display = 'flex';
        } else {
            linkDrive.style.display = 'none';
        }

        // Formatear horas HH:mm
        const hInicio = start.getHours().toString().padStart(2, '0') + ':' + start.getMinutes().toString().padStart(2, '0');
        const hFin = end.getHours().toString().padStart(2, '0') + ':' + end.getMinutes().toString().padStart(2, '0');
        
        document.getElementById('asig-hora-inicio').value = hInicio;
        document.getElementById('asig-hora-fin').value = hFin;

        modalAsignar.classList.remove('hidden');
        lucide.createIcons();
    };

    window.closeAsignarModal = function() {
        modalAsignar.classList.add('hidden');
        formAsignar.reset();
    };

    async function loadUsers() {
        try {
            const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : window.location.origin;
            const response = await fetch(`${baseUrl}/control-carga/users`);
            const result = await response.json();
            if (result.status === 'success') {
                let responsablesHtml = '';
                let otrosHtml = '';
                
                result.data.forEach(u => {
                    const isResp = parseInt(u.es_responsable) === 1;
                    const option = `<option value="${u.id}">${isResp ? '⭐ ' : ''}${u.nombre}${isResp ? ' (Responsable)' : ''}</option>`;
                    if (isResp) {
                        responsablesHtml += option;
                    } else {
                        otrosHtml += option;
                    }
                });

                let finalHtml = '<option value="">Seleccionar auxiliar...</option>';
                if (responsablesHtml) {
                    finalHtml += `<optgroup label="⭐ Responsables de Hoy">${responsablesHtml}</optgroup>`;
                }
                if (otrosHtml) {
                    finalHtml += `<optgroup label="Otros Auxiliares">${otrosHtml}</optgroup>`;
                }

                selectMainUsuario.innerHTML = finalHtml;
                selectModalUsuario.innerHTML = finalHtml;

                // Seleccionar usuario por defecto si existe
                if (typeof DEFAULT_USER_ID !== 'undefined' && DEFAULT_USER_ID) {
                    selectMainUsuario.value = DEFAULT_USER_ID;
                    selectModalUsuario.value = DEFAULT_USER_ID;
                    calendar.refetchEvents();
                }
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    formAsignar.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline-block"></i> Guardando...';
        submitBtn.disabled = true;

        try {
            const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : window.location.origin;
            
            const formData = new FormData();
            formData.append('actividad_id', document.getElementById('asig-actividad-id').value);
            formData.append('usuario_id', document.getElementById('asig-usuario-id').value);
            
            if (currentStartDate) {
                const fecha = currentStartDate.getFullYear() + '-' + 
                            (currentStartDate.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                            currentStartDate.getDate().toString().padStart(2, '0');
                formData.append('fecha', fecha);
            }
            
            formData.append('hora', document.getElementById('asig-hora-inicio').value);

            const response = await fetch(`${baseUrl}/control-carga/save`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                alert(result.message);
                closeAsignarModal();
                loadPendingActivities();
                calendar.refetchEvents();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('Error al guardar la asignación.');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    // Fetch Pending Activities
    async function loadPendingActivities() {
        try {
            const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : window.location.origin;
            const response = await fetch(`${baseUrl}/control-carga/pending-activities`);
            const result = await response.json();

            if (result.status === 'success') {
                renderExternalEvents(result.data);
            }
        } catch (error) {
            console.error('Error loading activities:', error);
            externalEventsEl.innerHTML = `
                <div class="p-4 text-center">
                    <p class="text-xs text-rose-500 font-medium">Error al cargar actividades</p>
                </div>
            `;
        }
    }

    function renderExternalEvents(activities) {
        if (loadingEl) loadingEl.remove();

        if (activities.length === 0) {
            externalEventsEl.innerHTML = `
                <div class="p-8 text-center">
                    <p class="text-xs text-slate-400 font-medium">No hay actividades pendientes</p>
                </div>
            `;
            return;
        }

        let html = '';
        activities.forEach(item => {
            const priorityClass = getPriorityClass(item.prioridad);
            html += `
                <div class="external-event p-3 mb-2 rounded-xl border border-slate-100 bg-white shadow-sm hover:border-indigo-200 transition-all group" 
                     data-id="${item.id}" 
                     data-title="${item.tarea}"
                     data-minutos="${item.minutos || ''}"
                     data-titulo="${item.titulo || ''}"
                     data-universidad="${item.universidad || ''}"
                     data-carrera="${item.carrera || ''}"
                     data-nivel_academico="${item.nivel_academico || ''}"
                     data-contacto_origen="${item.contacto_origen || ''}"
                     data-prospecto_cliente="${item.prospecto_cliente || ''}"
                     data-link_drive="${item.link_drive || ''}"
                     data-prioridad="${item.prioridad || 'NORMAL'}"
                     data-observaciones="${item.observaciones || ''}">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider ${priorityClass}">
                            ${item.prioridad}
                        </span>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="grip-vertical" class="w-3.5 h-3.5 text-slate-300"></i>
                        </div>
                    </div>
                    <h4 class="text-xs font-bold text-slate-700 leading-tight mb-1 truncate" title="${item.tarea}">
                        ${item.tarea}
                    </h4>
                    <p class="text-[10px] text-slate-500 font-medium truncate mb-0.5">
                        <span class="text-indigo-500 font-bold">Cli:</span> ${item.prospecto_cliente || 'N/A'}
                    </p>
                    <p class="text-[10px] text-slate-400 font-medium truncate">
                        <span class="text-emerald-500 font-bold">Ven:</span> ${item.vendedor || 'N/A'}
                    </p>
                </div>
            `;
        });

        externalEventsEl.innerHTML = html;
        lucide.createIcons();

        // Initialize draggable for external events
        new FullCalendar.Draggable(externalEventsEl, {
            itemSelector: '.external-event',
            eventData: function(eventEl) {
                return {
                    id: eventEl.dataset.id,
                    title: eventEl.dataset.title,
                    extendedProps: {
                        minutos: eventEl.dataset.minutos,
                        titulo: eventEl.dataset.titulo,
                        universidad: eventEl.dataset.universidad,
                        carrera: eventEl.dataset.carrera,
                        nivel_academico: eventEl.dataset.nivel_academico,
                        contacto_origen: eventEl.dataset.contacto_origen,
                        prospecto_cliente: eventEl.dataset.prospecto_cliente,
                        link_drive: eventEl.dataset.link_drive,
                        prioridad: eventEl.dataset.prioridad,
                        observaciones: eventEl.dataset.observaciones
                    }
                };
            }
        });
    }

    function getPriorityClass(priority) {
        switch (priority) {
            case 'ALTA': return 'bg-rose-50 text-rose-600 border border-rose-100';
            case 'MEDIA': return 'bg-amber-50 text-amber-600 border border-amber-100';
            default: return 'bg-blue-50 text-blue-600 border border-blue-100';
        }
    }

    loadPendingActivities();
    loadUsers();
});
