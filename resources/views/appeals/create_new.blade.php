@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">🚀 Criar Novo Recurso</h1>
        <p class="text-gray-600">Gere recursos administrativos contra multas de trânsito com IA</p>
    </div>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progresso</span>
            <span id="progressText" class="text-sm font-medium text-gray-700">0% Completo</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Step Navigation -->
    <div class="mb-8">
        <div class="flex space-x-2 overflow-x-auto">
            <button class="step-btn active bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                📤 Upload de Documentos
            </button>
            <button class="step-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                👤 Dados Pessoais
            </button>
            <button class="step-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                🚗 Dados do Veículo
            </button>
            <button class="step-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                ⚠️ Dados da Multa
            </button>
            <button class="step-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                🎯 Argumentos Jurídicos
            </button>
            <button class="step-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                ✅ Revisão e Envio
            </button>
        </div>
    </div>

    <!-- Step 0: Upload de Documentos -->
    <div class="step-content" id="step-0">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📤 Upload de Documentos</h3>
            
            <!-- Busca de Placa -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">🚗 Buscar Dados do Veículo</h4>
                    
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Placa do Veículo</label>
                            <input type="text" id="plateSearch" placeholder="Digite a placa (ex: ABC-1234)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="formatPlateSearch(this)">
                        </div>
                        <div class="flex items-end">
                            <button onclick="searchVehicleData()" id="btnSearchVehicle" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                🔍 Buscar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Status da busca -->
                    <div id="vehicle-search-status" class="mt-4 hidden"></div>
                    
                    <!-- Resultado da busca -->
                    <div id="vehicle-data-result" class="mt-4 hidden">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h5 class="font-semibold text-green-900 mb-2">✅ Dados do Veículo Encontrados</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">Modelo:</span> <span id="vehicle-model-result"></span>
                                </div>
                                <div>
                                    <span class="font-medium">Ano:</span> <span id="vehicle-year-result"></span>
                                </div>
                                <div>
                                    <span class="font-medium">Cor:</span> <span id="vehicle-color-result"></span>
                                </div>
                                <div>
                                    <span class="font-medium">Marca:</span> <span id="vehicle-brand-result"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Upload da CNH -->
                <div class="bg-white rounded-2xl border-2 border-green-200 p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <i class="fas fa-id-card text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            🪪 CNH
                        </h3>
                        <p class="text-gray-600">Frente e verso da CNH</p>
                    </div>
                    
                    <!-- Área de Drop da CNH -->
                    <div id="cnhDropZone"
                         class="border-2 border-dashed border-green-300 rounded-xl p-8 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-300 cursor-pointer bg-green-50/50 group"
                         onclick="document.getElementById('cnhFileInput').click()">
                        
                        <input type="file" 
                               id="cnhFileInput" 
                               accept="image/*" 
                               style="display: none;">
                        
                        <div class="group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-cloud-upload-alt text-4xl text-green-500 mb-4"></i>
                            <p class="text-lg font-semibold text-gray-700 mb-2">Arraste a CNH aqui</p>
                            <p class="text-sm text-gray-500 mb-4">ou clique para selecionar</p>
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Formatos: JPG, PNG, PDF
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status do Upload CNH -->
                    <div id="cnh-status" class="mt-4 text-center hidden">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                            <span class="text-blue-600">Processando CNH...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Upload da Notificação -->
                <div class="bg-white rounded-2xl border-2 border-blue-200 p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            📋 Notificação/Multa
                        </h3>
                        <p class="text-gray-600">Imagem da notificação ou multa</p>
                    </div>
                    
                    <!-- Área de Drop da Notificação -->
                    <div id="notificationDropZone" 
                         class="border-2 border-dashed border-blue-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all duration-300 cursor-pointer bg-blue-50/50 group"
                         onclick="document.getElementById('notificationFileInput').click()">
                        
                        <input type="file" 
                               id="notificationFileInput" 
                               accept="image/*" 
                               style="display: none;">
                        
                        <div class="group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-cloud-upload-alt text-4xl text-blue-500 mb-4"></i>
                            <p class="text-lg font-semibold text-gray-700 mb-2">Arraste a notificação aqui</p>
                            <p class="text-sm text-gray-500 mb-4">ou clique para selecionar</p>
                            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Formatos: JPG, PNG, PDF
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status do Upload Notificação -->
                    <div id="notification-status" class="mt-4 text-center hidden">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                            <span class="text-blue-600">Processando notificação...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário Principal -->
    <form id="appealForm" action="{{ route('appeals.store_new') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Step 1: Dados Pessoais -->
        <div class="step-content hidden" id="step-1">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">👤 Dados Pessoais</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                        <input type="text" name="name" id="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                        <input type="text" name="cpf" id="cpf" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="formatCpf(this)">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CNH</label>
                        <input type="text" name="driver_license" id="driver_license" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="text" name="phone" id="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="formatPhone(this)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Dados do Veículo -->
        <div class="step-content hidden" id="step-2">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">🚗 Dados do Veículo</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
                        <input type="text" name="plate" id="plate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" oninput="formatPlate(this)">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                        <input type="text" name="vehicle_model" id="vehicle_model" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ano</label>
                        <input type="text" name="vehicle_year" id="vehicle_year" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
                        <input type="text" name="vehicle_color" id="vehicle_color" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Dados da Multa -->
        <div class="step-content hidden" id="step-3">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">⚠️ Dados da Multa</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número da Notificação</label>
                        <input type="text" name="citation_number" id="citation_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                        <input type="date" name="date" id="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Horário</label>
                        <input type="time" name="time" id="time" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Local</label>
                        <input type="text" name="location" id="location" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valor</label>
                        <input type="number" name="amount" id="amount" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Infração</label>
                        <select name="infraction_type_id" id="infraction_type_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadJustifications(this.value)">
                            <option value="">Selecione o tipo de infração...</option>
                            @foreach($infractionTypes as $type)
                                <option value="{{ $type->id }}" data-code="{{ $type->code }}">{{ $type->code }} - {{ $type->description }}</option>
                            @endforeach
                        </select>
                        <div id="infraction-detected-badge" class="mt-2 hidden">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Detectado automaticamente (você pode alterar)
                            </span>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Motivo da Infração</label>
                        <textarea name="reason" id="reason" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Descreva o motivo da infração..." oninput="detectInfractionType(this.value)"></textarea>
                        
                        <!-- Status de Detecção -->
                        <div id="detection-status" class="mt-2 hidden"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Argumentos Jurídicos -->
        <div class="step-content hidden" id="step-4">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">🎯 Argumentos Jurídicos</h3>
                
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-lightbulb text-4xl mb-4"></i>
                    <p>Selecione um tipo de infração para ver as estratégias disponíveis</p>
                </div>
            </div>
        </div>

        <!-- Step 5: Revisão e Envio -->
        <div class="step-content hidden" id="step-5">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">✅ Revisão e Envio</h3>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6" id="review-summary">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-semibold text-green-900">Revisão Rápida</h4>
                            <p class="text-green-700">Confira os dados antes de gerar o recurso.</p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm" id="review-summary-grid"></div>
                </div>
                
                <div class="text-center">
                    <button type="button" onclick="generateAppeal()" id="btnSubmit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                        🚀 Gerar Recurso
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 6: Recurso Gerado -->
        <div class="step-content hidden" id="step-6">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="text-center">
                    <div class="mb-6">
                        <i class="fas fa-file-alt text-green-500 text-6xl mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">✅ Recurso Gerado com Sucesso!</h3>
                        <p class="text-gray-600">Seu recurso administrativo foi criado e está pronto para download.</p>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h4 class="font-semibold text-green-900 mb-3">📋 Detalhes do Recurso</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Número:</span> <span id="appeal-number">-</span>
                            </div>
                            <div>
                                <span class="font-medium">Data:</span> <span id="appeal-date">-</span>
                            </div>
                            <div>
                                <span class="font-medium">Tipo de Infração:</span> <span id="appeal-infraction">-</span>
                            </div>
                            <div>
                                <span class="font-medium">Status:</span> <span class="text-green-600 font-semibold">Gerado</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button onclick="downloadAppeal('pdf')" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            📄 Download PDF
                        </button>
                        <button onclick="downloadAppeal('doc')" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            📝 Download DOC
                        </button>
                        <button onclick="downloadAppeal('docx')" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors">
                            📝 Download DOCX
                        </button>
                    </div>
                    
                    <div class="mt-6">
                        <button onclick="window.location.href='/appeals/create-new'" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                            🆕 Criar Novo Recurso
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campos ocultos para dados obrigatórios -->
        <input type="hidden" name="points" value="0">
        <input type="hidden" name="vehicle_chassi" value="N/A">
        <input type="hidden" name="vehicle_renavam" value="N/A">
        <input type="hidden" name="city" value="N/A">
        <input type="hidden" name="state" value="N/A">
    </form>

    <!-- Navegação -->
    <div class="flex justify-between mt-8">
        <button id="btnPrev" onclick="prevStep()" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors hidden">
            ⬅️ Anterior
        </button>
        
        <button id="btnNext" onclick="nextStep()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
            Próximo ➡️
        </button>
    </div>
