const menuButton = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('.main-nav');

if (menuButton && mainNav) {
    menuButton.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        menuButton.setAttribute('aria-expanded', String(isOpen));
    });
}

// Tutup popover laporan/edit saat klik di luar.
document.addEventListener('click', (event) => {
    document.querySelectorAll('details.report-details[open], details.admin-edit-details[open]').forEach((details) => {
        if (!details.contains(event.target)) {
            details.removeAttribute('open');
        }
    });
});
