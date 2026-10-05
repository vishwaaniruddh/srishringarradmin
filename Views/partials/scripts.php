<script>
    // Global support script for shared admin layout interactions
    document.addEventListener('DOMContentLoaded', () => {
        // Safe fallback for mobile sidebar buttons if not already bound
        const openSidebarBtn = document.getElementById('open-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');

        if (openSidebarBtn && !openSidebarBtn.onclick && typeof window.toggleMobileSidebar === 'function') {
            openSidebarBtn.addEventListener('click', (e) => {
                window.toggleMobileSidebar(e);
            });
        }

        if (closeSidebarBtn && !closeSidebarBtn.onclick && typeof window.closeMobileSidebar === 'function') {
            closeSidebarBtn.addEventListener('click', (e) => {
                window.closeMobileSidebar(e);
            });
        }
    });
</script>
