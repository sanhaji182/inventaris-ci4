/**
 * Inventaris Modern App UI & Interactions
 */

(function () {
    'use strict';

    // 1. Theme Management (Dark / Light Mode)
    const THEME_KEY = 'inventaris_theme';
    const htmlEl = document.documentElement;

    function getInitialTheme() {
        const saved = localStorage.getItem(THEME_KEY);
        if (saved) return saved;
        // Default ke light mode sesuai preferensi clean & modern
        return 'light';
    }

    function applyTheme(theme) {
        if (theme === 'dark') {
            htmlEl.setAttribute('data-theme', 'dark');
            htmlEl.setAttribute('data-bs-theme', 'dark');
        } else {
            htmlEl.removeAttribute('data-theme');
            htmlEl.setAttribute('data-bs-theme', 'light');
        }
        localStorage.setItem(THEME_KEY, theme);
        updateThemeToggleIcons(theme);
        // Trigger custom event for charts to adapt
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    function updateThemeToggleIcons(theme) {
        document.querySelectorAll('.btn-theme-toggle i').forEach(icon => {
            if (theme === 'dark') {
                icon.className = 'bi bi-sun-fill text-warning';
            } else {
                icon.className = 'bi bi-moon-stars text-secondary';
            }
        });
    }

    // Apply on run
    const currentTheme = getInitialTheme();
    applyTheme(currentTheme);

    document.addEventListener('DOMContentLoaded', () => {
        updateThemeToggleIcons(getInitialTheme());

        // Attach theme toggles
        document.querySelectorAll('.btn-theme-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const now = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(now);
                showToast(`Mode ${now === 'dark' ? 'Gelap' : 'Terang'} diaktifkan`, 'info');
            });
        });

        // 2. Mobile Sidebar & Backdrop
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const btnSidebar = document.getElementById('btnSidebar');

        if (btnSidebar && sidebar) {
            btnSidebar.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                if (backdrop) backdrop.classList.toggle('show');
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                if (sidebar) sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            });
        }

        // 3. Client-Side Table Filter
        document.querySelectorAll('[data-table-search]').forEach(input => {
            const targetSelector = input.getAttribute('data-table-search');
            const targetTable = document.querySelector(targetSelector);
            if (!targetTable) return;

            input.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows = targetTable.querySelectorAll('tbody tr');
                let matchCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const match = text.includes(query);
                    row.style.display = match ? '' : 'none';
                    if (match) matchCount++;
                });

                // Update or create counter badge if exists
                const countBadge = document.querySelector(`[data-table-count="${targetSelector}"]`);
                if (countBadge) {
                    countBadge.textContent = `${matchCount} baris`;
                }
            });
        });

        // 4. Global Quick Search shortcut (Ctrl+K or /)
        const globalSearch = document.getElementById('globalQuickSearch');
        if (globalSearch) {
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    globalSearch.focus();
                }
            });

            globalSearch.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                // Filter the primary table on current page if present
                const firstTable = document.querySelector('.table tbody');
                if (!firstTable) return;
                firstTable.querySelectorAll('tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }

        // 5. Copy-to-Clipboard (IMEI chip, Kode, dll)
        document.body.addEventListener('click', (e) => {
            const copyEl = e.target.closest('[data-copy]');
            if (!copyEl) return;

            const text = copyEl.getAttribute('data-copy') || copyEl.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast(`Disalin: ${text}`, 'success');
                }).catch(() => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        });

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast(`Disalin: ${text}`, 'success');
        }
    });

    // 6. Global Toast Helper
    window.showToast = function (message, type = 'success') {
        let container = document.querySelector('.toast-container-custom');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container-custom';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `modern-toast toast-${type}`;

        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        const iconClass = iconMap[type] || 'bi-bell-fill';

        toast.innerHTML = `
            <i class="bi ${iconClass}"></i>
            <div class="flex-grow-1" style="font-size:0.875rem; font-weight:500;">${message}</div>
            <button type="button" class="btn-close btn-close-white ms-auto" style="filter: invert(var(--bs-theme === 'dark' ? 1 : 0)); font-size:0.7rem;"></button>
        `;

        toast.querySelector('.btn-close')?.addEventListener('click', () => {
            toast.remove();
        });

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(40px)';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    };

})();
