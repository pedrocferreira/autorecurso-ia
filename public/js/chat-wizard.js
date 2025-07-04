// Variável global para armazenar a instância do componente
let wizardAppInstance = null;

// Função para gerar CPF aleatório válido
function generateRandomCPF() {
    const generateDigit = (digits) => {
        let sum = 0;
        let weight = digits.length + 1;
        
        for(let i = 0; i < digits.length; i++) {
            sum += parseInt(digits[i]) * weight;
            weight--;
        }
        
        const remainder = sum % 11;
        return remainder < 2 ? 0 : 11 - remainder;
    };

    // Gera 9 números aleatórios
    const numbers = Array.from({ length: 9 }, () => Math.floor(Math.random() * 10));
    
    // Calcula primeiro dígito verificador
    const digit1 = generateDigit(numbers);
    numbers.push(digit1);
    
    // Calcula segundo dígito verificador
    const digit2 = generateDigit(numbers);
    numbers.push(digit2);
    
    // Formata o CPF
    return numbers.join('');
}

document.addEventListener('alpine:init', () => {
    Alpine.data('wizardApp', () => {
        // Cria a instância
        const instance = {
            step: 1,
            isTyping: false,
            typingMessage: '',
            searchInfraction: '',
            filteredInfractions: [],
            selectedCategory: 'all',
            infractionOptions: [], // Será carregado na inicialização
            form: {
                name: '',
                cpf: '',
                email: '',
                phone: '',
                placa: '',
                ticket_number: '',
                organ: '',
                data: '',
                was_driver: '',
                had_signage: '',
                details: '',
                amount: '',
                points: 0,
                vehicle_model: '',
                vehicle_year: '',
                infraction_type: '',
                location: '',
                driver_license: '',
                pendingVehicleConfirm: false,
                apiCallInProgress: false,
                resumoMostrado: false,
                confirma: ''
            },
            messages: [],
            currentQuestion: '',
            showInput: false,
            inputType: 'text',
            inputOptions: [],
            loading: false,
            typing: false,
            progress: 0,
            userInput: '',
            paymentInterval: null,

            async init() {
                // Carrega os dados de infrações passados do Blade
                if (window.infractionOptions && Array.isArray(window.infractionOptions)) {
                    this.infractionOptions = window.infractionOptions.map(i => ({
                        value: i.id,
                        code: i.code,
                        description: i.description,
                        base_amount: parseFloat(i.base_amount || 0),
                        points: parseInt(i.points || 0),
                        severity: i.severity || 'medium'
                    }));
                    this.filteredInfractions = [...this.infractionOptions];
                } else {
                    // Fallback: carrega via API se não tiver dados do Blade
                    await this.loadInfractionOptions();
                }
                
                await this.startConversation();
                this.$watch('searchInfraction', () => this.filterInfractions());
                this.$watch('selectedCategory', () => this.filterInfractions());
            },

            // Função para obter o texto da gravidade
            getSeverityText(severity) {
                const texts = {
                    'light': 'Leve',
                    'medium': 'Média',
                    'severe': 'Grave',
                    'very_severe': 'Gravíssima'
                };
                return texts[severity] || severity;
            },

            // Função para categorizar uma infração
            getCategoryFromInfraction(infraction) {
                const description = infraction.description.toLowerCase();
                const code = infraction.code.toLowerCase();

                if (description.includes('velocidade') || code.startsWith('574')) {
                    return 'velocidade';
                }
                if (description.includes('cnh') || description.includes('habilitação') || description.includes('documentação') || description.includes('licenciamento')) {
                    return 'documentacao';
                }
                if (description.includes('sinal') || description.includes('semáforo') || description.includes('placa') || description.includes('sinalização')) {
                    return 'sinalizacao';
                }
                if (description.includes('estacion') || description.includes('parar')) {
                    return 'estacionamento';
                }
                if (description.includes('dirigir') || description.includes('conduzir') || description.includes('direção')) {
                    return 'conducao';
                }
                return 'outros';
            },

            // Função melhorada de filtragem
            filterInfractions() {
                const search = this.searchInfraction.toLowerCase();
                let filtered = this.infractionOptions;

                // Primeiro filtra por categoria se não for 'all'
                if (this.selectedCategory !== 'all') {
                    filtered = filtered.filter(option => 
                        this.getCategoryFromInfraction(option) === this.selectedCategory
                    );
                }

                // Depois aplica o filtro de busca
                if (search) {
                    filtered = filtered.filter(option => 
                        option.code.toLowerCase().includes(search) || 
                        option.description.toLowerCase().includes(search)
                    );
                }

                // Ordena por código
                filtered.sort((a, b) => a.code.localeCompare(b.code));

                this.filteredInfractions = filtered;
            },

            async startConversation() {
                await this.typeMessage('Olá! 👋 Eu sou a Ana, sua assistente virtual do AutoRecurso. Vou te ajudar a fazer seu recurso de multa de forma simples e rápida!');
                await this.sleep(500);
                await this.typeMessage('Para começar, qual é o seu nome completo?');
                this.showTextInput('name');
                this.updateProgress();
            },

            async showNextQuestion() {
                this.showInput = false;
                this.typing = true;
                
                // Step 1: Dados do usuário
                if (this.step === 1 && !this.form.name) {
                    await this.typeMessage('Para começar, qual é o seu nome completo?');
                    this.showTextInput('name');
                }
                else if (this.step === 1 && !this.form.cpf) {
                    await this.typeMessage('Agora preciso do seu CPF para registrar o recurso:');
                    this.showTextInput('cpf');
                }
                else if (this.step === 1 && !this.form.email) {
                    await this.typeMessage('Para enviar seu recurso depois, preciso do seu e-mail:');
                    this.showTextInput('email');
                }
                else if (this.step === 1 && !this.form.phone) {
                    await this.typeMessage('E qual seu número de celular para contato?');
                    this.showTextInput('phone');
                }
                else if (this.step === 1) {
                    this.step = 2;
                    await this.showNextQuestion();
                }
                else if (this.step === 2 && !this.form.placa) {
                    await this.typeMessage('Agora vamos falar sobre a multa! 🚗 Qual a placa do veículo?');
                    this.showTextInput('placa');
                }
                else if (this.step === 2 && !this.form.ticket_number) {
                    await this.typeMessage('E qual o número do auto de infração?');
                    this.showTextInput('ticket_number');
                }
                else if (this.step === 2 && !this.form.organ) {
                    await this.typeMessage('Qual órgão aplicou a multa?');
                    this.showTextInput('organ');
                }
                else if (this.step === 2 && !this.form.data) {
                    await this.typeMessage('Quando foi a data da infração?');
                    this.showDateInput('data');
                }
                else if (this.step === 2 && !this.form.infraction_type) {
                    if (!Array.isArray(this.infractionOptions) || this.infractionOptions.length === 0) {
                        await this.loadInfractionOptions();
                    }
                    
                    if (!Array.isArray(this.infractionOptions) || this.infractionOptions.length === 0) {
                        await this.typeMessage('Ops, não consegui carregar os tipos de infração. Tente novamente mais tarde.');
                        return;
                    }

                    await this.typeMessage('Qual o tipo de infração?');
                    this.showOptionsInput('infraction_type', this.infractionOptions);
                }
                else if (this.step === 2 && !this.form.location) {
                    await this.typeMessage('Em qual cidade/estado ocorreu a infração?');
                    this.showTextInput('location');
                }
                else if (this.step === 2 && !this.form.driver_license) {
                    await this.typeMessage('Qual o número da sua CNH (Carteira de Habilitação)?');
                    this.showTextInput('driver_license');
                }
                else if (this.step === 2 && !this.form.vehicle_model && !this.form.pendingVehicleConfirm) {
                    await this.typeMessage('Qual é o modelo do veículo?');
                    this.showTextInput('vehicle_model');
                }
                else if (this.step === 2 && !this.form.vehicle_year && !this.form.pendingVehicleConfirm) {
                    await this.typeMessage('Qual o ano de fabricação do veículo?');
                    this.showTextInput('vehicle_year');
                }
                else if (this.step === 2 && !this.form.amount) {
                    await this.typeMessage('Qual o valor da multa (em R$)?');
                    this.showTextInput('amount');
                }
                else if (this.step === 2 && !this.form.points) {
                    await this.typeMessage('Quantos pontos foram atribuídos à CNH?');
                    this.showTextInput('points');
                }
                else if (this.step === 2) {
                    this.step = 3;
                    await this.showNextQuestion();
                }
                else if (this.step === 3 && !this.form.was_driver) {
                    await this.typeMessage('Agora preciso entender melhor a situação... 🤔 Você era o condutor do veículo no momento da infração?');
                    this.showOptionsInput('was_driver', [
                        {value: 'sim', label: 'Sim, eu estava dirigindo'},
                        {value: 'nao', label: 'Não, era outra pessoa'}
                    ]);
                }
                else if (this.step === 3 && !this.form.had_signage) {
                    await this.typeMessage('E havia sinalização adequada no local?');
                    this.showOptionsInput('had_signage', [
                        {value: 'sim', label: 'Sim, estava bem sinalizado'},
                        {value: 'nao', label: 'Não, faltava sinalização'}
                    ]);
                }
                else if (this.step === 3 && !this.form.details) {
                    await this.typeMessage('Você gostaria de adicionar algum detalhe sobre o que aconteceu? Isso pode ajudar no seu recurso! 📝');
                    this.showTextArea('details');
                }
                else if (this.step === 3) {
                    this.step = 4;
                    await this.showNextQuestion();
                }
                else if (this.step === 4) {
                    if (!this.resumoMostrado) {
                        this.resumoMostrado = true;
                        await this.typeMessage('Perfeito! Vou resumir as informações que você me passou:');
                        
                        const resumo = `
Nome: ${this.form.name}
CPF: ${this.form.cpf}
E-mail: ${this.form.email}
Telefone: ${this.form.phone}
Placa: ${this.form.placa}
Auto de Infração: ${this.form.ticket_number}
Órgão: ${this.form.organ}
Data: ${this.form.data}
Era o condutor: ${this.form.was_driver === 'sim' ? 'Sim' : 'Não'}
Havia sinalização: ${this.form.had_signage === 'sim' ? 'Sim' : 'Não'}
Local: ${this.form.location}
Tipo de infração: ${ (this.infractionOptions.find(o => o.value == this.form.infraction_type) || {}).label || this.form.infraction_type }
CNH: ${this.form.driver_license}
Veículo: ${this.form.vehicle_model} / ${this.form.vehicle_year}
Valor da multa: R$ ${this.form.amount}
Pontos: ${this.form.points}
Relato: ${this.form.details}`;

                        this.messages.push({
                            type: 'bot',
                            content: resumo.trim()
                        });

                        await new Promise(resolve => setTimeout(resolve, 1000));
                        await this.typeMessage('Está tudo certo? Podemos prosseguir para o pagamento?');
                        this.showOptionsInput('confirma', [
                            { value: 'sim', label: '✅ Sim, está tudo certo!' },
                            { value: 'nao', label: '❌ Não, preciso corrigir algo' }
                        ]);
                    } else {
                        await this.typeMessage('Está tudo certo? Podemos prosseguir para o pagamento?');
                        this.showOptionsInput('confirma', [
                            { value: 'sim', label: '✅ Sim, está tudo certo!' },
                            { value: 'nao', label: '❌ Não, preciso corrigir algo' }
                        ]);
                    }
                }
                else if (this.step === 5) {
                    await this.typeMessage('Ótimo! 🎉 Chegamos à etapa final!');
                    await this.sleep(500);
                    await this.typeMessage('O valor para gerar seu recurso é de apenas R$ 29,90');
                    await this.sleep(500);
                    await this.typeMessage('Você pode pagar com PIX ou cartão de crédito. Como prefere?');
                    this.showOptionsInput('pagamento', [
                        {value: 'pix', label: '💠 PIX'},
                        {value: 'cartao', label: '💳 Cartão de Crédito'}
                    ]);
                }

                this.typing = false;
                this.updateProgress();
            },

            async handleInput(value) {
                if (this.isTyping) return;
                
                // Tratamentos especiais primeiro
                if (this.inputType === 'options' && value === 'nao' && this.currentQuestion === 'confirma') {
                    this.step = 1;
                    this.resumoMostrado = false; // Reset do flag do resumo
                    this.form = { name: '', cpf: '', email: '', phone: '', placa: '', ticket_number: '', organ: '', data: '', infraction_type: '', location: '', driver_license: '', vehicle_model: '', vehicle_year: '', was_driver: '', had_signage: '', details: '', amount: '', points: '', pendingVehicleConfirm: false, infractionOptions: [], apiCallInProgress: false };
                    await this.typeMessage('Ok, vamos recomeçar então!');
                    await this.showNextQuestion();
                    return;
                }

                if (this.inputType === 'options' && value === 'sim' && this.currentQuestion === 'confirma') {
                    this.step = 5;
                    await this.showNextQuestion();
                    return;
                }

                if (this.inputType === 'options' && value === 'pix') {
                    await this.typeMessage('Gerando QR Code PIX… aguarde ⏳');

                    try {
                        const resp = await fetch('/chat/pix', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.form.name,
                                email: this.form.email,
                                cpf: this.form.cpf,
                                phone: this.form.phone,
                                amount: 29.90
                            })
                        });
                        const json = await resp.json();

                        if (!json.success) {
                            if (json.message && json.message.toLowerCase().includes('cpf')) {
                                await this.typeMessage('❌ O CPF informado é inválido. Por favor, digite um CPF válido para gerar o PIX.');
                                this.showTextInput('cpf');
                                return;
                            }
                            await this.typeMessage('❌ Erro ao gerar PIX. Tente novamente mais tarde.');
                            return;
                        }

                        const billing = json.data.data;
                        console.log('DEBUG billing:', billing);

                        if (!billing) {
                            await this.typeMessage('❌ Erro ao processar resposta do servidor. Tente novamente.');
                            return;
                        }

                        // Sempre exibe o botão se tiver URL
                        if (billing.url) {
                            this.messages.push({
                                type: 'bot',
                                content: `
                                    <div class="bg-white p-6 rounded-xl shadow-lg border-2 border-green-200 mb-4">
                                        <div class="text-center">
                                            <div class="mb-4">
                                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-800 mb-2">Pagamento PIX</h3>
                                                <p class="text-gray-600 mb-4">Clique no botão abaixo para realizar o pagamento</p>
                                            </div>
                                            <a href="${billing.url}" target="_blank" 
                                               class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-lg font-bold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl cursor-pointer no-underline min-w-[280px]"
                                               style="text-decoration: none;">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <span>Pagar com PIX</span>
                                                <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-sm">R$ 29,90</span>
                                            </a>
                                            <p class="text-sm text-gray-500 mt-3">
                                                <i class="fas fa-shield-alt mr-1"></i>
                                                Pagamento seguro • Nova aba será aberta
                                            </p>
                                        </div>
                                    </div>
                                `
                            });

                            // Garante que a mensagem fique visível aplicando a classe de animação
                            this.$nextTick(() => {
                                const msgs = document.querySelectorAll('.message-enter');
                                msgs[msgs.length - 1]?.classList.add('message-enter-active');
                            });

                            await this.typeMessage('Após o pagamento, volto aqui para confirmar automaticamente!');

                            if (billing.id) {
                                this.pollPix(billing.id);
                            }
                        } 
                        // Se tiver QR code, exibe
                        else if (billing.pix && billing.pix.qrcode && billing.pix.copiaecola) {
                            this.messages.push({ type: 'bot', content: `<img src="data:image/png;base64,${billing.pix.qrcode}" class="w-48 mx-auto">` });
                            this.messages.push({ type: 'bot', content: `<small>Código copia-e-cola:</small><br><code class="text-xs break-all">${billing.pix.copiaecola}</code>` });

                            // Aplica a animação de entrada às duas últimas mensagens (imagem + copia-e-cola)
                            this.$nextTick(() => {
                                const msgs = document.querySelectorAll('.message-enter');
                                msgs[msgs.length - 1]?.classList.add('message-enter-active');
                                msgs[msgs.length - 2]?.classList.add('message-enter-active');
                            });

                            await this.typeMessage('Após o pagamento, volto aqui para confirmar automaticamente!');

                            if (billing.id) {
                                this.pollPix(billing.id);
                            }
                        } else {
                            await this.typeMessage('❌ Não foi possível gerar o QR Code ou link de pagamento PIX. Tente novamente mais tarde ou escolha outra forma de pagamento.');
                        }
                    } catch (error) {
                        console.error('❌ Erro ao processar PIX:', error);
                        await this.typeMessage(`
                            <div class="bg-red-50 p-4 rounded-lg shadow-sm">
                                <p class="text-red-800 font-medium">❌ Erro ao gerar pagamento. Por favor, tente novamente.</p>
                                <p class="text-sm text-red-600 mt-1">${error.message}</p>
                            </div>
                        `);
                    }
                    return;
                }

                if (this.inputType === 'options' && value === 'cartao') {
                    await this.typeMessage('Abrindo checkout seguro de cartão de crédito…');
                    try {
                        const resp = await fetch('/chat/stripe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name: this.form.name,
                                email: this.form.email
                            })
                        });
                        const json = await resp.json();
                        if (json.success) {
                            // Abre nova aba (pode ser bloqueada por pop-up), mas também mostra link clicável no chat
                            window.open(json.url, '_blank');

                            this.messages.push({
                                type: 'bot',
                                content: `
                                    <div class="bg-white p-6 rounded-xl shadow-lg border-2 border-blue-200 mb-4">
                                        <div class="text-center">
                                            <div class="mb-4">
                                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-800 mb-2">Pagamento Cartão</h3>
                                                <p class="text-gray-600 mb-4">Clique no botão abaixo para pagar com cartão</p>
                                            </div>
                                            <a href="${json.url}" target="_blank" 
                                               class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-lg font-bold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl cursor-pointer no-underline min-w-[280px]"
                                               style="text-decoration: none;">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                </svg>
                                                <span>Pagar com Cartão</span>
                                                <span class="bg-white bg-opacity-20 px-2 py-1 rounded text-sm">R$ 29,90</span>
                                            </a>
                                            <p class="text-sm text-gray-500 mt-3">
                                                <i class="fas fa-shield-alt mr-1"></i>
                                                Pagamento seguro • Nova aba será aberta
                                            </p>
                                        </div>
                                    </div>
                                `
                            });
                            this.$nextTick(() => {
                                const msgs = document.querySelectorAll('.message-enter');
                                msgs[msgs.length - 1]?.classList.add('message-enter-active');
                            });

                            await this.typeMessage('👉 Conclua o pagamento na nova aba ou clicando no link acima. Após o sucesso, volte aqui para continuar!');
                        } else {
                            await this.typeMessage('Erro ao iniciar pagamento. Tente novamente.');
                        }
                    } catch (error) {
                        console.error('Erro Stripe:', error);
                        await this.typeMessage('❌ Erro ao processar cartão. Tente novamente.');
                    }
                    return;
                }

                if (this.currentQuestion === 'vehicle_confirm') {
                    this.form.pendingVehicleConfirm = false;
                    if (value === 'nao') {
                        await this.typeMessage('Certo, vamos corrigir. Qual é o modelo do veículo?');
                        this.showTextInput('vehicle_model');
                        return;
                    }
                    await this.showNextQuestion();
                    return;
                }
                
                const field = this.currentQuestion;
                this.form[field] = value;

                // Adicionar a mensagem do usuário IMEDIATAMENTE, para manter ordem cronológica
                const userContent = this.inputType === 'options'
                    ? (this.inputOptions.find(opt => opt.value === value)?.label || value)
                    : value;
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message-enter');
                this.messages.push({ type: 'user', content: userContent });
                this.$nextTick(() => {
                    const messages = document.querySelectorAll('.message-enter');
                    messages[messages.length - 1]?.classList.add('message-enter-active');
                });
                this.$nextTick(() => {
                    const chatBody = document.getElementById('chat-body');
                    if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
                });

                // Se for seleção de infração, preencher automaticamente valor e pontos
                if (field === 'infraction_type') {
                    const selectedInfraction = this.infractionOptions.find(opt => opt.value == value);
                    if (selectedInfraction) {
                        this.form.amount = selectedInfraction.base_amount.toFixed(2);
                        this.form.points = selectedInfraction.points;
                        await this.typeMessage(`✅ Infração selecionada!`);
                        await this.typeMessage(`💰 Valor: R$ ${this.form.amount}`);
                        await this.typeMessage(`📊 Pontos: ${this.form.points}`);
                        // Aguardar um pouco antes de continuar
                        await new Promise(resolve => setTimeout(resolve, 800));
                    }
                }

                // Limpamos estado de input
                this.inputType = null;
                this.inputOptions = [];
                this.currentQuestion = null;
                this.userInput = '';

                // Retornar foco ao campo
                this.$nextTick(() => {
                    const inputField = document.getElementById('user-input');
                    if (inputField) inputField.focus();
                });

                // Tratamento especial para placa
                if (field === 'placa') {
                    await this.lookupVehicle(value);
                    return;
                }

                await new Promise(resolve => setTimeout(resolve, 300));
                await this.showNextQuestion();
            },

            async loadInfractionOptions() {
                try {
                    console.log('Tentando carregar tipos de infração via API...');
                    const resp = await fetch('/cliente/api/infraction-types');
                    
                    if (!resp.ok) {
                        throw new Error(`HTTP error! status: ${resp.status}`);
                    }
                    
                    const json = await resp.json();
                    console.log('Resposta da API:', json);
                    
                    if (json.success && Array.isArray(json.data)) {
                        this.infractionOptions = json.data.map(i => ({
                            value: i.id,
                            code: i.code || 'N/A',
                            description: i.description || 'Descrição não disponível',
                            base_amount: parseFloat(i.base_amount || 0),
                            points: parseInt(i.points || 0),
                            severity: i.severity || 'medium'
                        }));
                        this.filteredInfractions = [...this.infractionOptions];
                        console.log(`Carregadas ${this.infractionOptions.length} infrações`);
                    } else {
                        throw new Error('Resposta da API inválida ou sem dados');
                    }
                } catch (e) {
                    console.error('Erro ao carregar tipos de infração:', e);
                    // Fallback: criar alguns tipos básicos se falhar
                    this.infractionOptions = [
                        {
                            value: 'fallback_1',
                            code: '501-00',
                            description: 'Dirigir sem CNH/PPD/ACC',
                            base_amount: 880.41,
                            points: 7,
                            severity: 'very_severe'
                        },
                        {
                            value: 'fallback_2', 
                            code: '574-63',
                            description: 'Transitar em velocidade superior à máxima permitida em até 20%',
                            base_amount: 130.16,
                            points: 4,
                            severity: 'medium'
                        }
                    ];
                    this.filteredInfractions = [...this.infractionOptions];
                    console.log('Usando infrações de fallback');
                }
            },

            async lookupVehicle(plate) {
                try {
                    console.log('Consultando dados do veículo para placa:', plate);
                    
                    // Validação básica da placa
                    if (!plate || plate.length < 7) {
                        console.warn('Placa inválida, pulando consulta:', plate);
                        await this.showNextQuestion();
                        return;
                    }

                    await this.typeMessage('🔍 Consultando dados do veículo na base nacional...');
                    
                    const resp = await fetch('/api/vehicle/lookup', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ placa: plate })
                    });
                    
                    console.log('Status da resposta da API:', resp.status);
                    
                    if (!resp.ok) {
                        const errorData = await resp.json();
                        console.error('Erro na API de veículos:', errorData);
                        
                        if (resp.status === 404) {
                            await this.typeMessage('⚠️ Não encontrei dados para esta placa na base nacional. Vamos continuar com os dados manuais.');
                        } else if (resp.status === 422) {
                            await this.typeMessage('❌ Formato de placa inválido. Vamos continuar sem a consulta automática.');
                        } else if (resp.status === 503) {
                            await this.typeMessage('⚠️ Serviço de consulta temporariamente indisponível. Continuando sem dados automáticos.');
                        } else {
                            await this.typeMessage('⚠️ Erro ao consultar dados do veículo. Vamos continuar manualmente.');
                        }
                        
                        await this.showNextQuestion();
                        return;
                    }

                    const json = await resp.json();
                    console.log('Dados retornados:', json);
                    
                    if (json.success && json.data) {
                        const data = json.data;
                        
                        // Verifica se pelo menos um dado foi encontrado
                        const hasData = data.modelo || data.marca || data.ano || data.cor;
                        
                        if (hasData) {
                            await this.typeMessage(`✅ Dados encontrados na base nacional!`);
                            
                            // Mostrar informações da consulta (custo, saldo, etc.)
                            if (json.api_info && json.api_info.message) {
                                await this.typeMessage(`📊 ${json.api_info.message}`);
                            }
                        
                        const info = [
                                ['Marca', data.marca],
                            ['Modelo', data.modelo],
                            ['Ano', data.ano],
                            ['Cor', data.cor],
                                ['Combustível', data.combustivel],
                            ['Município', data.municipio],
                            ['UF', data.uf]
                        ].filter(([_, value]) => value);

                            let infoText = info.map(([label, value]) => `<strong>${label}:</strong> ${value}`).join('<br>');
                        this.messages.push({ type: 'bot', content: infoText });

                        // Preenche os dados automaticamente
                            if (data.modelo) {
                                this.form.vehicle_model = data.modelo;
                                console.log('Modelo preenchido automaticamente:', data.modelo);
                            }
                            if (data.ano) {
                                this.form.vehicle_year = data.ano;
                                console.log('Ano preenchido automaticamente:', data.ano);
                            }
                            
                            // Aguardar um pouco para que o usuário possa ler as informações
                            await new Promise(resolve => setTimeout(resolve, 1500));
                        
                        await this.typeMessage('Os dados estão corretos?');
                        this.showOptionsInput('vehicle_confirm', [
                            { value: 'sim', label: '✅ Sim, está correto' },
                            { value: 'nao', label: '❌ Não, preciso corrigir' }
                        ]);
                    } else {
                            console.log('Nenhum dado útil encontrado');
                            await this.typeMessage('ℹ️ Dados não encontrados para esta placa. Vamos continuar com preenchimento manual.');
                            await this.showNextQuestion();
                        }
                    } else {
                        console.log('Resposta sem sucesso ou sem dados');
                        await this.typeMessage('ℹ️ Não consegui encontrar dados para esta placa. Vamos continuar manualmente.');
                        await this.showNextQuestion();
                    }
                } catch(e) {
                    console.error('Falha ao consultar placa:', e);
                    await this.typeMessage('⚠️ Erro de conectividade. Vamos continuar sem os dados automáticos.');
                    await this.showNextQuestion();
                }
            },

            showTextInput(field) {
                this.currentQuestion = field;
                this.inputType = 'text';
                this.showInput = true;
                this.$nextTick(() => {
                    const focusLoop = () => {
                        const el = document.getElementById('user-input') || document.querySelector('input[x-model="userInput"]');
                        if (el && el.offsetParent !== null) { // offsetParent null => elemento ainda oculto
                            el.focus();
                            return;
                        }
                        requestAnimationFrame(focusLoop);
                    };
                    focusLoop();
                });
            },

            showDateInput(field) {
                this.currentQuestion = field;
                this.inputType = 'date';
                this.showInput = true;
                this.$nextTick(() => {
                    const focusLoop = () => {
                        const el = document.querySelector('input[type="date"][x-model="userInput"]');
                        if (el && el.offsetParent !== null) {
                            el.focus();
                            return;
                        }
                        requestAnimationFrame(focusLoop);
                    };
                    focusLoop();
                });
            },

            showTextArea(field) {
                this.currentQuestion = field;
                this.inputType = 'textarea';
                this.showInput = true;
                this.$nextTick(() => {
                    const focusLoop = () => {
                        const el = document.querySelector('textarea[x-model="userInput"]');
                        if (el && el.offsetParent !== null) {
                            el.focus();
                            return;
                        }
                        requestAnimationFrame(focusLoop);
                    };
                    focusLoop();
                });
            },

            showOptionsInput(field, options) {
                this.currentQuestion = field;
                this.inputType = 'options';
                this.inputOptions = options;
                this.showInput = true;
            },

            async typeMessage(text, delay = 30) {
                this.isTyping = true;
                let msg = '';
                for (let i = 0; i < text.length; i++) {
                    msg += text[i];
                    this.typingMessage = msg;
                    await new Promise(resolve => setTimeout(resolve, delay));
                    // Scroll seguro
                    const chatBody = document.getElementById('chat-body');
                    if (chatBody) {
                        chatBody.scrollTop = chatBody.scrollHeight;
                    }
                }
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message-enter');
                this.messages.push({ type: 'bot', content: text });
                this.$nextTick(() => {
                    const messages = document.querySelectorAll('.message-enter');
                    messages[messages.length - 1]?.classList.add('message-enter-active');
                });
                this.typingMessage = '';
                this.isTyping = false;
                // Scroll seguro após mensagem completa
                const chatBody = document.getElementById('chat-body');
                if (chatBody) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            },

            sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            },

            updateProgress() {
                this.progress = (this.step / 5) * 100;
            },

            pollPix(id) {
                if (!id) return;
                this.typeMessage('⌛ Aguardando confirmação do pagamento…');
                this.paymentInterval = setInterval(async () => {
                    try {
                        const r = await fetch(`/chat/pix/${id}/status`);
                        const js = await r.json();
                        if (js.status === 'paid') {
                            clearInterval(this.paymentInterval);
                            await this.typeMessage('✅ Pagamento confirmado! Obrigado.');
                            window.location.href = '/cliente/sucesso';
                        } else if (js.status === 'expired' || js.status === 'cancelled') {
                            clearInterval(this.paymentInterval);
                            await this.typeMessage('⚠️ Pagamento não foi concluído. Você pode tentar novamente.');
                        }
                    } catch (error) {
                        console.error('Erro ao verificar status do pagamento:', error);
                    }
                }, 7000);
            }
        };

        // Armazena a instância globalmente
        wizardAppInstance = instance;
        return instance;
    });

    // Aguarda um momento para garantir que o store foi inicializado
    setTimeout(() => {
        console.log('🎯 Tentando inicializar modo teste após criação do store...');
        setupTestMode();
    }, 100);
});