</div>

<script>
console.log('🚀 Script principal carregado!');

// Variáveis globais
let currentStep = 0;
const totalSteps = 7;
let selectedJustification = null;
let generatedAppealId = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando formulário...');
    
    try {
        updateProgress();
        setupUploadListeners();
        console.log('✅ Inicialização concluída');
    } catch (error) {
        console.error('❌ Erro na inicialização:', error);
    }
});

// Funções de navegação
function nextStep() {
    if (currentStep < totalSteps - 1) {
        currentStep++;
        updateProgress();
        showStep(currentStep);
    }
}

function prevStep() {
    if (currentStep > 0) {
        currentStep--;
        updateProgress();
        showStep(currentStep);
    }
}

function showStep(step) {
    // Esconder todos os steps
    document.querySelectorAll('.step-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Mostrar o step atual
    const currentStepEl = document.getElementById(`step-${step}`);
    if (currentStepEl) {
        currentStepEl.classList.remove('hidden');
    }
    
    // Configurar upload listeners se estiver no step 0
    if (step === 0) {
        setupUploadListeners();
    }
    
    // Atualizar botões
    updateNavigationButtons();
    updateStepButtons();

    // Popular resumo na revisão
    if (step === 5) {
        try { populateReviewSummary(); } catch (e) { console.error(e); }
    }
}

function updateNavigationButtons() {
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (currentStep > 0) {
        btnPrev.classList.remove('hidden');
    } else {
        btnPrev.classList.add('hidden');
    }
    
    if (currentStep === totalSteps - 1) {
        btnNext.classList.add('hidden');
        btnSubmit.classList.remove('hidden');
    } else {
        btnNext.classList.remove('hidden');
        btnSubmit.classList.add('hidden');
    }
}

function updateStepButtons() {
    document.querySelectorAll('.step-btn').forEach((btn, index) => {
        btn.classList.remove('active', 'bg-blue-500', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        
        if (index === currentStep) {
            btn.classList.add('active', 'bg-blue-500', 'text-white');
            btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        }
    });
}

function updateProgress() {
    const progress = (currentStep / (totalSteps - 1)) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressText').textContent = Math.round(progress) + '% Completo';
}

// Funções de upload
function setupUploadListeners() {
    console.log('🔧 Configurando upload listeners...');
    
    try {
        const cnhFileInput = document.getElementById('cnhFileInput');
        const notificationFileInput = document.getElementById('notificationFileInput');
        const cnhDropZone = document.getElementById('cnhDropZone');
        const notificationDropZone = document.getElementById('notificationDropZone');
        
        console.log('📋 Elementos encontrados:', {
            cnhFileInput: !!cnhFileInput,
            notificationFileInput: !!notificationFileInput,
            cnhDropZone: !!cnhDropZone,
            notificationDropZone: !!notificationDropZone
        });
        
        if (!cnhFileInput || !notificationFileInput || !cnhDropZone || !notificationDropZone) {
            console.error('❌ Alguns elementos de upload não foram encontrados!');
            return;
        }

        // Configurar CNH
        if (cnhFileInput) {
            cnhFileInput.removeEventListener('change', cnhFileInput._changeHandler);
            cnhFileInput._changeHandler = function(event) {
                console.log('📄 CNH selecionada:', event.target.files[0]);
                if (event.target.files.length > 0) {
                    processCnhUpload(event.target.files[0]);
                }
            };
            cnhFileInput.addEventListener('change', cnhFileInput._changeHandler);
        }
        
        // Configurar Notificação
        if (notificationFileInput) {
            notificationFileInput.removeEventListener('change', notificationFileInput._changeHandler);
            notificationFileInput._changeHandler = function(event) {
                console.log('📄 Notificação selecionada:', event.target.files[0]);
                if (event.target.files.length > 0) {
                    processNotificationUpload(event.target.files[0]);
                }
            };
            notificationFileInput.addEventListener('change', notificationFileInput._changeHandler);
        }
        
        // Drag and drop
        if (cnhDropZone) {
            cnhDropZone.addEventListener('dragover', function(event) {
                event.preventDefault();
            });
            cnhDropZone.addEventListener('drop', function(event) {
                event.preventDefault();
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    processCnhUpload(files[0]);
                }
            });
        }
        
        if (notificationDropZone) {
            notificationDropZone.addEventListener('dragover', function(event) {
                event.preventDefault();
            });
            notificationDropZone.addEventListener('drop', function(event) {
                event.preventDefault();
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    processNotificationUpload(files[0]);
                }
            });
        }
        
        console.log('✅ Upload listeners configurados');
    } catch (error) {
        console.error('❌ Erro ao configurar upload listeners:', error);
    }
}

