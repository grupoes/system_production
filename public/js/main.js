// Initialize Lucide Icons
lucide.createIcons();

// Sidebar Toggle for Mobile
const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');
const toggleBtn = document.getElementById('toggle-sidebar');

function toggleSidebar() {
    sidebar.classList.toggle('-translate-x-full');
    backdrop.classList.toggle('hidden');
    document.body.classList.toggle('overflow-hidden');
}

if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
if (backdrop) backdrop.addEventListener('click', toggleSidebar);

// Submenu Toggling
function toggleSubmenu(element) {
    const group = element.parentElement;
    const container = group.querySelector('.submenu-container');
    const isOpen = container.classList.contains('open');

    // Close all other open submenus
    document.querySelectorAll('.submenu-container.open').forEach(openContainer => {
        if (openContainer !== container) {
            openContainer.classList.remove('open');
            openContainer.parentElement.querySelector('.sidebar-item').classList.remove('expanded');
        }
    });

    // Toggle current
    container.classList.toggle('open');
    element.classList.toggle('expanded');
}

// Active state based on URL
const currentPath = window.location.pathname;
document.querySelectorAll('.sidebar-item, .submenu-item').forEach(link => {
    const href = link.getAttribute('href');
    if (href && (currentPath === href || currentPath.endsWith(href))) {
        link.classList.add('active');

        // If it's a submenu item, open the parent and highlight group
        const parentContainer = link.closest('.submenu-container');
        if (parentContainer) {
            parentContainer.classList.add('open');
            const group = parentContainer.parentElement;
            group.classList.add('has-active');
            group.querySelector('.sidebar-item').classList.add('expanded');
        }
    }
});

// User Dropdown Toggle
const userBtn = document.getElementById('user-dropdown-toggle');
const userDropdown = document.getElementById('user-dropdown');

if (userBtn && userDropdown) {
    userBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (typeof notifyDropdown !== 'undefined') notifyDropdown.classList.remove('show');
        userDropdown.classList.toggle('show');
        userBtn.classList.toggle('bg-slate-50');
    });

    document.addEventListener('click', (e) => {
        if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('show');
            userBtn.classList.remove('bg-slate-50');
        }
    });
}

// Notifications Dropdown Toggle
const notifyBtn = document.getElementById('notifications-toggle');
const notifyDropdown = document.getElementById('notifications-dropdown');

if (notifyBtn && notifyDropdown) {
    notifyBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (typeof userDropdown !== 'undefined') userDropdown.classList.remove('show');
        notifyDropdown.classList.toggle('show');
        notifyBtn.classList.toggle('bg-slate-100');
    });

    document.addEventListener('click', (e) => {
        if (!notifyBtn.contains(e.target) && !notifyDropdown.contains(e.target)) {
            notifyDropdown.classList.remove('show');
            notifyBtn.classList.remove('bg-slate-100');
        }
    });
}

// Dark Mode Toggle
const themeToggle = document.getElementById('theme-toggle');
const html = document.documentElement;

function updateThemeIcon(isDark) {
    const iconContainer = document.getElementById('theme-toggle');
    if (iconContainer) {
        iconContainer.innerHTML = `<i data-lucide="${isDark ? 'sun' : 'moon'}" class="w-5 h-5"></i>`;
        lucide.createIcons();
    }
}

// Check for saved theme
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
    html.classList.add('dark');
    updateThemeIcon(true);
}

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeIcon(isDark);
    });
}

// Mini Sidebar Toggle
const miniToggle = document.getElementById('mini-sidebar-toggle');
const miniIcon = document.getElementById('mini-toggle-icon');

if (miniToggle) {
    miniToggle.addEventListener('click', () => {
        const isMini = document.body.classList.toggle('sidebar-mini');
        if (miniIcon) {
            miniIcon.setAttribute('data-lucide', isMini ? 'chevron-right' : 'chevron-left');
            lucide.createIcons();
        }
        // Save state
        localStorage.setItem('sidebar-mini', isMini ? 'true' : 'false');
    });
}

// Restore sidebar mini state
if (localStorage.getItem('sidebar-mini') === 'true') {
    document.body.classList.add('sidebar-mini');
    if (miniIcon) miniIcon.setAttribute('data-lucide', 'chevron-right');
    lucide.createIcons();
}

// Modal Management
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal when clicking on backdrop
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-backdrop')) {
        closeModal(e.target.id);
    }
});

/**
 * Custom Toast Notifications (Tailwind CSS Glassmorphism)
 * @param {string} message - El mensaje a mostrar
 * @param {string} type - 'success', 'error', 'info', 'warning'
 * @param {string} position - 'top-center', 'top-right', 'bottom-right', 'bottom-center'
 */
