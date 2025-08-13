@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Teste Upload Simplificado</h1>
        
        <div class="space-y-6">
            <!-- Upload CNH -->
            <div class="border-2 border-dashed border-green-300 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">📄 Upload CNH</h3>
                <input type="file" id="cnhFileInput" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <div id="cnh-status" class="mt-2 text-sm text-gray-600 hidden">
                    Processando CNH...
                </div>
            </div>
            
            <!-- Upload Notificação -->
            <div class="border-2 border-dashed border-blue-300 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">📋 Upload Notificação</h3>
                <input type="file" id="notificationFileInput" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <div id="notification-status" class="mt-2 text-sm text-gray-600 hidden">
                    Processando notificação...
                </div>
            </div>
            
            <!-- Botões de Teste -->
            <div class="space-y-2">
                <button onclick="testUpload()" class="bg-purple-500 text-white px-4 py-2 rounded">
                    📤 Teste Upload
                </button>
                <button onclick="testAPI()" class="bg-orange-500 text-white px-4 py-2 rounded">
                    🌐 Teste API
                </button>
            </div>
            
            <!-- Resultado -->
            <div id="result" class="mt-4 p-4 bg-gray-100 rounded"></div>
        </div>
    </div>
</div>

<script>
console.log('🚀 Script simplificado carregado!');

// Variáveis globais
let currentStep = 0;

// Função de teste de upload
function testUpload() {
    console.log('🧪 Testando upload...');
    
    const cnhFileInput = document.getElementById('cnhFileInput');
    const notificationFileInput = document.getElementById('notificationFileInput');
    
    console.log('🔍 Elementos encontrados:');
    console.log('- cnhFileInput:', !!cnhFileInput);
    console.log('- notificationFileInput:', !!notificationFileInput);
    
    if (cnhFileInput && notificationFileInput) {
        console.log('✅ Elementos de upload encontrados');
        alert('✅ Elementos de upload encontrados!');
    } else {
        console.error('❌ Elementos de upload não encontrados');
        alert('❌ Elementos de upload não encontrados');
    }
}

// Função de teste de API
function testAPI() {
    console.log('🌐 Testando API...');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('🔑 CSRF Token:', csrfToken ? 'Encontrado' : 'NÃO ENCONTRADO');
    
    if (!csrfToken) {
        alert('❌ CSRF token não encontrado');
        return;
    }
    
    fetch('/extract-document-data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            type: 'cnh'
        })
    })
    .then(response => {
        console.log('📡 Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📋 Resposta:', data);
        document.getElementById('result').innerHTML = `
            <p class="text-green-600">✅ API funcionando!</p>
            <pre class="text-sm">${JSON.stringify(data, null, 2)}</pre>
        `;
    })
    .catch(error => {
        console.error('❌ Erro:', error);
        document.getElementById('result').innerHTML = `
            <p class="text-red-600">❌ Erro na API: ${error.message}</p>
        `;
    });
}

// Configurar event listeners quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM carregado!');
    
    const cnhFileInput = document.getElementById('cnhFileInput');
    const notificationFileInput = document.getElementById('notificationFileInput');
    
    if (cnhFileInput) {
        cnhFileInput.addEventListener('change', function(event) {
            console.log('📄 CNH selecionada:', event.target.files[0]);
            if (event.target.files.length > 0) {
                processCnhUpload(event.target.files[0]);
            }
        });
        console.log('✅ Event listener CNH configurado');
    }
    
    if (notificationFileInput) {
        notificationFileInput.addEventListener('change', function(event) {
            console.log('📄 Notificação selecionada:', event.target.files[0]);
            if (event.target.files.length > 0) {
                processNotificationUpload(event.target.files[0]);
            }
        });
        console.log('✅ Event listener Notificação configurado');
    }
});

// Função de upload CNH
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
            document.getElementById('result').innerHTML = `
                <p class="text-green-600">✅ CNH processada com sucesso!</p>
                <pre class="text-sm">${JSON.stringify(result.data, null, 2)}</pre>
            `;
        } else {
            document.getElementById('result').innerHTML = `
                <p class="text-red-600">❌ Erro ao processar CNH: ${result.message}</p>
            `;
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        document.getElementById('result').innerHTML = `
            <p class="text-red-600">❌ Erro: ${error.message}</p>
        `;
    } finally {
        document.getElementById('cnh-status').classList.add('hidden');
    }
}

// Função de upload Notificação
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
            document.getElementById('result').innerHTML = `
                <p class="text-green-600">✅ Notificação processada com sucesso!</p>
                <pre class="text-sm">${JSON.stringify(result.data, null, 2)}</pre>
            `;
        } else {
            document.getElementById('result').innerHTML = `
                <p class="text-red-600">❌ Erro ao processar notificação: ${result.message}</p>
            `;
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        document.getElementById('result').innerHTML = `
            <p class="text-red-600">❌ Erro: ${error.message}</p>
        `;
    } finally {
        document.getElementById('notification-status').classList.add('hidden');
    }
}
</script>
@endsection 