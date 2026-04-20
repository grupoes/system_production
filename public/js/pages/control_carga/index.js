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

            fetch(`${window.location.origin}/control-carga/user-schedule?usuario_id=${usuarioId}&start=${info.startStr}&end=${info.endStr}`)
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
            const endTime = info.event.end || new Date(startTime.getTime() + 60 * 60 * 1000);

            info.revert();

            // Si hay un usuario seleccionado arriba, pre-seleccionarlo en el modal
            if (selectMainUsuario.value) {
                selectModalUsuario.value = selectMainUsuario.value;
            }

            openAsignarModal(activityId, activityTitle, startTime, endTime);
        }
    });

    calendar.render();

    // Listener para cambio de usuario
    selectMainUsuario.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Modal Functions
    window.openAsignarModal = function(id, title, start, end) {
        document.getElementById('asig-actividad-id').value = id;
        document.getElementById('asig-actividad-nombre').value = title;
        
        // Formatear horas HH:mm
        const hInicio = start.getHours().toString().padStart(2, '0') + ':' + start.getMinutes().toString().padStart(2, '0');
        const hFin = end.getHours().toString().padStart(2, '0') + ':' + end.getMinutes().toString().padStart(2, '0');
        
        document.getElementById('asig-hora-inicio').value = hInicio;
        document.getElementById('asig-hora-fin').value = hFin;

        modalAsignar.classList.remove('hidden');
    };

    window.closeAsignarModal = function() {
        modalAsignar.classList.add('hidden');
        formAsignar.reset();
    };

    async function loadUsers() {
        try {
            const response = await fetch(`${window.location.origin}/control-carga/users`);
            const result = await response.json();
            if (result.status === 'success') {
                let options = '<option value="">Seleccionar auxiliar...</option>';
                result.data.forEach(u => {
                    options += `<option value="${u.id}">${u.nombre}</option>`;
                });
                selectMainUsuario.innerHTML = options;
                selectModalUsuario.innerHTML = options;

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

    formAsignar.addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí se enviaría al backend para guardar la asignación
        alert('Asignación confirmada (Falta implementar el guardado en BD)');
        closeAsignarModal();
        // Recargar actividades para quitar la asignada
        loadPendingActivities();
    });

    // Fetch Pending Activities
    async function loadPendingActivities() {
        try {
            const response = await fetch(`${window.location.origin}/control-carga/pending-activities`);
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
                     data-duration="01:00">
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
                    <p class="text-[10px] text-slate-500 font-medium truncate">
                        <span class="text-indigo-500 font-bold">Cli:</span> ${item.prospecto_cliente || 'N/A'}
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
                    duration: eventEl.dataset.duration
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
