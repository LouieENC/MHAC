// Handle sidebar toggle
const sidebarToggleBtn = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('sidebar');

sidebarToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
});

// JavaScript to toggle sidebar and overlay
document.addEventListener("DOMContentLoaded", function () {
    const burger = document.querySelector('.burger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    // Toggle sidebar and overlay
    burger.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });

    // Hide sidebar and overlay when overlay is clicked
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
});