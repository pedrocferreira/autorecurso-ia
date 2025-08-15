@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Teste Upload Simplificado</h1>
        
        <div class="space-y-6">
            <!-- Upload CNH -->
            <div class="border-2 border-dashed border-green-300 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">📄 Upload CNH</h3>
                <input type="file" id="cnhFileInput" accept="image/*,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <div id="cnh-status" class="mt-2 text-sm text-gray-600 hidden">
                    Processando CNH...
                </div>
            </div>
            
            <!-- Upload Notificação -->
            <div class="border-2 border-dashed border-blue-300 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">📋 Upload Notificação</h3>
                <input type="file" id="notificationFileInput" accept="image/*,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <div id="notification-status" class="mt-2 text-sm text-gray-600 hidden">
                    Processando notificação...
                </div>
            </div>
            
            <!-- Upload Documento do Carro (CRLV) -->
            <div class="border-2 border-dashed border-orange-300 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">🚗 Upload Documento do Carro (CRLV)</h3>
                <p class="text-sm text-gray-600 mb-3">Faça upload do CRLV-e ou documento do veículo para extrair informações como placa, modelo, cor, etc.</p>
                <input type="file" id="vehicleFileInput" accept="image/*,.pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                <div id="vehicle-status" class="mt-2 text-sm text-gray-600 hidden">
                    Processando documento do veículo...
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

            <!-- Decodificação local de QR do CRLV (cliente) -->
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                <p class="font-semibold text-yellow-800">Leitura do QR Code (experimental)</p>
                <p class="text-sm text-yellow-700">Tentativa local no navegador de decodificar o QR do PDF/imagem do CRLV para obter o link/dados embutidos.</p>
                <button id="btn-read-qr" class="mt-2 bg-yellow-500 text-white px-3 py-1 rounded">Ler QR do arquivo selecionado</button>
                <pre id="qr-output" class="text-xs mt-2 bg-white p-2 rounded border overflow-x-auto"></pre>
            </div>
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
    const vehicleFileInput = document.getElementById('vehicleFileInput');
    
    console.log('🔍 Elementos encontrados:');
    console.log('- cnhFileInput:', !!cnhFileInput);
    console.log('- notificationFileInput:', !!notificationFileInput);
    console.log('- vehicleFileInput:', !!vehicleFileInput);
    
    if (cnhFileInput && notificationFileInput && vehicleFileInput) {
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
    const vehicleFileInput = document.getElementById('vehicleFileInput');
    
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
    
    if (vehicleFileInput) {
        vehicleFileInput.addEventListener('change', function(event) {
            console.log('🚗 Documento do veículo selecionado:', event.target.files[0]);
            if (event.target.files.length > 0) {
                processVehicleUpload(event.target.files[0]);
            }
        });
        console.log('✅ Event listener Documento do Veículo configurado');
    }

    // QR decode (client-side) for the selected vehicle file
    const btnReadQr = document.getElementById('btn-read-qr');
    const qrOutput = document.getElementById('qr-output');
    if (btnReadQr && qrOutput) {
        btnReadQr.addEventListener('click', async () => {
            const fileInput = document.getElementById('vehicleFileInput');
            if (!fileInput || fileInput.files.length === 0) {
                qrOutput.textContent = 'Selecione primeiro o PDF/Imagem do CRLV.';
                return;
            }
            const file = fileInput.files[0];
            try {
                const result = await decodeQrFromFile(file);
                qrOutput.textContent = result ? `QR lido:\n${result}` : 'Nenhum QR encontrado.';
            } catch (e) {
                qrOutput.textContent = `Erro ao ler QR: ${e.message}`;
            }
        });
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

// Função de upload Documento do Veículo
async function processVehicleUpload(file) {
    console.log('🚗 Processando documento do veículo:', file.name);
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'vehicle');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    document.getElementById('vehicle-status').classList.remove('hidden');
    
    try {
        const response = await fetch('/extract-document-data', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        console.log('📋 Resultado Documento do Veículo:', result);
        
        if (result.success) {
            document.getElementById('result').innerHTML = `
                <p class="text-green-600">✅ Documento do veículo processado com sucesso!</p>
                <h4 class="font-semibold mt-2">🚗 Informações do Veículo:</h4>
                <div class="bg-white p-3 rounded border mt-2">
                    <p><strong>Placa:</strong> ${result.data.vehicle?.plate || 'N/A'}</p>
                    <p><strong>Modelo:</strong> ${result.data.vehicle?.model || 'N/A'}</p>
                    <p><strong>Cor:</strong> ${result.data.vehicle?.color || 'N/A'}</p>
                    <p><strong>Ano:</strong> ${result.data.vehicle?.year || 'N/A'}</p>
                    <p><strong>UF:</strong> ${result.data.vehicle?.uf || 'N/A'}</p>
                    <p><strong>Proprietário:</strong> ${result.data.vehicle?.owner_name || 'N/A'}</p>
                </div>
                <details class="mt-2">
                    <summary class="cursor-pointer text-sm text-gray-600">Ver dados completos</summary>
                    <pre class="text-xs mt-1">${JSON.stringify(result.data, null, 2)}</pre>
                </details>
            `;
        } else {
            document.getElementById('result').innerHTML = `
                <p class="text-red-600">❌ Erro ao processar documento do veículo: ${result.message}</p>
            `;
        }
    } catch (error) {
        console.error('❌ Erro:', error);
        document.getElementById('result').innerHTML = `
            <p class="text-red-600">❌ Erro: ${error.message}</p>
        `;
    } finally {
        document.getElementById('vehicle-status').classList.add('hidden');
    }
}
</script>
<script>
// Leitura de QR no cliente: usa pdf.js (para PDF) e jsQR (para QR em canvas)
// Carrega libs via CDN apenas nesta página de teste
(function loadQrLibs(){
    const pdfJs = document.createElement('script');
    pdfJs.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.min.js';
    pdfJs.crossOrigin = 'anonymous';
    pdfJs.onload = () => {
        try {
            if (window['pdfjsLib']) {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.worker.min.js';
            }
        } catch (e) { console.warn('pdfjs worker setup falhou', e); }
    };
    document.head.appendChild(pdfJs);
    const jsqr = document.createElement('script');
    jsqr.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
    jsqr.crossOrigin = 'anonymous';
    document.head.appendChild(jsqr);
})();

async function decodeQrFromFile(file) {
    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
    if (isPdf) {
        return decodeQrFromPdf(file);
    }
    return decodeQrFromImage(file);
}

async function decodeQrFromPdf(file) {
    // Renderizar primeira página do PDF em canvas e rodar jsQR
    const arrayBuffer = await file.arrayBuffer();
    if (!window['pdfjsLib']) throw new Error('pdf.js não carregado');
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
    const page = await pdf.getPage(1);
    const viewport = page.getViewport({ scale: 2 });
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = viewport.width;
    canvas.height = viewport.height;
    await page.render({ canvasContext: ctx, viewport }).promise;
    return scanCanvasForQr(canvas);
}

async function decodeQrFromImage(file) {
    const img = new Image();
    img.crossOrigin = 'anonymous';
    const url = URL.createObjectURL(file);
    try {
        await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
            img.src = url;
        });
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = img.naturalWidth;
        canvas.height = img.naturalHeight;
        ctx.drawImage(img, 0, 0);
        return scanCanvasForQr(canvas);
    } finally {
        URL.revokeObjectURL(url);
    }
}

function scanCanvasForQr(canvas) {
    if (!window['jsQR']) throw new Error('jsQR não carregado');
    const ctx = canvas.getContext('2d');
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
    return code && code.data ? code.data : null;
}
</script>
@endsection 