// app.js
document.addEventListener('DOMContentLoaded', () => {
    console.log('Malath PHP App Loaded Successfully');
    
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
