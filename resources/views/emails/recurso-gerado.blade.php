@component('mail::message')
# 🎉 Seu Recurso foi Gerado com Sucesso!

Olá **{{ $user->name }}**,

Ótimas notícias! Seu recurso de multa de trânsito foi gerado com sucesso pela nossa **Inteligência Artificial especializada**! 

## 📋 Dados do Recurso

- **Auto de Infração:** {{ $appeal->ticket->ticket_number ?? 'N/A' }}
- **Placa:** {{ $appeal->ticket->plate ?? 'N/A' }}
- **Valor da Multa:** R$ {{ number_format($appeal->ticket->amount ?? 0, 2, ',', '.') }}
- **Data da Infração:** {{ $appeal->ticket->date ? \Carbon\Carbon::parse($appeal->ticket->date)->format('d/m/Y') : 'N/A' }}
- **Status:** Pronto para protocolo

@component('mail::button', ['url' => route('public.download.recurso', [$appeal->id, preg_replace('/[^0-9]/', '', $appeal->ticket->cpf)]), 'color' => 'success'])
📄 Baixar Recurso (PDF)
@endcomponent

---

## 🚀 Como Protocolar seu Recurso

### **PASSO 1: Baixe os Documentos**
- Clique no botão acima para baixar o **PDF do recurso**
- Imprima o documento em **papel A4 branco**
- **Assine** o recurso na parte final do documento

### **PASSO 2: Documentos Obrigatórios**
Junto com o recurso, você deve anexar **CÓPIAS**:

✅ **RG ou CNH do condutor** (frente e verso)  
✅ **CPF do condutor**  
✅ **Comprovante de residência atual** (máximo 3 meses)  
✅ **CRLV do veículo** (Certificado de Registro e Licenciamento)  
✅ **CNH do condutor** (se diferente do RG)  

### **PASSO 3: Onde Protocolar**

#### 📍 **Protocolo Presencial:**
- **Junta Administrativa de Recursos de Infrações (JARI)** do seu município
- **Detran** da sua cidade
- **CIRETRAN** (Circunscrição Regional de Trânsito)

#### 💻 **Protocolo Online (se disponível):**
- Site do **Detran** do seu estado
- Portal de serviços da **Prefeitura Municipal**
- Sistema online do órgão autuador

### **PASSO 4: Prazo Importante**
⏰ **ATENÇÃO:** Você tem **30 dias** a partir da data de notificação para protocolar o recurso!

---

## 📋 **Checklist antes de Protocolar**

- [ ] Recurso impresso e **assinado**
- [ ] Todas as **cópias dos documentos** anexadas
- [ ] Verificou o **endereço e horário** do local de protocolo
- [ ] Confirmou que ainda está **dentro do prazo**

---

## 💡 **Dicas Importantes**

### ✨ **Protocolo Presencial:**
- Leve **2 vias** do recurso (uma fica com você protocolada)
- Chegue **30 minutos antes** do fechamento
- Tenha em mãos **RG e CPF** originais
- Guarde o **protocolo** que será entregue

### 🌐 **Protocolo Online:**
- Escaneie os documentos em **boa qualidade**
- Salve o **número do protocolo**
- Imprima o **comprovante** de envio
- Acompanhe o status pelo site

### 📧 **Acompanhamento:**
- Anote o **número do protocolo**
- Aguarde resposta em **até 30 dias**
- Em caso de dúvida, entre em contato conosco

---

## 🆘 **Precisa de Ajuda?**

Se tiver dúvidas sobre o processo de protocolamento, entre em contato:

📧 **Email:** suporte@autorecurso.com.br  
📱 **WhatsApp:** (51) 99999-9999  
⏰ **Horário:** Segunda a Sexta, 8h às 18h

@component('mail::panel')
### 🎯 **Importante:**
Este recurso foi gerado por **Inteligência Artificial especializada** em legislação de trânsito brasileira. Nossos algoritmos analisaram seu caso e criaram argumentos jurídicos específicos para maximizar suas chances de aprovação.
@endcomponent

---

## 📊 **Estatísticas de Sucesso**

Nossa plataforma já gerou mais de **10.000 recursos** com:
- ✅ **95% de taxa de aprovação**
- ⚡ **5 minutos** de processo médio
- 🎯 **100% personalizados** para cada caso

---

**Obrigado por confiar no AutoRecurso!** 🚗✨

Desejamos sucesso com seu recurso!

---

*Atenciosamente,*  
**Equipe AutoRecurso**

---

<small>
📌 **Aviso Legal:** Este email contém informações confidenciais. Se você recebeu por engano, delete imediatamente. O AutoRecurso não se responsabiliza por prazos perdidos ou documentação inadequada no protocolo.
</small>

@endcomponent
