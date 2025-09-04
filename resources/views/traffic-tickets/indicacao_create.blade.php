@extends('layouts.app')

@section('content')
<style>
    .file-upload-label:hover .file-upload-label-content {
        transform: translateY(-2px);
    }
    
    .file-upload-label-content {
        transition: all 0.3s ease;
    }
    
    .progress-animation {
        animation: progress-pulse 2s ease-in-out infinite;
    }
    
    @keyframes progress-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .upload-zone {
        transition: all 0.3s ease;
        border: 2px dashed #d1d5db;
    }
    
    .upload-zone:hover {
        border-color: #6366f1;
        background-color: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .upload-zone.dragover {
        border-color: #6366f1;
        background-color: #eef2ff;
        transform: scale(1.02);
    }
    
    .file-preview {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .btn-pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(99, 102, 241, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
        }
    }
    
    .step-indicator {
        position: relative;
    }
    
    .step-indicator::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -20px;
        width: 40px;
        height: 2px;
        background: linear-gradient(90deg, #e5e7eb 0%, #d1d5db 100%);
        transform: translateY(-50%);
    }
    
    .step-indicator:last-child::after {
        display: none;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin: 0 auto 12px;
        transition: all 0.3s ease;
    }
    
    .step-number.active {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
    }
    
    .step-number.completed {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    
    .step-number.pending {
        background: #f3f4f6;
        color: #9ca3af;
        border: 2px solid #e5e7eb;
    }
    
    .upload-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .upload-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .upload-card.uploaded {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    }
    
    .upload-card.error {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2, #fef2f2);
    }
    
    .file-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-top: 12px;
        font-size: 14px;
        color: #374151;
    }
    
    .file-size {
        color: #6b7280;
        font-size: 12px;
    }
    
    .file-type {
        color: #6366f1;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .remove-file {
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s ease;
    }
    
    .remove-file:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    
    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 16px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 3px;
        transition: width 0.3s ease;
        width: 0%;
    }
    
    .success-animation {
        animation: successBounce 0.6s ease-out;
    }
    
    @keyframes successBounce {
        0%, 20%, 53%, 80%, 100% {
            transform: translate3d(0,0,0);
        }
        40%, 43% {
            transform: translate3d(0, -30px, 0);
        }
        70% {
            transform: translate3d(0, -15px, 0);
        }
        90% {
            transform: translate3d(0, -4px, 0);
        }
    }
    
    .file-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .file-type-badge.pdf {
        background: #fef2f2;
        color: #dc2626;
    }
    
    .file-type-badge.image {
        background: #f0fdf4;
        color: #059669;
    }
