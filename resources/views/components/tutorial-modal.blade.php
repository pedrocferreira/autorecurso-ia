<!-- Modal de Tutorial -->
<div class="fixed inset-0 flex items-center justify-center z-[9999] hidden backdrop-blur-[30px]" id="tutorialModal">
    <!-- Overlay com blur -->
    <div class="absolute inset-0 bg-white/30"></div>
    
    <!-- Modal Content -->
    <style>
        @media (max-width: 768px) {
            #tutorialModal .modal-content {
                width: 100% !important;
            }
        }
    </style>
    <div class="modal-content bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-hidden transform transition-all duration-300 ease-out flex flex-col z-10" style="width:50%;">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center p-3 sm:p-4 border-b border-gray-200">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="bg-blue-100 p-1.5 sm:p-2 rounded-lg" style="padding: 12px;">
                    <i class="fas fa-graduation-cap text-base sm:text-lg text-blue-600"></i>
                </div>
                <div style="padding: 10px;">
                    <h2 class="text-sm sm:text-base font-semibold text-gray-900">Bem-vindo ao AutoRecurso!</h2>
                    <p class="text-xs text-gray-500">Um rápido tour pelo sistema (<span id="currentStep">1</span>/3)</p>
                </div>
            </div>
            <button onclick="closeTutorial()" class="text-gray-500 hover:text-gray-700 transition-colors rounded-full p-2 hover:bg-gray-100 flex items-center justify-center w-8 h-8">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Carrossel Wrapper -->
        <div class="relative flex-grow overflow-hidden p-2">
            <!-- Slides -->
            <div class="flex h-full transition-transform duration-500 ease-in-out" id="tutorialSlides">
                <!-- Slide 1: Introdução -->
                <div class="w-full flex-shrink-0 p-4 sm:p-6 flex flex-col items-center justify-center text-center">
                    <div class="bg-blue-600 w-12 h-12 sm:w-16 sm:h-16 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-5 shadow-md">
                        <i class="fas fa-car text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Sistema de Geração de Recursos de Multas</h3>
                    <p class="text-xs sm:text-sm text-gray-600 max-w-sm mx-auto leading-relaxed">Bem-vindo ao AutoRecurso! Gere seus recursos contra multas de trânsito de forma simples e eficiente com os dados que você fornecer.</p>
                </div>

                <!-- Slide 2: Cadastro da Infração -->
                <div class="w-full flex-shrink-0 p-4 sm:p-6 flex flex-col items-center justify-center text-center">
                    <div class="bg-green-600 w-12 h-12 sm:w-16 sm:h-16 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-5 shadow-md">
                        <i class="fas fa-file-alt text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Informe os Dados da Multa</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 max-w-sm mx-auto leading-relaxed">Para gerar seu recurso, clique em "Nova Multa" e preencha os detalhes da infração recebida.</p>
                    <img src="{{ asset('images/tutorial/criarMulta2.png') }}" alt="Cadastro de Multa" class="rounded-lg shadow-md max-h-36 sm:max-h-48 border border-gray-200">
                </div>

                <!-- Slide 3: Consulta de Créditos -->
                <div class="w-full flex-shrink-0 p-4 sm:p-6 flex flex-col items-center justify-center text-center">
                    <div class="bg-purple-600 w-12 h-12 sm:w-16 sm:h-16 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-5 shadow-md">
                        <i class="fas fa-coins text-xl sm:text-2xl text-white"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Consulte o Consumo de Créditos</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 max-w-sm mx-auto leading-relaxed">Verifique quantos créditos são consumidos para cada tipo de multa antes de gerar o recurso.</p>
                    <img src="{{ asset('images/tutorial/tabela.png') }}" alt="Tabela de Créditos" class="rounded-lg shadow-md max-h-36 sm:max-h-48 border border-gray-200">
                </div>
            </div>

            <!-- Botões de Navegação -->
            <button onclick="prevSlide()" class="absolute left-1 sm:left-3 top-1/2 transform -translate-y-1/2 bg-white/80 backdrop-blur-sm p-1.5 sm:p-2 rounded-full shadow-md hover:bg-white transition-all duration-200">
                <i class="fas fa-chevron-left text-gray-700 text-sm sm:text-base"></i>
            </button>
            <button onclick="nextSlide()" class="absolute right-1 sm:right-3 top-1/2 transform -translate-y-1/2 bg-white/80 backdrop-blur-sm p-1.5 sm:p-2 rounded-full shadow-md hover:bg-white transition-all duration-200">
                <i class="fas fa-chevron-right text-gray-700 text-sm sm:text-base"></i>
            </button>
        </div>

        <!-- Rodapé / Controles -->
        <div class="flex justify-between items-center p-3 sm:p-4 border-t border-gray-200 bg-gray-50/50">
            <!-- Indicadores de Slide -->
            <div class="flex space-x-1.5 sm:space-x-2" id="slideIndicators">
                <button class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-blue-600 ring-2 ring-blue-200 transition-all duration-300" onclick="goToSlide(0)"></button>
                <button class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gray-300 hover:bg-gray-400 transition-all duration-300" onclick="goToSlide(1)"></button>
                <button class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gray-300 hover:bg-gray-400 transition-all duration-300" onclick="goToSlide(2)"></button>
            </div>
            <!-- Botões de Ação -->
            <div class="flex items-center gap-2 sm:gap-3">
                <button onclick="closeTutorial()" class="text-gray-600 hover:text-gray-900 transition-colors text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 sm:py-1.5 rounded-md hover:bg-gray-200">
                    Pular
                </button>
                <button id="nextButton" onclick="nextSlide()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-5 py-1 sm:py-1.5 rounded-md transition-all duration-200 text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-1.5 shadow hover:shadow-md" style="height: 36px;width: 100%;padding: 4px;">
                    <span id="nextButtonText">Próximo</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentSlide = 0;
