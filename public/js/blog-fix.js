/**
 * Script para corrigir problemas de legibilidade no conteúdo do blog
 */
document.addEventListener('DOMContentLoaded', function() {
    // Função principal para corrigir os estilos
    function fixPostContentStyles() {
        // Encontrar todos os elementos do conteúdo do post
        const postContent = document.querySelector('.post-content');
        if (!postContent) return;
        
        // Aplicar estilos base ao container principal
        postContent.style.backgroundColor = '#ffffff';
        postContent.style.color = '#000000';
        
        // Função recursiva para corrigir todos os níveis da árvore DOM
        function fixElementAndChildren(element) {
            // Se for elemento <a>, manter azul
            if (element.tagName === 'A') {
                element.style.color = '#1d4ed8';
                element.style.textDecoration = 'underline';
            } else {
                element.style.color = '#000000';
            }
            
            // Elementos de título devem ter negrito
            if (['H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(element.tagName)) {
                element.style.fontWeight = 'bold';
            }
            
            // Garantir que o fundo seja transparente
            element.style.backgroundColor = 'transparent';
            
            // Remover sombras de texto
            element.style.textShadow = 'none';
            
            // Remover opacidade baixa
            element.style.opacity = '1';
            
            // Remover atributos que podem interferir
            if (element.hasAttribute('data-mce-style')) {
                element.removeAttribute('data-mce-style');
            }
            
            // Processar elementos filhos
            Array.from(element.children).forEach(fixElementAndChildren);
        }
        
        // Processar todos os elementos diretos do conteúdo
        Array.from(postContent.children).forEach(fixElementAndChildren);
    }
    
    // Função para encontrar e corrigir cores claras específicas
    function fixLightColoredText() {
        const postContent = document.querySelector('.post-content');
        if (!postContent) return;
        
        // Buscar por cores claras nos elementos inline
        const elements = postContent.querySelectorAll('[style*="color"]');
        elements.forEach(function(el) {
            const style = window.getComputedStyle(el);
            const color = style.color;
            
            // Verificar se é uma cor clara (convertendo para RGB e verificando luminosidade)
            if (color && (
                color.includes('rgb(255, 255, 255)') || 
                color.includes('rgb(255,255,255)') || 
                color.includes('#fff') || 
                color.includes('#ffffff') ||
                isLightColor(color)
            )) {
                el.style.color = '#000000';
            }
        });
    }
    
    // Verifica se uma cor é clara demais baseado na luminosidade percebida
    function isLightColor(color) {
        // Extrai valores RGB do formato "rgb(r, g, b)" ou "rgba(r, g, b, a)"
        const rgbMatch = color.match(/rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
        if (rgbMatch) {
            const r = parseInt(rgbMatch[1]);
            const g = parseInt(rgbMatch[2]);
            const b = parseInt(rgbMatch[3]);
            
            // Calcula luminosidade (fórmula ponderada comum para percepção humana)
            // Cores com luminância > 200 são consideradas muito claras
            const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
            return luminance > 200;
        }
        return false;
    }
    
    // Executar a correção inicial
    fixPostContentStyles();
    fixLightColoredText();
    
    // Executar novamente após curto intervalo para pegar elementos carregados dinamicamente
    setTimeout(fixPostContentStyles, 500);
    setTimeout(fixLightColoredText, 500);
    
    // E uma última vez após carregamento completo da página
    setTimeout(fixPostContentStyles, 1500);
    setTimeout(fixLightColoredText, 1500);
    
    // Observar mudanças no DOM para aplicar as correções automaticamente
    const observer = new MutationObserver(function(mutations) {
        fixPostContentStyles();
        fixLightColoredText();
    });
    
    const postContent = document.querySelector('.post-content');
    if (postContent) {
        observer.observe(postContent, { 
            childList: true, 
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class']
        });
    }
}); 