</style>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header com instruções -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full mb-4">
            <i class="fas fa-user-check text-2xl text-indigo-600"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Indicação de Condutor</h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Gere automaticamente o formulário de indicação de condutor com base nos documentos enviados. 
            O sistema extrai as informações via IA e monta um PDF completo.
        </p>
        
        <!-- Status do suporte a PDFs -->
        <div class="mt-4 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium" id="pdf-support-status">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Verificando suporte a PDFs...
        </div>
    </div>

    <!-- Indicador de progresso -->
    <div class="flex justify-center mb-8">
        <div class="flex items-center space-x-8">
            <div class="step-indicator text-center">
                <div class="step-number active" id="step-1">1</div>
                <div class="text-sm font-medium text-gray-700">Upload</div>
            </div>
            <div class="step-indicator text-center">
                <div class="step-number pending" id="step-2">2</div>
                <div class="text-sm font-medium text-gray-700">Processamento</div>
            </div>
            <div class="step-indicator text-center">
                <div class="step-number pending" id="step-3">3</div>
                <div class="text-sm font-medium text-gray-700">Download</div>
            </div>
        </div>
    </div>

    <!-- Formulário principal -->
    <form id="indicacao-form" class="space-y-8" enctype="multipart/form-data">
        @csrf
        
        <!-- Grid de uploads -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Notificação da Multa -->
            <div class="upload-card" id="card-notificacao">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Notificação da Multa</h3>
                    <p class="text-sm text-gray-600">Auto de infração ou notificação</p>
                </div>
                
                <label class="file-upload-label block cursor-pointer">
                    <input type="file" name="foto_notificacao_multa" accept="image/*,.pdf" class="hidden file-upload-input" data-preview="notificacao-preview" data-card="card-notificacao">
                    <div class="upload-zone flex items-center justify-center p-8 rounded-xl cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                            <p class="text-sm font-medium text-gray-700 mb-1">Clique para selecionar</p>
                            <p class="text-xs text-gray-500">JPG, PNG, WEBP ou PDF</p>
                            <p class="text-xs text-gray-400 mt-2">ou arraste e solte aqui</p>
                            <p class="text-xs text-indigo-500 mt-1 font-medium">Máximo: 10MB</p>
                        </div>
                    </div>
                </label>
                
                <div id="notificacao-preview" class="hidden mt-4">
                    <div class="file-type-badge mb-2" id="notificacao-type"></div>
                    <img src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border-2 border-gray-200" id="notificacao-img">
                    <div class="file-info">
                        <span class="file-name"></span>
                        <div class="flex items-center space-x-2">
                            <span class="file-size"></span>
                            <button type="button" class="remove-file" onclick="removeFile('notificacao')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documento do Veículo -->
            <div class="upload-card" id="card-crlv">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-car text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Documento do Veículo</h3>
                    <p class="text-sm text-gray-600">CRLV ou CRLV-e</p>
                </div>
                
                <label class="file-upload-label block cursor-pointer">
                    <input type="file" name="foto_doc_carro" accept="image/*,.pdf" class="hidden file-upload-input" data-preview="crlv-preview" data-card="card-crlv">
                    <div class="upload-zone flex items-center justify-center p-8 rounded-xl cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                            <p class="text-sm font-medium text-gray-700 mb-1">Clique para selecionar</p>
                            <p class="text-xs text-gray-500">JPG, PNG, WEBP ou PDF</p>
                            <p class="text-xs text-gray-400 mt-2">ou arraste e solte aqui</p>
                            <p class="text-xs text-indigo-500 mt-1 font-medium">Máximo: 10MB</p>
                        </div>
                    </div>
                </label>
                
                <div id="crlv-preview" class="hidden mt-4">
                    <div class="file-type-badge mb-2" id="crlv-type"></div>
                    <img src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border-2 border-gray-200" id="crlv-img">
                    <div class="file-info">
                        <span class="file-name"></span>
                        <div class="flex items-center space-x-2">
                            <span class="file-size"></span>
                            <button type="button" class="remove-file" onclick="removeFile('crlv')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CNH do Condutor -->
            <div class="upload-card" id="card-cnh">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-id-card text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">CNH do Condutor</h3>
                    <p class="text-sm text-gray-600">Carteira Nacional de Habilitação</p>
                </div>
                
                <label class="file-upload-label block cursor-pointer">
                    <input type="file" name="foto_cnh_condutor" accept="image/*,.pdf" class="hidden file-upload-input" data-preview="cnh-preview" data-card="card-cnh">
                    <div class="upload-zone flex items-center justify-center p-8 rounded-xl cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                            <p class="text-sm font-medium text-gray-700 mb-1">Clique para selecionar</p>
                            <p class="text-xs text-gray-500">JPG, PNG, WEBP ou PDF</p>
                            <p class="text-xs text-gray-400 mt-2">ou arraste e solte aqui</p>
                            <p class="text-xs text-indigo-500 mt-1 font-medium">Máximo: 10MB</p>
                        </div>
                    </div>
                </label>
                
                <div id="cnh-preview" class="hidden mt-4">
                    <div class="file-type-badge mb-2" id="cnh-type"></div>
                    <img src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border-2 border-gray-200" id="cnh-img">
                    <div class="file-info">
                        <span class="file-name"></span>
                        <div class="flex items-center space-x-2">
                            <span class="file-size"></span>
                            <button type="button" class="remove-file" onclick="removeFile('cnh')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documento do Proprietário -->
            <div class="upload-card" id="card-proprietario">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Documento do Proprietário</h3>
                    <p class="text-sm text-gray-600">RG, CNH ou CPF</p>
                </div>
                
                <label class="file-upload-label block cursor-pointer">
                    <input type="file" name="foto_doc_proprietario" accept="image/*,.pdf" class="hidden file-upload-input" data-preview="proprietario-preview" data-card="card-proprietario">
                    <div class="upload-zone flex items-center justify-center p-8 rounded-xl cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                            <p class="text-sm font-medium text-gray-700 mb-1">Clique para selecionar</p>
                            <p class="text-xs text-gray-500">JPG, PNG, WEBP ou PDF</p>
                            <p class="text-xs text-gray-400 mt-2">ou arraste e solte aqui</p>
                            <p class="text-xs text-indigo-500 mt-1 font-medium">Máximo: 10MB</p>
                        </div>
                    </div>
                </label>
                
                <div id="proprietario-preview" class="hidden mt-4">
                    <div class="file-type-badge mb-2" id="proprietario-type"></div>
                    <img src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border-2 border-gray-200" id="proprietario-img">
                    <div class="file-info">
                        <span class="file-name"></span>
                        <div class="flex items-center space-x-2">
                            <span class="file-size"></span>
                            <button type="button" class="remove-file" onclick="removeFile('proprietario')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pessoa Jurídica -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-building text-amber-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-amber-900">Pessoa Jurídica</h3>
            </div>
            
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_pessoa_juridica" value="1" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"> 
                    <span class="ml-2 text-sm font-medium text-amber-800">Proprietário é uma Pessoa Jurídica</span>
                </label>
            </div>
            
            <div class="hidden mt-4" id="pj-doc-wrapper">
                <div class="upload-card" id="card-pj">
                    <div class="text-center mb-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-file-contract text-orange-600"></i>
                        </div>
                        <h4 class="text-md font-semibold text-gray-900">Documento de Representação</h4>
                        <p class="text-sm text-gray-600">Contrato Social, Procuração, etc.</p>
                    </div>
                    
                    <label class="file-upload-label block cursor-pointer">
                        <input type="file" name="foto_doc_representacao_pj" accept="image/*,.pdf" class="hidden file-upload-input" data-preview="pj-preview" data-card="card-pj">
                        <div class="upload-zone flex items-center justify-center p-6 rounded-xl cursor-pointer">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <p class="text-sm font-medium text-gray-700 mb-1">Clique para selecionar</p>
                                <p class="text-xs text-gray-500">JPG, PNG, WEBP ou PDF</p>
                            </div>
                        </div>
                    </label>
                    
                    <div id="pj-preview" class="hidden mt-4">
                        <div class="file-type-badge mb-2" id="pj-type"></div>
                        <img src="" alt="Preview" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200" id="pj-img">
                        <div class="file-info">
                            <span class="file-name"></span>
                            <div class="flex items-center space-x-2">
                                <span class="file-size"></span>
                                <button type="button" class="remove-file" onclick="removeFile('pj')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de progresso -->
        <div class="hidden" id="progress-container">
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Processando documentos...</h3>
                    <span class="text-sm text-gray-600" id="progress-text">0%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2" id="progress-status">Iniciando processamento...</p>
            </div>
        </div>

        <!-- Botões de ação -->
        <div class="flex items-center justify-center space-x-4 pt-6">
            <button type="button" id="btn-gerar" 
                    class="inline-flex items-center px-10 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-xl font-semibold text-white hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-200 shadow-lg btn-pulse">
                <i class="fas fa-file-pdf mr-3 text-xl"></i>
                <span class="btn-text text-lg">Gerar PDF de Indicação</span>
            </button>
            
            <a id="download-link" href="#" target="_blank" 
               class="hidden inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-xl font-semibold text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-download mr-2"></i>
                Abrir PDF Gerado
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar suporte a PDFs
    checkPdfSupport();
    
    // Controle da checkbox de Pessoa Jurídica
    const pjCheckbox = document.querySelector('input[name="is_pessoa_juridica"]');
    const pjWrapper = document.getElementById('pj-doc-wrapper');
    
    if (pjCheckbox) {
        pjCheckbox.addEventListener('change', () => {
            pjWrapper.classList.toggle('hidden', !pjCheckbox.checked);
        });
    }

    // Drag and drop para uploads
    const uploadZones = document.querySelectorAll('.upload-zone');
       
    uploadZones.forEach(zone => {
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        
        zone.addEventListener('dragleave', () => {
            zone.classList.remove('dragover');
        });
        
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                const input = zone.parentElement.querySelector('input[type="file"]');
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // Preview de imagens e PDFs
    const fileInputs = document.querySelectorAll('.file-upload-input');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const previewId = this.getAttribute('data-preview');
                const cardId = this.getAttribute('data-card');
                const preview = document.getElementById(previewId);
                const card = document.getElementById(cardId);
                
                if (preview && card) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgElement = preview.querySelector('img');
                        const typeBadge = preview.querySelector('.file-type-badge');
                        
                        // Determinar tipo de arquivo
                        const isPdf = file.type === 'application/pdf';
                        const fileType = isPdf ? 'PDF' : 'IMAGEM';
                        const badgeClass = isPdf ? 'pdf' : 'image';
                        
                        // Configurar badge de tipo
                        typeBadge.textContent = fileType;
                        typeBadge.className = `file-type-badge ${badgeClass} mb-2`;
                        
                        if (isPdf) {
                            // Para PDFs, mostrar ícone ou primeira página convertida
                            imgElement.src = '/vendor/dompdf/dompdf/lib/fonts/Helvetica.afm'; // Placeholder
                            imgElement.style.objectFit = 'contain';
                            imgElement.style.backgroundColor = '#f3f4f6';
                        } else {
                            // Para imagens, mostrar preview normal
                            imgElement.src = e.target.result;
                            imgElement.style.objectFit = 'cover';
                            imgElement.style.backgroundColor = 'transparent';
                        }
                        
                        preview.querySelector('.file-name').textContent = file.name;
                        preview.querySelector('.file-size').textContent = formatFileSize(file.size);
                        preview.classList.remove('hidden');
                        card.classList.add('uploaded');
                        
                        // Atualizar indicador de progresso
                        updateStepProgress();
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    });

    // Botão de geração
    const btn = document.getElementById('btn-gerar');
    btn.addEventListener('click', async () => {
        const formEl = document.getElementById('indicacao-form');
        const formData = new FormData(formEl);
        
        // Validar se todos os campos obrigatórios foram preenchidos
        const requiredInputs = formEl.querySelectorAll('input[type="file"]:not([name="foto_doc_representacao_pj"])');
        let isValid = true;
        
        requiredInputs.forEach(input => {
            if (!input.files || input.files.length === 0) {
                isValid = false;
                const card = document.getElementById(input.getAttribute('data-card'));
                card.classList.add('error');
                setTimeout(() => card.classList.remove('error'), 3000);
            }
        });
        
        if (!isValid) {
            alert('Por favor, selecione todos os documentos obrigatórios.');
            return;
        }
        
        try {
            // Atualizar UI para processamento
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i><span>Processando...</span>';
            updateStepProgress(2);
            
            // Mostrar barra de progresso
            document.getElementById('progress-container').classList.remove('hidden');
            simulateProgress();
            
            const resp = await fetch('{{ route('indicacao_condutor.generate') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const json = await resp.json();
            
            if (json.success) {
                // Sucesso
                updateStepProgress(3);
                document.getElementById('progress-container').classList.add('hidden');
                
                const downloadLink = document.getElementById('download-link');
                downloadLink.href = json.url;
                downloadLink.classList.remove('hidden');
                
                // Animação de sucesso
                btn.classList.add('success-animation');
                btn.innerHTML = '<i class="fas fa-check mr-3"></i><span>PDF Gerado!</span>';
                btn.classList.remove('btn-pulse');
                btn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-emerald-600');
                
                // Reset após 3 segundos
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-file-pdf mr-3 text-xl"></i><span class="btn-text text-lg">Gerar PDF de Indicação</span>';
                    btn.classList.remove('success-animation', 'bg-gradient-to-r', 'from-green-600', 'to-emerald-600');
                    btn.classList.add('btn-pulse');
                }, 3000);
                
            } else {
                throw new Error(json.error || 'Falha ao gerar PDF.');
            }
        } catch (e) {
            // Erro
            document.getElementById('progress-container').classList.add('hidden');
            alert('Erro: ' + e.message);
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-pdf mr-3 text-xl"></i><span class="btn-text text-lg">Gerar PDF de Indicação</span>';
            updateStepProgress(1);
        }
    });
});

