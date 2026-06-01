/**
 * Smart Girl Empowerment Platform - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });
        document.addEventListener('click', function (e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 20px rgba(30,86,160,0.15)';
            } else {
                navbar.style.boxShadow = '0 4px 6px -1px rgba(0,0,0,0.1)';
            }
        });
    }

    // Auto-hide flash messages
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 500);
        }, 5000);
    }

    // Confirm delete actions
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // Modal handling
    document.querySelectorAll('[data-modal]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const modal = document.getElementById(trigger.getAttribute('data-modal'));
            if (modal) modal.classList.add('active');
        });
    });
    document.querySelectorAll('.modal-close, .modal-overlay').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target === el) {
                el.closest('.modal-overlay').classList.remove('active');
            }
        });
    });

    // Form validation helper
    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const required = form.querySelectorAll('[required]');
            let valid = true;
            required.forEach(function (field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '';
                }
            });
            if (!valid) e.preventDefault();
        });
    });
});
