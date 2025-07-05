// Landing Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling for anchor links
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

    // Intersection Observer for animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);

    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });

    // Counter animation
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 100;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString('pt-BR');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString('pt-BR');
            }
        }, 20);
    }

    // Initialize counters when they become visible
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const target = parseInt(element.dataset.target);
                if (target) {
                    animateCounter(element, target);
                }
                counterObserver.unobserve(element);
            }
        });
    }, observerOptions);

    // Observe counter elements
    document.querySelectorAll('.counter-number').forEach(counter => {
        counterObserver.observe(counter);
    });

    // Add scroll effect to header
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const header = document.querySelector('header');
        
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            header.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            header.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });

    // Add floating action button
    const floatingBtn = document.createElement('div');
    floatingBtn.innerHTML = `
        <a href="/cliente/wizard" class="fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 z-50 flex items-center justify-center">
            <i class="fas fa-comments text-xl"></i>
        </a>
    `;
    document.body.appendChild(floatingBtn);

    // Show/hide floating button based on scroll
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const floatingButton = document.querySelector('.fixed.bottom-6.right-6');
        
        if (scrollTop > 300) {
            floatingButton.style.opacity = '1';
            floatingButton.style.transform = 'scale(1)';
        } else {
            floatingButton.style.opacity = '0';
            floatingButton.style.transform = 'scale(0)';
        }
    });

    // Form validation and submission
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Enviando...';
            submitBtn.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Show success message
                alert('Obrigado! Entraremos em contato em breve.');
            }, 2000);
        });
    });

    // Add WhatsApp integration
    const whatsappNumber = '5511999999999'; // Replace with actual number
    const whatsappMessage = 'Olá! Gostaria de saber mais sobre o serviço de anulação de multas da AutoRecurso.';
    
    // Create WhatsApp button
    const whatsappBtn = document.createElement('div');
    whatsappBtn.innerHTML = `
        <a href="https://wa.me/${whatsappNumber}?text=${encodeURIComponent(whatsappMessage)}" 
           target="_blank" 
           class="fixed bottom-6 left-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 z-50 flex items-center justify-center">
            <i class="fab fa-whatsapp text-xl"></i>
        </a>
    `;
    document.body.appendChild(whatsappBtn);

    // Add click tracking for analytics
    document.querySelectorAll('a, button').forEach(element => {
        element.addEventListener('click', function() {
            // Track button clicks
            const text = this.textContent.trim();
            const href = this.getAttribute('href');
            
            console.log('Button clicked:', {
                text: text,
                href: href,
                timestamp: new Date().toISOString()
            });
            
            // Here you would integrate with your analytics service
            // Example: gtag('event', 'click', { event_category: 'button', event_label: text });
        });
    });

    // Add scroll progress indicator
    const progressBar = document.createElement('div');
    progressBar.className = 'fixed top-0 left-0 w-full h-1 bg-blue-600 z-50 transition-all duration-300';
    progressBar.style.width = '0%';
    document.body.appendChild(progressBar);

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / documentHeight) * 100;
        
        progressBar.style.width = scrollPercent + '%';
    });

    // Add testimonial carousel functionality
    const testimonials = document.querySelectorAll('.testimonial-card');
    let currentTestimonial = 0;

    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            if (i === index) {
                testimonial.style.display = 'block';
                testimonial.style.opacity = '1';
            } else {
                testimonial.style.opacity = '0.5';
            }
        });
    }

    // Auto-rotate testimonials on mobile
    if (window.innerWidth <= 768) {
        setInterval(() => {
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            showTestimonial(currentTestimonial);
        }, 5000);
    }

    // Add price calculator
    window.calculatePrice = function(fineAmount) {
        if (fineAmount <= 130) {
            return 39;
        } else if (fineAmount <= 300) {
            return 79;
        } else {
            return 129;
        }
    };

    // Add exit intent popup
    let exitIntentShown = false;
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY <= 0 && !exitIntentShown) {
            exitIntentShown = true;
            showExitIntentPopup();
        }
    });

    function showExitIntentPopup() {
        const popup = document.createElement('div');
        popup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        popup.innerHTML = `
            <div class="bg-white p-8 rounded-lg max-w-md mx-4 text-center">
                <h3 class="text-2xl font-bold mb-4">Espere! Não vá embora!</h3>
                <p class="mb-6">Que tal uma análise gratuita da sua multa antes de sair?</p>
                <div class="flex gap-4 justify-center">
                    <a href="/cliente/wizard" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Análise Gratuita
                    </a>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400">
                        Fechar
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(popup);

        // Auto-close after 10 seconds
        setTimeout(() => {
            if (popup.parentElement) {
                popup.remove();
            }
        }, 10000);
    }

    // Add loading screen
    const loadingScreen = document.createElement('div');
    loadingScreen.className = 'fixed inset-0 bg-white flex items-center justify-center z-50';
    loadingScreen.innerHTML = `
        <div class="text-center">
            <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Carregando...</p>
        </div>
    `;
    document.body.appendChild(loadingScreen);

    // Hide loading screen after page loads
    window.addEventListener('load', function() {
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.remove();
        }, 500);
    });

    // Add lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('opacity-0');
                img.classList.add('opacity-100');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        imageObserver.observe(img);
    });
}); 