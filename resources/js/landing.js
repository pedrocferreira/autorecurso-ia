import 'aos/dist/aos.css';
import AOS from 'aos';

AOS.init({
    duration: 800,
    once: true,
    easing: 'ease-out-cubic'
});

// Mobile menu
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    menuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        mobileMenu.classList.toggle('active');
    });

    document.querySelectorAll('.mobile-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            mobileMenu.classList.remove('active');
        });
    });

    // FAQ Accordion
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const faqItem = button.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });

    // Counter Animation
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                animateCounter(counter, target);
                counterObserver.unobserve(counter);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.counter').forEach(counter => {
        counterObserver.observe(counter);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Enhanced CTA buttons interaction
    document.querySelectorAll('.cta-button.enhanced').forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Counter animation function
function animateCounter(element, target) {
    element.classList.add('counting');
    let current = 0;
    const increment = target / 50; // 50 steps
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
            element.classList.remove('counting');
        }
        
        // Format the number based on the target
        if (target === 95) {
            element.textContent = Math.floor(current) + '%';
        } else if (target >= 1000) {
            element.textContent = '+' + Math.floor(current).toLocaleString('pt-BR');
        } else {
            element.textContent = Math.floor(current);
        }
    }, 50);
}

// Performance optimization - Lazy load heavy elements
const lazyLoadOptions = {
    threshold: 0.1,
    rootMargin: '50px 0px'
};

const lazyLoadObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const element = entry.target;
            
            // Add loaded class for any additional animations
            element.classList.add('loaded');
            
            // Stop observing this element
            lazyLoadObserver.unobserve(element);
        }
    });
}, lazyLoadOptions);

// Observe elements for lazy loading
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.stat-card, .feature-card, .testimonial-card').forEach(element => {
        lazyLoadObserver.observe(element);
    });
}); 