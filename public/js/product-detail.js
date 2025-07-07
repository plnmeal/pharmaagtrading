// public/js/product-detail.js

document.addEventListener('DOMContentLoaded', () => {

    // --- Image Gallery Logic ---
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = document.querySelectorAll('.thumbnail');
    if (mainImage && thumbnails.length) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                mainImage.src = this.src;
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    // --- Tabbed Interface Logic ---
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    if (tabLinks.length && tabContents.length) {
        // Activate the first tab by default if none are active
        if (!document.querySelector('.tab-link.active')) {
            if (tabLinks[0]) tabLinks[0].classList.add('active');
            if (tabContents[0]) tabContents[0].classList.add('active');
        }

        tabLinks.forEach(link => {
            link.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                tabLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                tabContents.forEach(content => {
                    content.classList.toggle('active', content.id === tabId);
                });
            });
        });
    }

    // --- Select Button Logic ---
    const selectBtn = document.getElementById('select-btn');
    if (selectBtn) {
        selectBtn.addEventListener('click', function() {
            this.classList.toggle('selected');
            if (this.classList.contains('selected')) {
                this.innerHTML = '<i class="fas fa-check"></i> Selected';
            } else {
                this.innerHTML = 'Select';
            }
        });
    }

});