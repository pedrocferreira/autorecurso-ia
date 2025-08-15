@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Cabeçalho -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🚗 Upload de Documentos de Veículo</h1>
            <p class="text-lg text-gray-600">Faça upload do CRLV-e, CRV ou outros documentos do veículo para extrair informações automaticamente</p>
        </div>

        <!-- Tipos de Documentos Suportados -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <div class="text-4xl mb-2">📄</div>
                <h3 class="font-semibold text-blue-900">CRLV-e</h3>
                <p class="text-sm text-blue-700">Licenciamento Anual</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <div class="text-4xl mb-2">📋</div>
                <h3 class="font-semibold text-green-900">CRV</h3>
                <p class="text-sm text-green-700">Certificado de Registro</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                <div class="text-4xl mb-2">📸</div>
                <h3 class="font-semibold text-purple-900">Imagens</h3>
                <p class="text-sm text-purple-700">Fotos dos documentos</p>
            </div>
        </div>

        <!-- Área de Upload -->
        <div class="border-2 border-dashed border-orange-300 rounded-lg p-8 text-center bg-orange-50">
            <div class="text-6xl mb-4">🚗</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Selecione o documento do veículo</h3>
            <p class="text-gray-600 mb-4">Arraste e solte o arquivo aqui ou clique para selecionar</p>
            
            <input type="file" 
                   id="vehicleFileInput" 
                   accept="image/*,.pdf" 
                   class="hidden">
            
            <button onclick="document.getElementById('vehicleFileInput').click()" 
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200">
                📁 Selecionar Arquivo
            </button>
            
            <div class="mt-4 text-sm text-gray-500">
                Formatos aceitos: PDF, JPG, PNG, JPEG (máx. 10MB)
            </div>
        </div>

        <!-- Status do Upload -->
        <div id="upload-status" class="hidden mt-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-blue-800">Processando documento...</span>
            </div>
        </div>

        <!-- Resultados -->
        <div id="results" class="mt-8 space-y-6"></div>

        <!-- Exemplo de Dados -->
        <div class="mt-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Dados que serão extraídos:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Informações do Veículo:</h4>
                    <ul class="space-y-1 text-gray-600">
                        <li>• Placa</li>
                        <li>• Modelo</li>
                        <li>• Cor</li>
                        <li>• Ano</li>
                        <li>• RENAVAM</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Informações do Proprietário:</h4>
                    <ul class="space-y-1 text-gray-600">
                        <li>• Nome</li>
                        <li>• CPF</li>
                        <li>• Endereço</li>
                        <li>• Telefone</li>
                        <li>• Email</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleFileInput = document.getElementById('vehicleFileInput');
    const uploadStatus = document.getElementById('upload-status');
    const results = document.getElementById('results');
    
    // Drag and Drop
    const dropZone = document.querySelector('.border-dashed');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        dropZone.classList.add('border-orange-500', 'bg-orange-100');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('border-orange-500', 'bg-orange-100');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            vehicleFileInput.files = files;
            processVehicleUpload(files[0]);
        }
    }
    
    // File Input Change
    vehicleFileInput.addEventListener('change', function(event) {
        if (event.target.files.length > 0) {
            processVehicleUpload(event.target.files[0]);
        }
    });
});

async function processVehicleUpload(file) {
    console.log('🚗 Processando documento do veículo:', file.name);
    
    // Validar arquivo
    if (file.size > 10 * 1024 * 1024) {
        showError('Arquivo muito grande. Máximo permitido: 10MB');
        return;
    }
    
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        showError('Tipo de arquivo não suportado. Use PDF, JPG ou PNG.');
        return;
    }
    
    // Mostrar status
    showUploadStatus();
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'vehicle');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch('/extract-document-data', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        console.log('📋 Resultado:', result);
        
        if (result.success) {
            showSuccess(result.data);
        } else {
            showError(result.message || 'Erro ao processar documento');
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        showError('Erro de conexão. Tente novamente.');
    } finally {
        hideUploadStatus();
    }
}

function showUploadStatus() {
    document.getElementById('upload-status').classList.remove('hidden');
}

function hideUploadStatus() {
    document.getElementById('upload-status').classList.add('hidden');
}

function showSuccess(data) {
    const results = document.getElementById('results');
    
    results.innerHTML = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">✅</div>
                <h3 class="text-lg font-semibold text-green-900">Documento processado com sucesso!</h3>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informações do Veículo -->
                <div class="bg-white p-4 rounded-lg border">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        🚗 Informações do Veículo
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Placa:</span>
                            <span class="font-medium">${data.vehicle?.plate || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Modelo:</span>
                            <span class="font-medium">${data.vehicle?.model || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cor:</span>
                            <span class="font-medium">${data.vehicle?.color || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ano:</span>
                            <span class="font-medium">${data.vehicle?.year || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">RENAVAM:</span>
                            <span class="font-medium">${data.vehicle?.renavam || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">UF:</span>
                            <span class="font-medium">${data.vehicle?.uf || 'N/A'}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Informações do Proprietário -->
                <div class="bg-white p-4 rounded-lg border">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        👤 Informações do Proprietário
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nome:</span>
                            <span class="font-medium">${data.vehicle?.owner_name || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">CPF:</span>
                            <span class="font-medium">${data.vehicle?.owner_cpf || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Telefone:</span>
                            <span class="font-medium">${data.vehicle?.owner_phone || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">${data.vehicle?.owner_email || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dados Técnicos -->
            <div class="mt-4 p-3 bg-gray-50 rounded border">
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Tipo de Documento: ${data.vehicle?.document_type || 'N/A'}</span>
                    <span>Método: ${data.vehicle?.extraction_method || 'N/A'}</span>
                    <span>Confiança: ${Math.round((data.vehicle?.confidence || 0) * 100)}%</span>
                </div>
            </div>
            
            <!-- Botão para ver dados completos -->
            <details class="mt-4">
                <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                    📊 Ver dados completos em JSON
                </summary>
                <pre class="text-xs mt-2 p-3 bg-gray-100 rounded overflow-x-auto">${JSON.stringify(data, null, 2)}</pre>
            </details>
        </div>
    `;
}

function showError(message) {
    const results = document.getElementById('results');
    
    results.innerHTML = `
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="text-2xl mr-3">❌</div>
                <div>
                    <h3 class="text-lg font-semibold text-red-900">Erro ao processar documento</h3>
                    <p class="text-red-700 mt-1">${message}</p>
                </div>
            </div>
        </div>
    `;
}
</script>
@endsection
