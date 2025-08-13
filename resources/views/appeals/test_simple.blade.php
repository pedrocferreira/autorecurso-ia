@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Teste Simples</h1>
        
        <div class="space-y-4">
            <button onclick="testAlert()" class="bg-blue-500 text-white px-4 py-2 rounded">
                Teste Alert
            </button>
            
            <button onclick="testConsole()" class="bg-green-500 text-white px-4 py-2 rounded">
                Teste Console
            </button>
            
            <button onclick="testAPI()" class="bg-purple-500 text-white px-4 py-2 rounded">
                Teste API
            </button>
        </div>
        
        <div id="result" class="mt-4 p-4 bg-gray-100 rounded"></div>
    </div>
</div>

<script>
console.log('🚀 Script carregado!');

function testAlert() {
    alert('Teste funcionando!');
}

function testConsole() {
    console.log('✅ Console funcionando!');
    document.getElementById('result').innerHTML = '<p class="text-green-600">Console funcionando!</p>';
}

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
</script>
@endsection 