async function processCnhUpload(file) {
    console.log('🚀 Processando CNH:', file.name);
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'cnh');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    document.getElementById('cnh-status').classList.remove('hidden');
    
    try {
        const response = await fetch('/extract-document-data', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        console.log('📋 Resultado CNH:', result);
        
        if (result.success) {
            fillFormWithExtractedData(result.data);
            alert('✅ CNH processada com sucesso!');
        } else {
            alert('❌ Erro ao processar CNH: ' + result.message);
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        alert('❌ Erro ao processar arquivo.');
    } finally {
        document.getElementById('cnh-status').classList.add('hidden');
    }
}

async function processNotificationUpload(file) {
    console.log('🚀 Processando Notificação:', file.name);
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'notification');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    document.getElementById('notification-status').classList.remove('hidden');
    
    try {
        const response = await fetch('/extract-document-data', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        console.log('📋 Resultado Notificação:', result);
        
        if (result.success) {
            fillFormWithExtractedData(result.data);
            alert('✅ Notificação processada com sucesso!');
        } else {
            alert('❌ Erro ao processar notificação: ' + result.message);
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        alert('❌ Erro ao processar arquivo.');
    } finally {
        document.getElementById('notification-status').classList.add('hidden');
    }
}

function fillFormWithExtractedData(data) {
    console.log('📝 Preenchendo formulário com dados:', data);
    
    let cnhData = data.cnh || data;
    let notificationData = data.notification || data;
    let vehicleData = data.vehicle || data;
    
    // Preencher dados da CNH
    if (cnhData.name) {
        document.getElementById('name').value = cnhData.name;
    }
    if (cnhData.cpf) {
        document.getElementById('cpf').value = cnhData.cpf;
    }
    if (cnhData.driver_license) {
        document.getElementById('driver_license').value = cnhData.driver_license;
    }
    
    // Preencher dados do veículo
    if (vehicleData.plate) {
        document.getElementById('plate').value = vehicleData.plate;
    }
    if (vehicleData.model || vehicleData.vehicle_model) {
        const model = vehicleData.model || vehicleData.vehicle_model;
        document.getElementById('vehicle_model').value = model;
    }
    if (vehicleData.year || vehicleData.vehicle_year) {
        const year = vehicleData.year || vehicleData.vehicle_year;
        document.getElementById('vehicle_year').value = year;
    }
    if (vehicleData.color || vehicleData.vehicle_color) {
        const color = vehicleData.color || vehicleData.vehicle_color;
        document.getElementById('vehicle_color').value = color;
    }
    
    // Preencher dados da notificação
    if (notificationData.citation_number) {
        document.getElementById('citation_number').value = notificationData.citation_number;
    }
    if (notificationData.date) {
        document.getElementById('date').value = notificationData.date;
    }
    if (notificationData.time) {
        document.getElementById('time').value = notificationData.time;
    }
    if (notificationData.reason) {
        document.getElementById('reason').value = notificationData.reason;
        // Disparar detecção automática com o motivo preenchido pela extração
        try { detectInfractionType(notificationData.reason); } catch (e) { console.error(e); }
    }
    if (notificationData.amount) {
        document.getElementById('amount').value = notificationData.amount;
    }
    if (notificationData.location) {
        document.getElementById('location').value = notificationData.location;
    }
    
    console.log('✅ Formulário preenchido com sucesso!');
}

// Funções de formatação
function formatCpf(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    input.value = value;
}

function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    input.value = value;
}

function formatPlate(input) {
    let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (value.length > 3) {
        value = value.substring(0, 3) + '-' + value.substring(3);
    }
    input.value = value;
}

// Detecção automática do tipo de infração
let detectionTimeout = null;

function detectInfractionType(reason) {
    console.log('🔍 Detectando tipo de infração:', reason);
    
    // Limpar timeout anterior
    if (detectionTimeout) {
        clearTimeout(detectionTimeout);
    }
    
    // Se o campo estiver vazio, limpar detecção
    if (!reason || reason.trim() === '') {
        clearDetection();
        return;
    }
    
    // Aguardar 500ms após o usuário parar de digitar
    detectionTimeout = setTimeout(() => {
        performDetection(reason);
    }, 500);
}

function performDetection(reason) {
    console.log('🚀 Executando detecção:', reason);
    
    const statusDiv = document.getElementById('detection-status');
    statusDiv.classList.remove('hidden');
    
    // Mostrar loading
    statusDiv.innerHTML = `
        <div class="flex items-center text-blue-600">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-2"></div>
            <span>🤖 Detectando tipo de infração automaticamente...</span>
        </div>
    `;
    
    // Coletar dados da multa
    const multaData = {
        location: document.getElementById('location')?.value || '',
        date: document.getElementById('date')?.value || '',
        time: document.getElementById('time')?.value || '',
        amount: document.getElementById('amount')?.value || '',
        citation_number: document.getElementById('citation_number')?.value || '',
        vehicle_plate: document.getElementById('plate')?.value || ''
    };
    
    // Verificar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('❌ CSRF token não encontrado');
        statusDiv.innerHTML = `
            <div class="text-red-600">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Erro: CSRF token não encontrado
            </div>
        `;
        return;
    }
    
    // Fazer requisição para a API
    fetch('/infractions/detect-type', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            reason: reason,
            multa_data: multaData
        })
    })
    .then(response => {
        console.log('📡 Resposta da API:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📋 Dados recebidos:', data);
        
        if (data.success && data.detected_type) {
            console.log('✅ Tipo detectado:', data.detected_type);
            selectDetectedType(data.detected_type);
        } else {
            console.log('⚠️ Nenhum tipo detectado');
            statusDiv.innerHTML = `
                <div class="text-yellow-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Não foi possível detectar automaticamente. Selecione manualmente.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('❌ Erro na detecção:', error);
        statusDiv.innerHTML = `
            <div class="text-red-600">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Erro na detecção automática: ${error.message}
            </div>
        `;
    });
}

function selectDetectedType(detectedType) {
    console.log('🎯 Selecionando tipo detectado:', detectedType);
    
    const select = document.getElementById('infraction_type_id');
    const statusDiv = document.getElementById('detection-status');
    
    if (!select) {
        console.error('❌ Select não encontrado');
        return;
    }
    
    // Verificar se a opção existe
    const optionExists = Array.from(select.options).some(option => option.value == detectedType.id);
    console.log('✅ Opção existe no select:', optionExists);
    
    if (!optionExists) {
        console.error('❌ Opção não encontrada no select:', detectedType.id);
        
        // Tentar encontrar por código
        const optionByCode = Array.from(select.options).find(option => 
            option.getAttribute('data-code') === detectedType.code
        );
        
        if (optionByCode) {
            console.log('✅ Encontrado por código:', detectedType.code);
            detectedType.id = optionByCode.value;
        } else {
            statusDiv.innerHTML = `
                <div class="text-red-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Tipo detectado não encontrado no sistema: ${detectedType.code}
                </div>
            `;
            return;
        }
    }
    
    // Selecionar a opção
    select.value = detectedType.id;
    select.dispatchEvent(new Event('change', { bubbles: true }));
    
    // Mostrar confirmação
    statusDiv.innerHTML = `
        <div class="text-green-600">
            <i class="fas fa-check-circle mr-1"></i>
            Tipo detectado: <strong>${detectedType.code} - ${detectedType.description}</strong>
            <button onclick="clearDetection()" class="ml-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    console.log('✅ Tipo de infração selecionado automaticamente');

    // Mostrar badge de detecção automática
    const badge = document.getElementById('infraction-detected-badge');
    if (badge) badge.classList.remove('hidden');
}

function clearDetection() {
    const statusDiv = document.getElementById('detection-status');
    statusDiv.classList.add('hidden');
    statusDiv.innerHTML = '';
    const badge = document.getElementById('infraction-detected-badge');
    if (badge) badge.classList.add('hidden');
}

// Funções para busca de dados do veículo
function formatPlateSearch(input) {
    let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    
    // Formatar baseado no comprimento
    if (value.length > 3) {
        // Formato Mercosul: AAA0A00
        if (value.length >= 7) {
            value = value.substring(0, 3) + '-' + value.substring(3, 4) + value.substring(4, 5) + value.substring(5, 7);
        } else {
            // Formato antigo: AAA0000
            value = value.substring(0, 3) + '-' + value.substring(3);
        }
    }
    
    input.value = value;
}

function searchVehicleData() {
    const plate = document.getElementById('plateSearch').value.trim().toUpperCase();
    
    if (!plate) {
        alert('Por favor, digite a placa do veículo.');
        return;
    }
    
    // Validar formato da placa (aceita Mercosul e formato antigo)
    const placaRegex = /^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$|^[A-Z]{3}[0-9]{4}$/;
    const plateClean = plate.replace(/[^A-Z0-9]/g, ''); // Remove hífens e outros caracteres
    
    if (!placaRegex.test(plateClean)) {
        alert('Formato de placa inválido. Use AAA0000 (antigo) ou AAA0A00 (Mercosul).');
        return;
    }
    
    // Mostrar loading
    const btnSearch = document.getElementById('btnSearchVehicle');
    const originalText = btnSearch.innerHTML;
    btnSearch.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';
    btnSearch.disabled = true;
    
    const statusDiv = document.getElementById('vehicle-search-status');
    statusDiv.classList.remove('hidden');
    statusDiv.innerHTML = `
        <div class="flex items-center text-blue-600">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-2"></div>
            <span>Buscando dados do veículo...</span>
        </div>
    `;
    
    // Verificar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('❌ CSRF token não encontrado');
        return;
    }
    
    // Fazer requisição para a API
    fetch('/api/vehicle-data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            placa: plate.replace(/[^A-Z0-9]/g, '') // Remove hífens e outros caracteres
        })
    })
    .then(response => {
        console.log('📡 Status da resposta:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📋 Dados do veículo:', data);
        
        if (data.success && data.data) {
            // Preencher os campos do formulário
            fillVehicleData(data.data);
            
            // Mostrar resultado
            showVehicleDataResult(data.data);
            
            // Preencher a placa no campo do formulário
            document.getElementById('plate').value = plate;
            
            alert('✅ Dados do veículo encontrados e preenchidos!');
        } else {
            throw new Error(data.message || 'Erro ao buscar dados do veículo');
        }
    })
    .catch(error => {
        console.error('❌ Erro na busca:', error);
        statusDiv.innerHTML = `
            <div class="text-red-600">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Erro ao buscar dados: ${error.message}
            </div>
        `;
    })
    .finally(() => {
        btnSearch.innerHTML = originalText;
        btnSearch.disabled = false;
    });
}

