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
            if (this.value === 'FULL_TIME' || this.value === 'PART_TIME') {
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
        toggle.addEventListener('change', function() {
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
});