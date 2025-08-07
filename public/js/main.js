// Main JavaScript for PubAd Website
// This file handles all client-side interactivity.

document.addEventListener('DOMContentLoaded', function() {

    /**
     * Mobile Menu Toggle
     * Handles opening and closing the hamburger menu on mobile devices.
     */
    const hamburger = document.querySelector('.hamburger-menu');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
    }

    /**
     * Back to Top Button
     * Shows the button when the user scrolls down and scrolls to top on click.
     */
    const backToTopButton = document.getElementById('back-to-top');

    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) { // Show after 300px of scrolling
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Animate-on-Scroll
     * Uses Intersection Observer API to add a 'visible' class to elements as they enter the viewport.
     */
    const animatedElements = document.querySelectorAll('.slide-up, .fade-in');

    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Optional: stop observing once visible
                }
            });
        }, {
            threshold: 0.1 // Trigger when 10% of the element is visible
        });

        animatedElements.forEach(element => {
            observer.observe(element);
        });
    }

    /**
     * Cookie Consent Bar
     * Shows the bar and sets a cookie when the user accepts.
     */
    const consentBar = document.getElementById('cookie-consent-bar');
    const consentButton = document.getElementById('cookie-consent-button');

    if (consentBar && consentButton) {
        // Check if cookie is already set
        if (!getCookie('cookie_consent')) {
            // Use a timeout to avoid being too intrusive immediately
            setTimeout(() => {
                consentBar.classList.add('active');
            }, 2000);
        }

        consentButton.addEventListener('click', () => {
            setCookie('cookie_consent', 'true', 365); // Set cookie for 1 year
            consentBar.classList.remove('active');
        });
    }

    // Helper functions for cookies
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }


    /**
     * Ad Formats Slider (Simple Implementation)
     * A basic slider to showcase different ad formats.
     */
    const slider = document.querySelector('.ad-formats-slider');
    if (slider) {
        const slides = slider.querySelectorAll('.slider-item');
        const nextBtn = document.querySelector('.slider-next');
        const prevBtn = document.querySelector('.slider-prev');
        let currentSlide = 0;

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            slides[n].classList.add('active');
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        if (nextBtn && prevBtn && slides.length > 0) {
            nextBtn.addEventListener('click', nextSlide);
            prevBtn.addEventListener('click', prevSlide);

            // Auto-play functionality
            setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }
    }
});