function fillVehicleData(data) {
    console.log('📝 Preenchendo dados do veículo:', data);
    
    if (data.modelo) {
        document.getElementById('vehicle_model').value = data.modelo;
    }
    if (data.ano) {
        document.getElementById('vehicle_year').value = data.ano;
    }
    if (data.cor) {
        document.getElementById('vehicle_color').value = data.cor;
    }
    if (data.marca) {
        // Se não houver campo marca, adicionar ao modelo
        const modelo = document.getElementById('vehicle_model').value;
        if (modelo && data.marca) {
            document.getElementById('vehicle_model').value = `${data.marca} ${modelo}`;
        }
    }
    
    console.log('✅ Dados do veículo preenchidos');
}

function showVehicleDataResult(data) {
    const resultDiv = document.getElementById('vehicle-data-result');
    const modelResult = document.getElementById('vehicle-model-result');
    const yearResult = document.getElementById('vehicle-year-result');
    const colorResult = document.getElementById('vehicle-color-result');
    const brandResult = document.getElementById('vehicle-brand-result');
    
    modelResult.textContent = data.modelo || 'N/A';
    yearResult.textContent = data.ano || 'N/A';
    colorResult.textContent = data.cor || 'N/A';
    brandResult.textContent = data.marca || 'N/A';
    
    resultDiv.classList.remove('hidden');
}

