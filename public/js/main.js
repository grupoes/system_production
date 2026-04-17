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
