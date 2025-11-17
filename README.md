# AutoRecurso - Sistema de Recursos de Multas com IA

Sistema web desenvolvido em Laravel para geração automática de recursos de multas de trânsito utilizando inteligência artificial.

## Características

- Geração automática de recursos de multas
- Interface moderna e responsiva
- Otimizado para dispositivos móveis
- Sistema de planos e preços
- Depoimentos de clientes
- Estatísticas de sucesso

## Tecnologias Utilizadas

- Laravel
- TailwindCSS
- Font Awesome
- Swiper.js
- PHP 8.x

## Requisitos

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/autorecurso.git
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Instale as dependências do Node.js:
```bash
npm install
```

4. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

5. Gere a chave da aplicação:
```bash
php artisan key:generate
```

6. Configure o banco de dados no arquivo .env

7. Execute as migrações:
```bash
php artisan migrate
```

8. Compile os assets:
```bash
npm run dev
```

## OCR de Multas (Gemini + olmOCR)

O projeto disponibiliza um endpoint para ler a foto da multa e preencher automaticamente os campos conhecidos.

1. Configure o `.env` com a chave do Gemini:
   ```env
   GEMINI_API_KEY=AIzaSyD67Krgy_1vNiXsFAWI_R3CMB17TGM03oc
   GEMINI_MODEL=gemini-1.5-flash-latest
   OCR_DRIVER=gemini
   ```
2. (Opcional) Para habilitar o olmOCR local:
   ```bash
   python3 -m venv .venv
   .venv/bin/pip install --upgrade pip
   .venv/bin/pip install olmocr pillow
   sudo apt-get install -y poppler-utils
   ```
   ```env
   OCR_DRIVER=olmocr
   OLMOCR_PYTHON_PATH=/caminho/para/.venv/bin/python
   OLMOCR_SCRIPT_PATH=/caminho/para/scripts/olmocr_extract.py
   ```
   > O pipeline local do olmOCR demanda GPU compatível ou um endpoint OpenAI-compatible configurado (ex.: Parasail/DeepInfra). Caso contrário, use o driver `gemini`.

3. Com usuário autenticado, faça um `POST` para `tickets/ocr` enviando `document` (imagem ou PDF) e, opcionalmente, `ticket_id` para atualizar uma multa existente.

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