// Função para verificar suporte a PDFs
async function checkPdfSupport() {
    const statusElement = document.getElementById('pdf-support-status');
    
    try {
        const response = await fetch('{{ route('indicacao_condutor.generate') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                test: true,
                foto_notificacao_multa: 'test.pdf',
                foto_doc_carro: 'test.jpg',
                foto_cnh_condutor: 'test.jpg',
                foto_doc_proprietario: 'test.jpg'
            })
        });
        
        // Se chegou até aqui, o endpoint está funcionando
        statusElement.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-2"></i>✅ Suporta imagens (JPG, PNG, WEBP) e PDFs';
        statusElement.className = 'mt-4 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800';
        
    } catch (error) {
        // Verificar se é erro de suporte a PDFs
        if (error.message.includes('PDFs não podem ser processados')) {
            statusElement.innerHTML = '<i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i>⚠️ Suporta apenas imagens (JPG, PNG, WEBP)';
            statusElement.className = 'mt-4 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-amber-100 text-amber-800';
            
            // Desabilitar upload de PDFs
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.accept = 'image/*';
            });
            
            // Atualizar mensagens
            document.querySelectorAll('.upload-zone .text-xs.text-gray-500').forEach(el => {
                el.textContent = 'JPG, PNG ou WEBP';
            });
        } else {
            statusElement.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-2"></i>✅ Suporta imagens (JPG, PNG, WEBP) e PDFs';
            statusElement.className = 'mt-4 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800';
        }
    }
}