// Configuração do modo teste - DESATIVE PARA PRODUÇÃO
const TEST_MODE = false; // Mude para false em produção

// Função auxiliar para teste
function setupTestMode() {
    // Só executa se TEST_MODE estiver ativo
    if (!TEST_MODE) return;

    console.log('🔍 Iniciando modo teste...');

    // Remove botão existente se houver
    const existingBtn = document.getElementById('test-mode-button');
    if (existingBtn) {
        existingBtn.remove();
    }

    // Cria botão de teste
    const testBtn = document.createElement('button');
    testBtn.innerText = '🧪 Modo Teste: Gerar PIX';
    testBtn.style = 'position:fixed;top:10px;right:10px;z-index:9999;background:#2563eb;color:#fff;padding:10px 18px;border-radius:6px;font-weight:bold;box-shadow:0 2px 8px #0002;cursor:pointer;';
    testBtn.id = 'test-mode-button';
    
    // Adiciona efeito hover
    testBtn.onmouseover = () => testBtn.style.background = '#1d4ed8';
    testBtn.onmouseout = () => testBtn.style.background = '#2563eb';
    
    // Função que preenche os dados de teste
    const fillTestData = async () => {
        try {
            console.log('📝 Preenchendo dados de teste...');
            
            if (!wizardAppInstance) {
                throw new Error('Instância do wizardApp não encontrada');
            }

            // Gera um CPF aleatório válido para teste
            const testCPF = generateRandomCPF();
            console.log('🔑 CPF de teste gerado:', testCPF);

            // Dados de teste com CPF aleatório
            const testData = {
                name: 'Teste PIX ' + new Date().toLocaleTimeString(),
                cpf: testCPF,
                email: `teste-${testCPF}@exemplo.com`,
                phone: '51999999999',
                amount: '29.90',
                points: 0,
                confirma: 'sim'
            };

            // Preenche os dados
            Object.assign(wizardAppInstance.form, testData);
            
            // Força ir para o último passo
            console.log('⏭️ Avançando para etapa de pagamento...');
            wizardAppInstance.step = 5;
            wizardAppInstance.updateProgress();
            
            // Limpa estado anterior
            wizardAppInstance.messages = [];
            wizardAppInstance.showInput = false;
            wizardAppInstance.inputType = null;
            wizardAppInstance.inputOptions = [];
            
            // Adiciona mensagem de processamento
            await wizardAppInstance.typeMessage(`
                <div class="bg-blue-50 p-4 rounded-lg shadow-sm mb-4">
                    <p class="text-blue-800 font-medium">💫 Gerando pagamento PIX...</p>
                </div>
            `);
            
            try {
                // Faz a requisição diretamente
                const response = await fetch('/chat/pix', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(wizardAppInstance.form)
                });

                if (!response.ok) {
                    throw new Error(`Erro ${response.status} ao gerar PIX`);
                }

                const data = await response.json();
                console.log('📦 Dados recebidos:', data);

                // Atualiza o billing no wizardApp
                wizardAppInstance.billing = data;

                // Adiciona mensagem com o botão
                await wizardAppInstance.typeMessage(`
                    <div class="bg-white p-6 rounded-xl shadow-lg mb-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Pagamento Gerado com Sucesso!</h3>
                        <p class="text-gray-600 mb-4">Clique no botão abaixo para realizar o pagamento via PIX</p>
                        <div class="flex flex-col items-center justify-center">
                            <a 
                                href="${data.url}"
                                target="_blank"
                                class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-xl font-bold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md w-full md:w-auto min-w-[250px] cursor-pointer no-underline"
                                style="text-decoration: none;"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Pagar com PIX
                                <span class="text-sm opacity-90 ml-1">(R$ 29,90)</span>
                            </a>
                            <p class="text-sm text-gray-600 mt-3">Uma nova aba será aberta para realizar o pagamento</p>
                        </div>
                    </div>
                `);

                // Adiciona mensagem de monitoramento
                await wizardAppInstance.typeMessage(`
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-blue-800">⌛ Aguardando confirmação do pagamento...</p>
                        <p class="text-sm text-blue-600 mt-1">A página será atualizada automaticamente após a confirmação</p>
                    </div>
                `);

                // Inicia polling se necessário
                if (data.id) {
                    wizardAppInstance.pollPix(data.id);
                }

            } catch (error) {
                console.error('❌ Erro ao processar PIX:', error);
                await wizardAppInstance.typeMessage(`
                    <div class="bg-red-50 p-4 rounded-lg shadow-sm">
                        <p class="text-red-800 font-medium">❌ Erro ao gerar pagamento. Por favor, tente novamente.</p>
                        <p class="text-sm text-red-600 mt-1">${error.message}</p>
                    </div>
                `);
            }

        } catch (error) {
            console.error('❌ Erro no modo teste:', error);
            alert('Erro ao executar modo teste. Verifique o console para mais detalhes.');
        }
    };

    // Configura o clique do botão
    testBtn.onclick = fillTestData;

    // Adiciona o botão na página
    document.body.appendChild(testBtn);
    console.log('✅ Modo teste ativado com sucesso!');
}

// Inicializa o modo teste após o DOM estar pronto
document.addEventListener('DOMContentLoaded', () => {
    // Aguarda um pouco para garantir que o Alpine.js foi inicializado
    setTimeout(setupTestMode, 1000);
}); 