// Função para carregar justificativas
function loadJustifications(infractionTypeId) {
    console.log('🎯 Carregando justificativas para tipo:', infractionTypeId);
    
    if (!infractionTypeId) {
        console.log('❌ Nenhum tipo selecionado');
        return;
    }
    
    // Coletar dados da multa
    const multaData = {
        location: document.getElementById('location')?.value || '',
        date: document.getElementById('date')?.value || '',
        time: document.getElementById('time')?.value || '',
        amount: document.getElementById('amount')?.value || '',
        citation_number: document.getElementById('citation_number')?.value || '',
        vehicle_plate: document.getElementById('plate')?.value || ''
    };
    
    // Verificar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('❌ CSRF token não encontrado');
        return;
    }
    
    // Mostrar loading
    const step4Content = document.querySelector('#step-4 .bg-white');
    step4Content.innerHTML = `
        <h3 class="text-2xl font-bold text-gray-900 mb-6">🎯 Argumentos Jurídicos</h3>
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <p class="text-gray-600">Carregando estratégias de defesa...</p>
        </div>
    `;
    
    // Fazer requisição para a API
    fetch('/infractions/justifications/contextualized', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            infraction_type_id: infractionTypeId,
            multa_data: multaData
        })
    })
    .then(response => {
        console.log('📡 Status da resposta:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📋 Dados recebidos:', data);
        
        if (data.success && data.justifications) {
            displayJustifications(data.justifications, data.infraction);
        } else {
            throw new Error(data.message || 'Erro ao carregar justificativas');
        }
    })
    .catch(error => {
        console.error('❌ Erro ao carregar justificativas:', error);
        step4Content.innerHTML = `
            <h3 class="text-2xl font-bold text-gray-900 mb-6">🎯 Argumentos Jurídicos</h3>
            <div class="text-center py-8">
                <div class="text-red-600 mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl"></i>
                </div>
                <p class="text-red-600">Erro ao carregar estratégias de defesa</p>
                <p class="text-gray-500 text-sm mt-2">${error.message}</p>
                <button onclick="loadJustifications('${infractionTypeId}')" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    🔄 Tentar Novamente
                </button>
            </div>
        `;
    });
}