// Funções auxiliares
function removeFile(type) {
    const input = document.querySelector(`input[name="foto_${type}"]`);
    const preview = document.getElementById(`${type}-preview`);
    const card = document.getElementById(`card-${type}`);
    
    input.value = '';
    preview.classList.add('hidden');
    card.classList.remove('uploaded');
    
    updateStepProgress();
}

function updateStepProgress(step = 1) {
    const steps = document.querySelectorAll('.step-number');
    
    steps.forEach((stepEl, index) => {
        stepEl.classList.remove('active', 'completed', 'pending');
        
        if (index + 1 < step) {
            stepEl.classList.add('completed');
        } else if (index + 1 === step) {
            stepEl.classList.add('active');
        } else {
            stepEl.classList.add('pending');
        }
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function simulateProgress() {
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');
    const progressStatus = document.getElementById('progress-status');
    
    const statuses = [
        'Analisando documentos...',
        'Convertendo PDFs (se houver)...',
        'Extraindo dados via IA...',
        'Montando formulário...',
        'Gerando PDF...',
        'Finalizando...'
    ];
    
    let progress = 0;
    let statusIndex = 0;
    
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 100) progress = 100;
        
        progressFill.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '%';
        
        if (statusIndex < statuses.length && progress > (statusIndex * 16)) {
            progressStatus.textContent = statuses[statusIndex];
            statusIndex++;
        }
        
        if (progress >= 100) {
            clearInterval(interval);
        }
    }, 200);
}
</script>
@endpush
@endsection


