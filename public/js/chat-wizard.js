document.addEventListener('alpine:init', () => {
    Alpine.data('wizardApp', () => ({
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
            points: '',
            vehicle_model: '',
            vehicle_year: '',
            infraction_type: '',
            location: '',
            driver_license: '',
            pendingVehicleConfirm: false,
            apiCallInProgress: false,
            resumoMostrado: false
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
                        await this.typeMessage('❌ Erro ao gerar PIX. Tente novamente mais tarde.');
                        return;
                    }

                    const billing = json.data?.data;
                    if (billing) {
                        // exibe QR code
                        this.messages.push({ type: 'bot', content: `<img src="data:image/png;base64,${billing.pix.qrcode}" class="w-48 mx-auto">` });
                        this.messages.push({ type: 'bot', content: `<small>Código copia-e-cola:</small><br><code class="text-xs break-all">${billing.pix.copiaecola}</code>` });

                        await this.typeMessage('Após o pagamento, volto aqui para confirmar automaticamente!');

                        // inicia polling
                        this.pollPix(billing.id);
                    }
                } catch (error) {
                    console.error('Erro PIX:', error);
                    await this.typeMessage('❌ Erro ao processar PIX. Tente novamente.');
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
                        window.open(json.url, '_blank');
                        await this.typeMessage('👉 Conclua o pagamento na nova aba. Após o sucesso, volte aqui para continuar!');
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
        },

        showDateInput(field) {
            this.currentQuestion = field;
            this.inputType = 'date';
            this.showInput = true;
        },

        showTextArea(field) {
            this.currentQuestion = field;
            this.inputType = 'textarea';
            this.showInput = true;
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
    }));
}); 