function showToast(message, type = 'success', position = 'top-center') {
    const containerId = `toast-container-${position}`;
    let container = document.getElementById(containerId);
    
    if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        
        // Base classes
        let positionClasses = 'fixed z-[9999] flex flex-col gap-3 pointer-events-none ';
        
        switch (position) {
            case 'top-center':
                positionClasses += 'top-10 left-1/2 -translate-x-1/2 items-center';
                break;
            case 'top-right':
                positionClasses += 'top-5 right-5 items-end';
                break;
            case 'bottom-right':
                positionClasses += 'bottom-5 right-5 items-end';
                break;
            case 'bottom-center':
                positionClasses += 'bottom-10 left-1/2 -translate-x-1/2 items-center';
                break;
            default:
                positionClasses += 'top-10 left-1/2 -translate-x-1/2 items-center';
        }
        
        container.className = positionClasses;
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    
    let icon = '';
    let colorClass = '';
    
    if (type === 'success') {
        icon = '<i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>';
        colorClass = 'border-emerald-100 bg-emerald-50/90 text-emerald-800';
    } else if (type === 'error') {
        icon = '<i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>';
        colorClass = 'border-rose-100 bg-rose-50/90 text-rose-800';
    } else if (type === 'info') {
        icon = '<i data-lucide="info" class="w-5 h-5 text-blue-600"></i>';
        colorClass = 'border-blue-100 bg-blue-50/90 text-blue-800';
    } else {
        icon = '<i data-lucide="bell" class="w-5 h-5 text-indigo-600"></i>';
        colorClass = 'border-indigo-100 bg-indigo-50/90 text-indigo-800';
    }

    // Configurar animación inicial según la posición
    let animStartClass = '';
    let animEndClass = '';
    
    if (position.includes('top')) {
        animStartClass = '-translate-y-10';
        animEndClass = 'translate-y-0';
    } else {
        animStartClass = 'translate-y-10';
        animEndClass = 'translate-y-0';
    }

    if (position.includes('right')) {
        animStartClass = 'translate-x-[120%]';
        animEndClass = 'translate-x-0';
    }

    // Shape changes slightly if it's centered (pill) vs corner (rounded rect)
    const shapeClass = position.includes('center') ? 'rounded-full px-6' : 'rounded-2xl px-5';

    toast.className = `flex items-center gap-3 py-3.5 border backdrop-blur-md shadow-xl shadow-slate-200/50 transform transition-all duration-300 opacity-0 ${animStartClass} ${shapeClass} ${colorClass}`;
    toast.innerHTML = `
        ${icon}
        <span class="text-sm font-bold tracking-tight">${message}</span>
    `;

    // Si es bottom, insertamos al principio para que los nuevos salgan abajo (o arriba visualmente)
    if (position.includes('bottom')) {
        container.prepend(toast);
    } else {
        container.appendChild(toast);
    }

    if (typeof lucide !== 'undefined') lucide.createIcons({ root: toast });

    // Animación de entrada
    requestAnimationFrame(() => {
        toast.classList.remove(animStartClass, 'opacity-0');
        toast.classList.add(animEndClass, 'opacity-100');
    });

    // Animación de salida y remoción
    setTimeout(() => {
        toast.classList.remove(animEndClass, 'opacity-100');
        toast.classList.add(animStartClass, 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

window.showToast = showToast;

/**
 * Custom Confirm Modal (Tailwind CSS Glassmorphism)
 * @param {string} title - Título principal
 * @param {string} message - Mensaje secundario
 * @param {string} confirmText - Texto del botón de confirmación
 * @param {function} callback - Función a ejecutar al aceptar
 */
function showConfirm(title, message, confirmText, callback) {
    let existingModal = document.getElementById('custom-confirm-modal');
    if (existingModal) existingModal.remove();

    const modalHTML = `
        <div id="custom-confirm-modal" class="fixed inset-0 z-[9999] flex items-center justify-center pointer-events-none">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm pointer-events-auto transition-opacity duration-300 opacity-0" id="confirm-backdrop"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white/90 backdrop-blur-xl border border-white/50 shadow-2xl shadow-slate-300/50 rounded-3xl w-full max-w-[340px] p-6 transform transition-all duration-300 scale-95 opacity-0 pointer-events-auto" id="confirm-content">
                
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-500"></i>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 tracking-tight mb-2">${title}</h3>
                    <p class="text-[13px] text-slate-500 mb-8 leading-relaxed">${message}</p>
                    
                    <div class="flex gap-3 w-full">
                        <button id="btn-confirm-cancel" class="flex-1 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-800 text-[13px] font-bold rounded-2xl transition-colors">Cancelar</button>
                        <button id="btn-confirm-accept" class="flex-1 py-3 px-4 bg-rose-500 hover:bg-rose-600 text-white text-[13px] font-bold rounded-2xl shadow-lg shadow-rose-200 transition-colors">${confirmText}</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    if (typeof lucide !== 'undefined') lucide.createIcons();

    const modal = document.getElementById('custom-confirm-modal');
    const backdrop = document.getElementById('confirm-backdrop');
    const content = document.getElementById('confirm-content');
    const btnCancel = document.getElementById('btn-confirm-cancel');
    const btnAccept = document.getElementById('btn-confirm-accept');

    function closeConfirmModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.remove(), 300);
    }

    btnCancel.addEventListener('click', closeConfirmModal);
    
    btnAccept.addEventListener('click', () => {
        closeConfirmModal();
        if (typeof callback === 'function') callback();
    });

    // Animate In
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    });
}
window.showConfirm = showConfirm;