function displayJustifications(justifications, infraction) {
    console.log('📝 Exibindo justificativas:', justifications);
    
    const step4Content = document.querySelector('#step-4 .bg-white');
    
    let html = `
        <h3 class="text-2xl font-bold text-gray-900 mb-6">🎯 Argumentos Jurídicos</h3>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h4 class="font-semibold text-blue-900 mb-2">Infração Selecionada</h4>
            <p class="text-blue-700"><strong>${infraction.code}</strong> - ${infraction.description}</p>
            <p class="text-blue-600 text-sm">Artigo: ${infraction.article} | Pontos: ${infraction.points} | Valor: R$ ${infraction.amount}</p>
        </div>
    `;
    
    // Verificar se há estratégias de defesa
    if (justifications.estrategias_defesa && Array.isArray(justifications.estrategias_defesa)) {
        html += `<div class="space-y-4 mb-6">`;
        
        justifications.estrategias_defesa.forEach((estrategia, index) => {
            // Extrair informações da estratégia
            const nome = estrategia.nome || `Estratégia ${index + 1}`;
            const descricao = estrategia.descricao || 'Descrição não disponível';
            const relevancia = estrategia.relevancia_contexto || estrategia.relevancia || 'Alta';
            
            // Extrair argumentos se disponível
            let argumentosHtml = '';
            if (estrategia.argumentos && Array.isArray(estrategia.argumentos)) {
                estrategia.argumentos.forEach((arg, argIndex) => {
                    argumentosHtml += `
                        <div class="mb-3 p-3 bg-gray-50 rounded">
                            <h6 class="font-medium text-gray-800 mb-1">${arg.titulo || `Argumento ${argIndex + 1}`}</h6>
                            <p class="text-gray-700 text-sm mb-2">${arg.descricao || 'Descrição não disponível'}</p>
                            <p class="text-gray-600 text-xs"><strong>Fundamentação:</strong> ${arg.fundamentacao || 'Não informado pela IA'}</p>
                        </div>
                    `;
                });
            }
            
            // Extrair vantagens e desvantagens
            let vantagensHtml = '';
            if (estrategia.vantagens && Array.isArray(estrategia.vantagens)) {
                vantagensHtml = `
                    <div class="mb-3">
                        <h6 class="font-medium text-green-800 mb-1">✅ Vantagens:</h6>
                        <ul class="list-disc list-inside text-sm text-green-700">
                            ${estrategia.vantagens.map(v => `<li>${v}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            let desvantagensHtml = '';
            if (estrategia.desvantagens && Array.isArray(estrategia.desvantagens)) {
                desvantagensHtml = `
                    <div class="mb-3">
                        <h6 class="font-medium text-red-800 mb-1">⚠️ Desvantagens:</h6>
                        <ul class="list-disc list-inside text-sm text-red-700">
                            ${estrategia.desvantagens.map(d => `<li>${d}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            html += `
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">${nome}</h4>
                        <span class="text-sm text-gray-500">Relevância: ${relevancia}</span>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-800 mb-2">Descrição:</h5>
                        <p class="text-gray-700 leading-relaxed">${descricao}</p>
                    </div>
                    
                    ${argumentosHtml}
                    ${vantagensHtml}
                    ${desvantagensHtml}
                    
                    ${estrategia.recomendacao ? `
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                            <h6 class="font-medium text-blue-800 mb-1">💡 Recomendação:</h6>
                            <p class="text-blue-700 text-sm">${estrategia.recomendacao}</p>
                        </div>
                    ` : ''}
                    
                    <div class="flex justify-end">
                        <button onclick="selectJustification(${index})" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            ✅ Selecionar Estratégia
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
    }
    
    // Adicionar contexto específico se disponível
    if (justifications.contexto_especifico) {
        html += `
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-900 mb-3">📋 Análise do Contexto</h4>
                <div class="space-y-2 text-sm">
                    ${justifications.contexto_especifico.local_analise ? `<p><strong>Local:</strong> ${justifications.contexto_especifico.local_analise}</p>` : ''}
                    ${justifications.contexto_especifico.temporal_analise ? `<p><strong>Temporal:</strong> ${justifications.contexto_especifico.temporal_analise}</p>` : ''}
                    ${justifications.contexto_especifico.circunstancial_analise ? `<p><strong>Circunstancial:</strong> ${justifications.contexto_especifico.circunstancial_analise}</p>` : ''}
                </div>
            </div>
        `;
    }
    
    // Adicionar artigos relevantes se disponível
    if (justifications.artigos_relevantes && Array.isArray(justifications.artigos_relevantes)) {
        html += `
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-purple-900 mb-2">📚 Artigos Relevantes</h4>
                <ul class="list-disc list-inside space-y-1 text-sm text-purple-800">
                    ${justifications.artigos_relevantes.map(artigo => `<li>${artigo}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Adicionar dicas de evidências se disponível
    if (justifications.dicas_evidencias && Array.isArray(justifications.dicas_evidencias)) {
        html += `
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-green-900 mb-2">🔍 Dicas de Evidências</h4>
                <ul class="list-disc list-inside space-y-1 text-sm text-green-800">
                    ${justifications.dicas_evidencias.map(dica => `<li>${dica}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    // Adicionar instruções de escolha se disponível
    if (justifications.instrucoes_escolha) {
        html += `
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-semibold text-yellow-900 mb-2">💡 Instruções de Escolha</h4>
                <p class="text-yellow-800">${justifications.instrucoes_escolha}</p>
            </div>
        `;
    } else {
        html += `
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-semibold text-yellow-900 mb-2">💡 Dica</h4>
                <p class="text-yellow-800">Selecione a estratégia que melhor se adequa ao seu caso. Você pode personalizar o argumento posteriormente.</p>
            </div>
        `;
    }
    
    step4Content.innerHTML = html;
}

function selectJustification(index) {
    console.log('✅ Estratégia selecionada:', index);
    
    // Salvar a estratégia selecionada
    selectedJustification = index;
    
    // Mostrar confirmação
    const step4Content = document.querySelector('#step-4 .bg-white');
    const selectedCard = step4Content.querySelectorAll('.border')[index];
    selectedCard.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
    
    // Avançar para o próximo passo
    nextStep();
}

function generateAppeal() {
    console.log('🚀 Gerando recurso...');
    
    // Validação prévia dos campos obrigatórios
    const requiredFields = [
        { id: 'name', label: 'Nome' },
        { id: 'cpf', label: 'CPF' },
        { id: 'driver_license', label: 'CNH' },
        { id: 'plate', label: 'Placa do Veículo' },
        { id: 'vehicle_model', label: 'Modelo do Veículo' },
        { id: 'vehicle_year', label: 'Ano do Veículo' },
        { id: 'vehicle_color', label: 'Cor do Veículo' },
        { id: 'citation_number', label: 'Número da Autuação' },
        { id: 'date', label: 'Data da Infração' },
        { id: 'time', label: 'Horário da Infração' },
        { id: 'amount', label: 'Valor da Multa' },
        { id: 'location', label: 'Local da Infração' },
        { id: 'reason', label: 'Motivo da Infração' }
    ];
    
    const missingFields = [];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element && (!element.value || element.value.trim() === '')) {
            missingFields.push(field.label);
            highlightField(field.id, 'error');
        }
    });
    
    if (missingFields.length > 0) {
        // Criar modal de erro para campos faltantes
        const errorModal = document.createElement('div');
        errorModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        errorModal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                <div class="flex items-center mb-4">
                    <div class="text-red-500 text-2xl mr-3">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Campos Obrigatórios</h3>
                </div>
                <div class="mb-4">
                    <p class="text-gray-700 mb-3">Por favor, preencha os seguintes campos antes de gerar o recurso:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                        ${missingFields.map(field => `<li>${field}</li>`).join('')}
                    </ul>
                </div>
                <div class="flex justify-end">
                    <button onclick="this.closest('.fixed').remove()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        OK
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(errorModal);
        return;
    }
    
    // Mostrar loading com texto mais informativo
    const btnSubmit = document.getElementById('btnSubmit');
    const originalText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando recurso...';
    btnSubmit.disabled = true;
    
    // Coletar dados do formulário
    const formData = new FormData(document.getElementById('appealForm'));
    
    // Adicionar estratégia selecionada
    if (selectedJustification !== null) {
        formData.append('selected_justification', selectedJustification);
    }
    
    // Verificar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('❌ CSRF token não encontrado');
        return;
    }
    
    // Criar overlay de loading mais detalhado
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    loadingOverlay.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="text-blue-500 text-4xl mb-4">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Criando seu Recurso</h3>
            <p class="text-gray-600 mb-4">Estamos processando suas informações...</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 60%"></div>
            </div>
            <p class="text-sm text-gray-500">Isso pode levar alguns segundos</p>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
    
    // Fazer requisição para gerar o recurso
    fetch('/appeals/create-new', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        console.log('📡 Status da resposta:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📋 Dados recebidos:', data);
        
        if (data.success) {
            generatedAppealId = data.appeal_id;
            
            // Preencher detalhes do recurso
            document.getElementById('appeal-number').textContent = data.appeal_number || 'N/A';
            document.getElementById('appeal-date').textContent = new Date().toLocaleDateString('pt-BR');
            document.getElementById('appeal-infraction').textContent = data.infraction_type || 'N/A';
            
            // Mostrar tela de sucesso
            showStep(6);
            
            // Mostrar notificação de sucesso
            const successModal = document.createElement('div');
            successModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            successModal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                    <div class="flex items-center mb-4">
                        <div class="text-green-500 text-2xl mr-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Recurso Criado com Sucesso!</h3>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-700 mb-3">Seu recurso foi gerado e está pronto para download.</p>
                        <p class="text-sm text-gray-500">Você pode baixar o documento em PDF ou DOC.</p>
                    </div>
                    <div class="flex justify-end">
                        <button onclick="this.closest('.fixed').remove()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            Continuar
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(successModal);
        } else {
            // Tratar erros de validação
            if (data.errors) {
                let errorMessage = data.message + '\n\n';
                for (const field in data.errors) {
                    errorMessage += `• ${data.errors[field].join(', ')}\n`;
                }
                throw new Error(errorMessage);
            } else {
                throw new Error(data.message || 'Erro ao gerar recurso');
            }
        }
    })
    .catch(error => {
        console.error('❌ Erro ao gerar recurso:', error);
        
        // Criar modal de erro mais amigável
        const errorModal = document.createElement('div');
        errorModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        errorModal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                <div class="flex items-center mb-4">
                    <div class="text-red-500 text-2xl mr-3">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Erro ao Gerar Recurso</h3>
                </div>
                <div class="mb-4">
                    <p class="text-gray-700 mb-3">${error.message}</p>
                    <p class="text-sm text-gray-500">Por favor, corrija os campos destacados e tente novamente.</p>
                </div>
                <div class="flex justify-end">
                    <button onclick="this.closest('.fixed').remove()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        OK
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(errorModal);
        
        // Erros destacados são tratados por displayValidationErrors
    })
    .finally(() => {
        // Remover overlay de loading
        const loadingOverlay = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-75');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
        
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
    });
}

function highlightField(fieldId, type) {
    const field = document.getElementById(fieldId);
    if (field) {
        if (type === 'error') {
            field.classList.add('border-red-500', 'bg-red-50');
            field.classList.remove('border-gray-300', 'bg-white');
            
            // Remover destaque após 5 segundos
            setTimeout(() => {
                field.classList.remove('border-red-500', 'bg-red-50');
                field.classList.add('border-gray-300', 'bg-white');
            }, 5000);
        }
    }
}

function ensureErrorPlaceholder(fieldId) {
    const input = document.getElementById(fieldId);
    if (!input) return null;
    let holder = document.getElementById(fieldId + '-error');
    if (!holder) {
        holder = document.createElement('div');
        holder.id = fieldId + '-error';
        holder.className = 'mt-1 text-xs text-red-600';
        input.closest('div').appendChild(holder);
    }
    return holder;
}

function displayValidationErrors(errors) {
    // Limpar anteriores e aplicar mensagens
    Object.keys(errors).forEach(key => {
        const fieldId = mapFieldKeyToId(key);
        if (!fieldId) return;
        highlightField(fieldId, 'error');
        const holder = ensureErrorPlaceholder(fieldId);
        if (holder) holder.textContent = errors[key].join(', ');
    });
}

function mapFieldKeyToId(key) {
    const map = {
        name: 'name',
        cpf: 'cpf',
        driver_license: 'driver_license',
        phone: 'phone',
        plate: 'plate',
        vehicle_model: 'vehicle_model',
        vehicle_year: 'vehicle_year',
        vehicle_color: 'vehicle_color',
        citation_number: 'citation_number',
        date: 'date',
        time: 'time',
        amount: 'amount',
        location: 'location',
        reason: 'reason',
        infraction_type_id: 'infraction_type_id',
    };
    return map[key] || null;
}

function populateReviewSummary() {
    const grid = document.getElementById('review-summary-grid');
    if (!grid) return;
    const entries = [
        ['Nome', document.getElementById('name')?.value],
        ['CPF', document.getElementById('cpf')?.value],
        ['CNH', document.getElementById('driver_license')?.value],
        ['Telefone', document.getElementById('phone')?.value || 'Não informado'],
        ['Placa', document.getElementById('plate')?.value],
        ['Modelo', document.getElementById('vehicle_model')?.value],
        ['Ano', document.getElementById('vehicle_year')?.value],
        ['Cor', document.getElementById('vehicle_color')?.value],
        ['Autuação', document.getElementById('citation_number')?.value],
        ['Data', document.getElementById('date')?.value],
        ['Horário', document.getElementById('time')?.value],
        ['Local', document.getElementById('location')?.value],
        ['Valor', document.getElementById('amount')?.value],
        ['Motivo', document.getElementById('reason')?.value],
    ];
    grid.innerHTML = entries.map(([label, value]) => `
        <div class="p-3 bg-white border rounded">
            <div class="text-xs text-gray-500">${label}</div>
            <div class="text-sm font-medium text-gray-900 break-words">${(value || '—')}</div>
        </div>
    `).join('');
}

function copyToClipboard(text) {
    try {
        navigator.clipboard.writeText(text);
        alert('Argumento copiado para a área de transferência.');
    } catch (e) {
        console.error(e);
    }
}

function downloadAppeal(format) {
    console.log('📥 Baixando recurso em formato:', format);
    
    if (!generatedAppealId) {
        alert('❌ Nenhum recurso gerado para download');
        return;
    }
    
    // Mostrar loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Baixando...';
    btn.disabled = true;
    
    // Fazer requisição para download
    fetch(`/appeals/${generatedAppealId}/download/${format}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.blob();
    })
    .then(blob => {
        // Criar link para download
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `recurso_${generatedAppealId}.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        console.log('✅ Download concluído');
    })
    .catch(error => {
        console.error('❌ Erro no download:', error);
        alert(`Erro no download: ${error.message}`);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
@endsection 