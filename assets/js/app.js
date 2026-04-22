// app.js
document.addEventListener('DOMContentLoaded', () => {
    console.log('Malath PHP App Loaded Successfully');
    
    // Notification Toggle
    const notifBtn = document.getElementById('notification-btn');
    const notifDropdown = document.getElementById('notification-dropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            notifDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
                notifDropdown.classList.remove('active');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        notifDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    // Profile Tabs
    const profileTabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    if (profileTabs.length > 0) {
        profileTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                profileTabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to the clicked tab
                tab.classList.add('active');
                
                // Show corresponding content
                const targetId = tab.getAttribute('data-tab');
                const targetContent = document.getElementById(targetId);
                if(targetContent) {
                    targetContent.classList.add('active');
                    // Retrigger animation
                    targetContent.classList.remove('animate-fade-in-up');
                    void targetContent.offsetWidth; // Trigger reflow
                    targetContent.classList.add('animate-fade-in-up');
                }
            });
        });
    }
});
