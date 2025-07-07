// public/js/app.js

document.addEventListener('DOMContentLoaded', function () {

    // --- 1. Global/Common Features ---

    // Mobile Menu Functionality
    const mobileNavToggle = document.getElementById('mobileNavToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeBtn = document.getElementById('closeBtn');

    if (mobileNavToggle && mobileMenu && closeBtn) {
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');

        mobileNavToggle.addEventListener('click', () => {
            mobileMenu.classList.add('active');
        });
        closeBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
        });
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });
        });
    }

    // On-Scroll Reveal Animation (Applies to .reveal and .animate-on-scroll)
    const revealElements = document.querySelectorAll('.reveal');
    const animateOnScrollElements = document.querySelectorAll('.animate-on-scroll');

    const handleRevealOnScroll = () => {
        const windowHeight = window.innerHeight;
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 80;
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });
        animateOnScrollElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 100;
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('is-visible');
            }
        });
    };
    window.addEventListener('scroll', handleRevealOnScroll);
    handleRevealOnScroll(); // Run on load to reveal elements already in view

    // Active Navigation State on Scroll
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.desktop-nav a');

    if (sections.length > 0 && navLinks.length > 0) {
        const navObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    navLinks.forEach(link => {
                        link.classList.remove('active-link');
                        // Adjusting this to match Laravel routes later:
                        // For now, it matches based on section IDs on index.html
                        if (link.getAttribute('href') === `#${id}`) {
                            link.classList.add('active-link');
                        }
                    });
                }
            });
        }, { rootMargin: '-30% 0px -70% 0px' });
        sections.forEach(section => navObserver.observe(section));
    }

    // This script does NOT include map, product filtering/loading, news loading, or product detail logic.

}); // End of DOMContentLoaded