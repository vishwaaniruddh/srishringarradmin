<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const openSidebarBtn = document.getElementById('open-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');

        // Sidebar Toggling
        const toggleSidebar = () => {
            sidebar.classList.toggle('-translate-x-full');
        };

        if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);

        // Submenu Toggling
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const submenu = toggle.nextElementSibling;
                const chevron = toggle.querySelector('.chevron');
                
                // Toggle Submenu visibility
                submenu.classList.toggle('hidden');
                
                // Rotate Chevron
                chevron.classList.toggle('rotate-90');
            });
        });
    });
</script>