const totalSlides = 3;

function showTutorial() {
    const modal = document.getElementById('tutorialModal');
    modal.classList.remove('hidden');
    modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
    modal.querySelector('div').classList.add('scale-100', 'opacity-100');
    document.body.style.overflow = 'hidden';
    localStorage.setItem('tutorialShown', 'true');
    updateSlideUI(); // Initialize UI
}

function closeTutorial() {
    const modal = document.getElementById('tutorialModal');
    modal.querySelector('div').classList.add('scale-95', 'opacity-0');
    modal.querySelector('div').classList.remove('scale-100', 'opacity-100');
    document.body.style.overflow = '';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function updateSlideUI() {
    const slidesContainer = document.getElementById('tutorialSlides');
    const indicators = document.getElementById('slideIndicators').children;
    const currentStepSpan = document.getElementById('currentStep');
    const nextButton = document.getElementById('nextButton');
    const nextButtonText = document.getElementById('nextButtonText');

    // Move slides
    slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;

    // Update indicators
    for (let i = 0; i < indicators.length; i++) {
        indicators[i].classList.remove('bg-blue-600', 'ring-2', 'ring-blue-200');
        indicators[i].classList.add('bg-gray-300', 'hover:bg-gray-400');
    }
    indicators[currentSlide].classList.add('bg-blue-600', 'ring-2', 'ring-blue-200');
    indicators[currentSlide].classList.remove('bg-gray-300', 'hover:bg-gray-400');

    // Update step counter
    currentStepSpan.textContent = currentSlide + 1;

    // Update next button text
    if (currentSlide === totalSlides - 1) {
        nextButtonText.textContent = 'Começar';
        nextButton.onclick = function() {
            window.location.href = '/tickets/create';
        };
    } else {
        nextButtonText.textContent = 'Próximo';
        nextButton.onclick = nextSlide;
    }
}

function nextSlide() {
    if (currentSlide < totalSlides - 1) {
        currentSlide++;
        updateSlideUI();
    } else {
        window.location.href = '/tickets/create';
    }
}

function prevSlide() {
    if (currentSlide > 0) {
        currentSlide--;
        updateSlideUI();
    }
}

function goToSlide(index) {
    currentSlide = index;
    updateSlideUI();
}

document.addEventListener('DOMContentLoaded', function() {
    if (!localStorage.getItem('tutorialShown')) {
        // Initial state for animation
        const modalContent = document.getElementById('tutorialModal')?.querySelector('div');
        if (modalContent) {
             modalContent.classList.add('scale-95', 'opacity-0');
        }
        setTimeout(showTutorial, 1000);
    }

    // Hide nav buttons if only one slide (precautionary)
    if (totalSlides <= 1) {
        document.querySelector('button[onclick="prevSlide()"]').style.display = 'none';
        document.querySelector('button[onclick="nextSlide()"]').style.display = 'none';
        document.getElementById('slideIndicators').style.display = 'none';
    }
});

// Remove old animation style tag if exists
const oldStyle = document.getElementById('tutorial-animation-style');
if (oldStyle) {
    oldStyle.remove();
}
</script> 