<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AutoRecurso para Despachantes - Recursos de Multas com IA</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        <!-- Styles -->
        <style>
            /* ! tailwindcss v3.2.4 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:Figtree, sans-serif;font-feature-settings:normal}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;font-weight:inherit;line-height:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}*, ::before, ::after{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }::-webkit-backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }.relative{position:relative}.mx-auto{margin-left:auto;margin-right:auto}.mx-6{margin-left:1.5rem;margin-right:1.5rem}.ml-4{margin-left:1rem}.mt-16{margin-top:4rem}.mt-6{margin-top:1.5rem}.mt-4{margin-top:1rem}.mt-8{margin-top:2rem}.mt-12{margin-top:3rem}.mt-2{margin-top:0.5rem}.-mt-px{margin-top:-1px}.mr-1{margin-right:0.25rem}.flex{display:flex}.inline-flex{display:inline-flex}.grid{display:grid}.h-16{height:4rem}.h-7{height:1.75rem}.h-6{height:1.5rem}.h-5{height:1.25rem}.min-h-screen{min-height:100vh}.w-auto{width:auto}.w-16{width:4rem}.w-7{width:1.75rem}.w-6{width:1.5rem}.w-5{width:1.25rem}.w-full{width:100%}.max-w-7xl{max-width:80rem}.shrink-0{flex-shrink:0}.scale-100{--tw-scale-x:1;--tw-scale-y:1;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.grid-cols-1{grid-template-columns:repeat(1, minmax(0, 1fr))}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-6{gap:1.5rem}.gap-4{gap:1rem}.self-center{align-self:center}.rounded-lg{border-radius:0.5rem}.rounded-full{border-radius:9999px}.rounded-md{border-radius:0.375rem}.bg-gray-100{--tw-bg-opacity:1;background-color:rgb(243 244 246 / var(--tw-bg-opacity))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity))}.bg-red-50{--tw-bg-opacity:1;background-color:rgb(254 242 242 / var(--tw-bg-opacity))}.bg-blue-500{--tw-bg-opacity:1;background-color:rgb(59 130 246 / var(--tw-bg-opacity))}.bg-blue-600{--tw-bg-opacity:1;background-color:rgb(37 99 235 / var(--tw-bg-opacity))}.bg-gradient-to-r{background-image:linear-gradient(to right, var(--tw-gradient-stops))}.from-blue-500{--tw-gradient-from:#3b82f6;--tw-gradient-to:rgb(59 130 246 / 0);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.to-indigo-600{--tw-gradient-to:#4f46e5}.bg-dots-darker{background-image:url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E")}.from-gray-700\/50{--tw-gradient-from:rgb(55 65 81 / 0.5);--tw-gradient-to:rgb(55 65 81 / 0);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.via-transparent{--tw-gradient-to:rgb(0 0 0 / 0);--tw-gradient-stops:var(--tw-gradient-from), transparent, var(--tw-gradient-to)}.bg-center{background-position:center}.stroke-red-500{stroke:#ef4444}.stroke-gray-400{stroke:#9ca3af}.p-6{padding:1.5rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-4{padding-left:1rem;padding-right:1rem}.py-2{padding-top:0.5rem;padding-bottom:0.5rem}.text-center{text-align:center}.text-right{text-align:right}.text-xl{font-size:1.25rem;line-height:1.75rem}.text-sm{font-size:0.875rem;line-height:1.25rem}.text-3xl{font-size:1.875rem;line-height:2.25rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-5xl{font-size:3rem;line-height:1}.text-2xl{font-size:1.5rem;line-height:2rem}.font-semibold{font-weight:600}.font-bold{font-weight:700}.leading-relaxed{line-height:1.625}.text-gray-600{--tw-text-opacity:1;color:rgb(75 85 99 / var(--tw-text-opacity))}.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39 / var(--tw-text-opacity))}.text-gray-500{--tw-text-opacity:1;color:rgb(107 114 128 / var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.text-blue-600{--tw-text-opacity:1;color:rgb(37 99 235 / var(--tw-text-opacity))}.underline{-webkit-text-decoration-line:underline;text-decoration-line:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.shadow-2xl{--tw-shadow:0 25px 50px -12px rgb(0 0 0 / 0.25);--tw-shadow-colored:0 25px 50px -12px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-gray-500\/20{--tw-shadow-color:rgb(107 114 128 / 0.2);--tw-shadow:var(--tw-shadow-colored)}.shadow-md{--tw-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.hover\:bg-blue-700:hover{--tw-bg-opacity:1;background-color:rgb(29 78 216 / var(--tw-bg-opacity))}.hover\:text-white:hover{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.selection\:bg-blue-500 *::selection{--tw-bg-opacity:1;background-color:rgb(59 130 246 / var(--tw-bg-opacity))}.selection\:text-white *::selection{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.selection\:bg-blue-500::selection{--tw-bg-opacity:1;background-color:rgb(59 130 246 / var(--tw-bg-opacity))}.selection\:text-white::selection{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.hover\:text-gray-900:hover{--tw-text-opacity:1;color:rgb(17 24 39 / var(--tw-text-opacity))}.hover\:text-gray-700:hover{--tw-text-opacity:1;color:rgb(55 65 81 / var(--tw-text-opacity))}.focus\:rounded-sm:focus{border-radius:0.125rem}.focus\:outline:focus{outline-style:solid}.focus\:outline-2:focus{outline-width:2px}.focus\:outline-red-500:focus{outline-color:#ef4444}.group:hover .group-hover\:stroke-gray-600{stroke:#4b5563}.z-10{z-index: 10}@media (prefers-reduced-motion: no-preference){.motion-safe\:hover\:scale-\[1\.01\]:hover{--tw-scale-x:1.01;--tw-scale-y:1.01;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}}@media (prefers-color-scheme: dark){.dark\:bg-gray-900{--tw-bg-opacity:1;background-color:rgb(17 24 39 / var(--tw-bg-opacity))}.dark\:bg-gray-800\/50{background-color:rgb(31 41 55 / 0.5)}.dark\:bg-red-800\/20{background-color:rgb(153 27 27 / 0.2)}.dark\:bg-dots-lighter{background-image:url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E")}.dark\:bg-gradient-to-bl{background-image:linear-gradient(to bottom left, var(--tw-gradient-stops))}.dark\:stroke-gray-600{stroke:#4b5563}.dark\:text-gray-400{--tw-text-opacity:1;color:rgb(156 163 175 / var(--tw-text-opacity))}.dark\:text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.dark\:shadow-none{--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.dark\:ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)}.dark\:ring-inset{--tw-ring-inset:inset}.dark\:ring-white\/5{--tw-ring-color:rgb(255 255 255 / 0.05)}.dark\:hover\:text-white:hover{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.group:hover .dark\:group-hover\:stroke-gray-400{stroke:#9ca3af}}@media (min-width: 640px){.sm\:fixed{position:fixed}.sm\:top-0{top:0px}.sm\:right-0{right:0px}.sm\:ml-0{margin-left:0px}.sm\:flex{display:flex}.sm\:items-center{align-items:center}.sm\:justify-center{justify-content:center}.sm\:justify-between{justify-content:space-between}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width: 768px){.md\:grid-cols-2{grid-template-columns:repeat(2, minmax(0, 1fr))}.md\:grid-cols-3{grid-template-columns:repeat(3, minmax(0, 1fr))}}@media (min-width: 1024px){.lg\:gap-8{gap:2rem}.lg\:p-8{padding:2rem}}

            .hero-section {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                padding: 5rem 0 20rem;
                color: white;
                position: relative;
                overflow: hidden;
                clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            }

            @media (max-width: 768px) {
                .hero-section {
                    padding: 1rem 0;
                    clip-path: none;
                    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                }

                .hero-section .max-w-7xl {
                    padding: 0 1rem;
                }

                .hero-icon {
                    height: 64px !important;
                    width: 64px !important;
                    margin-bottom: 1rem;
                }

                .hero-icon i {
                    font-size: 2rem !important;
                }

                .hero-title {
                    font-size: 1.5rem;
                    line-height: 1.3;
                    margin-bottom: 1rem;
                    padding: 0;
                }

                .hero-subtitle {
                    font-size: 1rem;
                    line-height: 1.5;
                    margin-bottom: 1.5rem;
                    padding: 0;
                }

                .hero-cta {
                    flex-direction: column;
                    gap: 0.75rem;
                    width: 100%;
                    margin: 1.5rem 0;
                }

                .hero-cta .cta-button {
                    width: 100%;
                    padding: 0.75rem 1rem;
                    font-size: 1rem;
                    justify-content: center;
                }

                .hero-badges {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.5rem;
                    width: 100%;
                    padding: 0;
                    margin: 1rem 0;
                }

                .hero-badge {
                    padding: 0.5rem;
                    font-size: 0.875rem;
                    justify-content: center;
                    text-align: center;
                }

                .hero-badge i {
                    font-size: 1rem;
                    margin-right: 0.5rem;
                }

                .scroll-indicator {
                    display: none;
                }

                .stats-section {
                    margin-top: 0;
                    padding: 1rem;
                }

                .stats-section .grid {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                    margin-top: 1.5rem;
                }

                .stats-card {
                    padding: 1rem;
                    margin: 0;
                }

                .stats-number {
                    font-size: 1.25rem;
                    margin-bottom: 0.5rem;
                }

                .stats-label {
                    font-size: 0.875rem;
                }

                .section-title {
                    font-size: 2rem;
                    line-height: 1.3;
                    margin-bottom: 0.75rem;
                }

                .section-subtitle {
                    font-size: 1rem;
                    margin-bottom: 2rem;
                }

                .feature-card {
                    padding: 1.5rem;
                    margin: 0.5rem 0;
                }

                .feature-icon {
                    font-size: 2.5rem;
                    margin-bottom: 1rem;
                }

                .feature-card h3 {
                    font-size: 1.25rem;
                    margin-bottom: 0.75rem;
                }

                .feature-card p {
                    font-size: 0.875rem;
                }

                .step-card {
                    padding: 1.5rem;
                    margin: 0.5rem 0;
                }

                .step-number {
                    width: 3rem;
                    height: 3rem;
                    font-size: 1.25rem;
                    margin-bottom: 1rem;
                }

                .step-card h3 {
                    font-size: 1.125rem;
                    margin-bottom: 0.5rem;
                }

                .step-card p {
                    font-size: 0.875rem;
                }

                .price-card {
                    padding: 1.5rem;
                    margin: 0.5rem 0;
                }

                .price-card.popular {
                    transform: none;
                }

                .price-card.popular::before {
                    display: none;
                }

                .price-card h3 {
                    font-size: 1.25rem;
                    margin-bottom: 1rem;
                }

                .price-card .text-5xl {
                    font-size: 2.5rem;
                }

                .price-card ul {
                    margin-bottom: 1.5rem;
                }

                .price-card li {
                    font-size: 0.875rem;
                }

                .testimonial-card {
                    padding: 1.5rem;
                    margin: 0.5rem 0;
                }

                .testimonial-card p {
                    font-size: 0.875rem;
                    margin-bottom: 1rem;
                }

                .testimonial-avatar {
                    width: 3rem;
                    height: 3rem;
                    font-size: 1.25rem;
                }

                .testimonial-card .ml-4 p {
                    font-size: 0.875rem;
                }

                .mt-16, .mt-24 {
                    margin-top: 2rem;
                }

                .gap-16 {
                    gap: 1rem;
                }

                .p-6 {
                    padding: 1rem;
                }
            }

            @media (max-width: 480px) {
                .hero-section {
                    padding: 0.75rem 0;
                }

                .hero-title {
                    font-size: 1.25rem;
                }

                .hero-subtitle {
                    font-size: 0.875rem;
                }

                .hero-badges {
                    grid-template-columns: 1fr;
                }

                .hero-badge {
                    font-size: 0.75rem;
                }

                .stats-number {
                    font-size: 1.125rem;
                }

                .stats-label {
                    font-size: 0.75rem;
                }

                .section-title {
                    font-size: 1.75rem;
                }

                .section-subtitle {
                    font-size: 0.875rem;
                }

                .feature-card, .step-card, .price-card, .testimonial-card {
                    padding: 1rem;
                }

                .feature-icon {
                    font-size: 2rem;
                }

                .feature-card h3, .step-card h3, .price-card h3 {
                    font-size: 1.125rem;
                }

                .price-card .text-5xl {
                    font-size: 2rem;
                }

                .testimonial-avatar {
                    width: 2.5rem;
                    height: 2.5rem;
                    font-size: 1rem;
                }
            }

            .hero-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4l-2-2V24h-2v4l-2 2v4h6zm0-30V0h-2v4h-2v2h2v4h2V6h2V4h-2zM6 34v-4l-2-2V24H2v4l-2 2v4h6zM6 4V0H4v4H2v2h2v4h2V6h2V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                opacity: 0.1;
                animation: float 20s linear infinite;
            }

            @keyframes float {
                0% { background-position: 0 0; }
                100% { background-position: 100% 100%; }
            }

            .hero-icon {
                position: relative;
                transition: all 0.3s ease;
                animation: pulse 2s infinite;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                margin-bottom: 4rem;
            }

            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }

            .hero-icon::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                height: 100%;
                background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
                border-radius: 50%;
                opacity: 0;
                transition: all 0.3s ease;
            }

            .hero-icon:hover::after {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
            }

            .hero-title {
                font-size: 3rem;
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 2.5rem;
                color: white;
                text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                animation: fadeInUp 0.8s ease-out;
                max-width: 1000px;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-subtitle {
                font-size: 1.25rem;
                line-height: 1.4;
                color: rgba(255,255,255,0.95);
                max-width: 900px;
                margin: 0 auto 4rem;
                animation: fadeInUp 0.8s ease-out 0.2s backwards;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .hero-cta {
                display: flex;
                gap: 1rem;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
                margin-top: 2rem;
                margin-bottom: 3rem;
            }

            .hero-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 2rem;
                justify-content: center;
                margin-top: 4rem;
                padding: 0 2rem;
            }

            .hero-badge {
                background: rgba(255, 255, 255, 0.85);
                border: 1px solid rgba(0, 0, 0, 0.05);
                padding: 1rem 2rem;
                border-radius: 9999px;
                transition: all 0.3s ease;
                font-size: 1.125rem;
                backdrop-filter: blur(6px);
                display: flex;
                align-items: center;
                gap: 0.75rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                color: #0f172a; /* texto escuro para garantir contraste */
            }

            .hero-badge i {
                font-size: 1.25rem;
                color: #4ade80;
            }

            .hero-badge:hover {
                background: rgba(255, 255, 255, 0.95);
                transform: translateY(-2px);
                border-color: rgba(0, 0, 0, 0.08);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            }

            .stats-section {
                margin-top: -12rem;
                position: relative;
                z-index: 10;
                padding: 0 2rem;
            }

            .stats-card {
                background: white;
                border-radius: 1.5rem;
                padding: 2.5rem;
                box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.05);
                margin: 0 1rem;
                text-align: center;
                color: #0f172a; /* texto escuro */
            }

            .stats-number {
                font-size: 2rem;
                font-weight: 800;
                line-height: 1;
                margin-bottom: 1.5rem;
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .stats-label {
                font-size: 1.5rem;
                color: #64748b;
                font-weight: 500;
            }

            .section-title {
                font-size: 2.25rem;
                font-weight: 800;
                line-height: 1.2;
                margin-bottom: 1rem;
                color: #0f172a; /* texto escuro para fundos claros */
                position: relative;
                display: inline-block;
            }

            .section-title::after {
                content: '';
                position: absolute;
                bottom: -0.5rem;
                left: 0;
                width: 60px;
                height: 4px;
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                border-radius: 2px;
            }

            .section-subtitle {
                font-size: 1.25rem;
                color: #475569; /* texto escuro para fundo claro */
                max-width: 600px;
                margin: 0 auto 3rem;
                line-height: 1.6;
            }

            .feature-card {
                background: white;
                border-radius: 1.5rem;
                padding: 2rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(0, 0, 0, 0.05);
                margin: 0 1rem;
                color: #0f172a; /* texto escuro */
            }

            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), transparent);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .feature-card:hover::before {
                opacity: 1;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .feature-icon {
                font-size: 3.5rem;
                margin-bottom: 1.5rem;
                color: #3b82f6;
                background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                transition: all 0.3s ease;
            }

            .feature-card:hover .feature-icon {
                transform: scale(1.1);
            }

            .step-card {
                position: relative;
                padding: 2rem;
                background: white;
                border-radius: 1.5rem;
                transition: all 0.3s ease;
                border: 1px solid rgba(0, 0, 0, 0.05);
                text-align: center;
                margin: 0 1rem;
                color: #0f172a; /* texto escuro */
            }

            .step-number {
                width: 4rem;
                height: 4rem;
                background: linear-gradient(135deg, #3b82f6, #1e40af);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 1.75rem;
                margin: 0 auto 1.5rem;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            }

            .step-card:hover .step-number {
                transform: scale(1.1);
                box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
            }

            .price-card {
                background: white;
                border-radius: 1.5rem;
                padding: 2rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(0, 0, 0, 0.05);
                margin: 0 1rem;
                color: #0f172a; /* texto escuro */
            }

            .price-card.popular {
                border: 2px solid #3b82f6;
                transform: none;
                box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.2);
            }

            .price-card.popular::before {
                content: 'Mais Popular';
                position: absolute;
                top: 1rem;
                right: -2rem;
                background: linear-gradient(135deg, #3b82f6, #1e40af);
                color: white;
                padding: 0.5rem 2rem;
                transform: rotate(45deg);
                font-size: 0.875rem;
                font-weight: 600;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .price-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .price-card.popular:hover { transform: translateY(-5px); }

            .testimonial-card {
                background: white;
                border-radius: 1.5rem;
                padding: 2rem;
                transition: all 0.3s ease;
                position: relative;
                border: 1px solid rgba(0, 0, 0, 0.05);
                margin: 0 1rem;
                height: 100%;
                color: #0f172a; /* texto escuro */
            }

            .testimonial-card::before {
                content: '"';
                position: absolute;
                top: 1rem;
                left: 1rem;
                font-size: 4rem;
                color: #3b82f6;
                opacity: 0.1;
                font-family: Georgia, serif;
            }

            .testimonial-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .testimonial-avatar {
                width: 3.25rem;
                height: 3.25rem;
                border-radius: 50%;
                background: linear-gradient(135deg, #3b82f6, #1e40af);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 1.125rem;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            }

            .scroll-indicator {
                position: absolute;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                animation: bounce 2s infinite;
            }

            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% {
                    transform: translateY(0);
                }
                40% {
                    transform: translateY(-20px);
                }
                60% {
                    transform: translateY(-10px);
                }
            }

            /* Swiper Styles */
            .testimonial-swiper {
                padding: 2rem 0;
            }

            .swiper-button-next,
            .swiper-button-prev {
                color: #3b82f6;
                background: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .swiper-button-next:after,
            .swiper-button-prev:after {
                font-size: 1.25rem;
            }

            .swiper-pagination-bullet {
                background: #3b82f6;
                opacity: 0.5;
            }

            .swiper-pagination-bullet-active {
                opacity: 1;
            }

            @media (max-width: 768px) {
                .testimonial-swiper {
                    padding: 1rem 0;
                }

                .swiper-button-next,
                .swiper-button-prev {
                    display: none;
                }
            }
            /* CTA Buttons */
            .cta-button { display:inline-flex; align-items:center; gap:0.5rem; padding:0.9rem 1.5rem; border-radius:0.75rem; font-weight:700; border:1px solid transparent; transition:all .2s ease; }
            .cta-button.primary { background: linear-gradient(135deg, #1e40af, #3b82f6); color:#fff; box-shadow: 0 10px 20px -10px rgba(59,130,246,0.6); }
            .cta-button.primary:hover { transform: translateY(-2px); filter: brightness(1.05); }
            .cta-button.secondary { background:#fff; color:#1e40af; border-color:#93c5fd; }
            .cta-button.secondary:hover { background:#eff6ff; }

            /* Sticky CTA */
            .sticky-cta { position:fixed; left:0; right:0; bottom:0; background:#ffffff; border-top:1px solid rgba(0,0,0,0.06); box-shadow:0 -10px 20px -15px rgba(0,0,0,0.2); transform: translateY(100%); transition: transform .3s ease; z-index: 999; }
            .sticky-cta.show { transform: translateY(0); }
            .sticky-cta .inner { max-width: 80rem; margin: 0 auto; padding: 0.75rem 1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
            .sticky-cta .title { font-weight:700; color:#0f172a; }
            .sticky-cta .actions { display:flex; gap:0.75rem; }
            @media (max-width: 640px) { .sticky-cta .inner { flex-direction:column; align-items:stretch; } .sticky-cta .actions { width:100%; } .sticky-cta .actions a { width:100%; justify-content:center; } }

            /* Step icons */
            .step-icon { font-size:2rem; color:#1e40af; margin-bottom:0.75rem; }

            /* FAQ */
            .faq { max-width: 60rem; margin: 0 auto; }
            .faq-item { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:1rem; margin-bottom:0.75rem; overflow:hidden; }
            .faq-question { width:100%; text-align:left; background:#fff; padding:1rem 1.25rem; font-weight:700; color:#0f172a; display:flex; align-items:center; justify-content:space-between; }
            .faq-answer { display:none; padding:0 1.25rem 1rem; color:#475569; }
            .faq-item.active .faq-answer { display:block; }

            /* Compare */
            .compare-wrap { display:grid; grid-template-columns: 1fr 1fr; gap:1rem; align-items:start; }
            .compare-card { background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:1rem; padding:1.5rem; }
            .compare-card h3 { font-size:1.25rem; font-weight:800; color:#0f172a; margin-bottom:0.75rem; }
            .compare-item { display:flex; align-items:center; gap:0.75rem; color:#475569; margin:0.5rem 0; }
            .compare-item i { width:1.25rem; text-align:center; }
            .compare-yes { color:#16a34a; }
            .compare-no { color:#ef4444; }
            @media (max-width:768px){ .compare-wrap { grid-template-columns: 1fr; } }

            /* Chat widget */
            .chat-button { position: fixed; right: 1rem; bottom: 5rem; background: linear-gradient(135deg, #1e40af, #3b82f6); color:#fff; width:3.25rem; height:3.25rem; border-radius:9999px; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 25px -10px rgba(59,130,246,0.8); z-index:999; }
            .chat-box { position: fixed; right: 1rem; bottom: 9.75rem; width: 20rem; background:#fff; border:1px solid rgba(0,0,0,0.06); border-radius:1rem; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3); display:none; z-index:999; overflow:hidden; }
            .chat-box header { background: linear-gradient(135deg, #1e40af, #3b82f6); color:#fff; padding:0.75rem 1rem; font-weight:700; }
            .chat-box .content { padding:1rem; color:#475569; display:flex; flex-direction:column; gap:0.5rem; }
            .chat-box .content a { display:flex; align-items:center; gap:0.5rem; color:#1e40af; font-weight:700; }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-dots-darker bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-blue-500 selection:text-white">
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                    @else
                        <a href="{{ url('/login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Entrar</a>

                        @if (Route::has('register'))
                            <a href="{{ url('/register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Registrar</a>
                        @endif
                    @endauth
                </div>
            @endif

            <!-- Hero Section -->
            <div class="hero-section">
                <div class="max-w-7xl mx-auto">
                    <!-- Desktop Version -->
                    <div class="hidden md:block">
                        <div class="flex justify-center">
                            <div class="hero-icon h-48 w-48 rounded-full flex items-center justify-center">
                                <i class="fas fa-gavel text-white text-8xl"></i>
                            </div>
                        </div>

                        <h1 class="hero-title">A plataforma de recursos para Despachantes</h1>
                        <p class="hero-subtitle">Centralize clientes, gere recursos em massa com IA, inclua anexos automaticamente (CNH, Notificação, CRLV) e aumente a produtividade do seu escritório. Planos a partir de R$ 150/mês.</p>

                        <div class="hero-cta">
                            <a href="{{ route('subscription.index') }}" class="cta-button primary">
                                <i class="fas fa-rocket"></i>
                                <span>Assinar Plano</span>
                            </a>
                            <a href="#planos" class="cta-button secondary">
                                <i class="fas fa-tags"></i>
                                <span>Ver Planos</span>
                            </a>
                        </div>

                        <div class="hero-badges">
                            <div class="hero-badge">
                                <i class="fas fa-briefcase"></i>
                                <span>Feito para Despachantes</span>
                            </div>
                            <div class="hero-badge">
                                <i class="fas fa-layer-group"></i>
                                <span>Geração em Lote</span>
                            </div>
                            <div class="hero-badge">
                                <i class="fas fa-plug"></i>
                                <span>API Brasil + OCR</span>
                            </div>
                            <div class="hero-badge">
                                <i class="fas fa-headset"></i>
                                <span>Suporte Prioritário</span>
                            </div>
                        </div>

                        <div class="scroll-indicator">
                            <i class="fas fa-chevron-down text-white text-2xl"></i>
                        </div>
                    </div>

                    <!-- Mobile Version -->
                    
                </div>
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-20">
                        <div class="stats-card">
                            <div class="stats-number" data-target="300">300+</div>
                            <div class="stats-label">Despachantes Atendidos</div>
                        </div>
                        <div class="stats-card">
                            <div class="stats-number" data-target="10">10x</div>
                            <div class="stats-label">Mais Produtividade</div>
                        </div>
                        <div class="stats-card">
                            <div class="stats-number" data-target="999">99,9%</div>
                            <div class="stats-label">Disponibilidade</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <!-- Value Proposition -->
                <div class="mt-16 text-center">
                    <h2 class="section-title">Feito para Despachantes</h2>
                    <p class="section-subtitle">Automatize seu fluxo de recursos, escale sua operação e aumente sua margem com uma plataforma criada para escritórios.</p>
                </div>

                <!-- Features Section -->
                <div class="mt-16">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Leitura Automática (OCR/QR)</h3>
                            <p class="text-gray-600 leading-relaxed">CNH, Notificação e CRLV lidos automaticamente. Campos do cliente e do veículo preenchidos em segundos.</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Gestão e Padronização</h3>
                            <p class="text-gray-600 leading-relaxed">Organize casos por cliente, padronize modelos e acompanhe status. Ideal para operação com equipe.</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Produção em Lote</h3>
                            <p class="text-gray-600 leading-relaxed">Gere múltiplos recursos com agilidade, mantendo qualidade e consistência nas peças.</p>
                        </div>
                    </div>
                </div>

                <!-- How It Works -->
                <div id="como-funciona" class="mt-24">
                    <h2 class="section-title">Como Funciona</h2>
                    <p class="section-subtitle">Três passos para escalar o seu escritório</p>
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <div class="step-icon"><i class="fas fa-building"></i></div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Cadastre sua empresa</h3>
                            <p class="text-gray-600">Convide sua equipe e organize os clientes em um só lugar.</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">2</div>
                            <div class="step-icon"><i class="fas fa-id-card"></i></div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Adicione clientes e multas</h3>
                            <p class="text-gray-600">Use consulta por placa (API Brasil) ou envio de documentos com OCR para preencher tudo.</p>
                        </div>

                        <div class="step-card">
                            <div class="step-number">3</div>
                            <div class="step-icon"><i class="fas fa-file-pdf"></i></div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Gere PDFs em lote</h3>
                            <p class="text-gray-600">Crie vários recursos com IA e baixe os PDFs finais com anexos incluídos.</p>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div id="planos" class="mt-24">
                    <h2 class="section-title">Planos para Despachantes</h2>
                    <p class="section-subtitle">Base de R$ 150/mês com descontos por período</p>

                    @php
                        $base = config('subscription.base_price_per_month', 150);
                        $tiers = config('subscription.discount_tiers', []);
                        $durations = [1,3,6,12,18,24];
                        $fmt = fn($v) => 'R$ '.number_format($v,2,',','.');
                    @endphp
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($durations as $months)
                            @php
                                $discount = $tiers[$months] ?? 0;
                                $monthValue = round($base * (1 - $discount), 2);
                                $totalValue = round($monthValue * $months, 2);
                                $saving = round(($base * $months) - $totalValue, 2);
                                $popular = $months === 12;
                            @endphp
                            <div class="price-card {{ $popular ? 'popular' : '' }}">
                                <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">{{ $months }} {{ $months>1 ? 'meses' : 'mês' }}</h3>
                                @if($discount>0)
                                    <p class="text-center text-blue-600 font-semibold mb-2">{{ (int)($discount*100) }}% OFF</p>
                                @endif
                                <div class="text-center mb-6">
                                    <span class="text-5xl font-bold text-gray-900">{{ $fmt($monthValue) }}</span>
                                    <span class="text-gray-600">/mês</span>
                                </div>
                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i><span>Geração de recursos com IA</span></li>
                                    <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i><span>Integração API Brasil + OCR</span></li>
                                    <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i><span>PDF com anexos (CNH, Notificação, CRLV)</span></li>
                                    <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i><span>Equipe multiusuário</span></li>
                                    <li class="flex items-center text-gray-600"><i class="fas fa-check text-green-500 mr-3"></i><span>Dashboard e relatórios</span></li>
                                </ul>
                                <div class="text-center text-gray-600 mb-4">
                                    <div>Total: <strong>{{ $fmt($totalValue) }}</strong></div>
                                    @if($saving>0)
                                        <div>Economia: <strong>{{ $fmt($saving) }}</strong></div>
                                    @endif
                                </div>
                                <form action="{{ route('subscription.subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="months" value="{{ $months }}">
                                    <button class="cta-button primary w-full">Assinar</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Testimonials -->
                <div class="mt-24">
                    <h2 class="section-title">O que os despachantes dizem</h2>
                    <p class="section-subtitle">Resultados reais de escritórios que escalaram com o AutoRecurso</p>
                    
                    <!-- Swiper -->
                    <div class="swiper testimonial-swiper mt-12">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <p class="text-lg text-gray-600 mb-6">"Reduzimos em 80% o tempo de preparação dos recursos. A extração automática dos documentos mudou nosso jogo."</p>
                                    <div class="flex items-center">
                                        <div class="testimonial-avatar">JS</div>
                                        <div class="ml-4">
                                            <p class="font-bold text-gray-900">Carlos Andrade</p>
                                            <p class="text-gray-600">Despachante, RJ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <p class="text-lg text-gray-600 mb-6">"A geração em lote com IA nos permite atender mais clientes sem aumentar o time. Suporte rápido e eficiente."</p>
                                    <div class="flex items-center">
                                        <div class="testimonial-avatar">MS</div>
                                        <div class="ml-4">
                                            <p class="font-bold text-gray-900">Fernanda Alves</p>
                                            <p class="text-gray-600">Escritório Alfa, SP</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <p class="text-lg text-gray-600 mb-6">"Integração com API Brasil e OCR confiável: paramos de digitar dados. PDFs saem prontos com anexos."</p>
                                    <div class="flex items-center">
                                        <div class="testimonial-avatar">PC</div>
                                        <div class="ml-4">
                                            <p class="font-bold text-gray-900">Pedro Costa</p>
                                            <p class="text-gray-600">Despachante Costa, PR</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <p class="text-lg text-gray-600 mb-6">"Mais clientes atendidos, menos retrabalho e visão clara dos processos no dashboard."</p>
                                    <div class="flex items-center">
                                        <div class="testimonial-avatar">AL</div>
                                        <div class="ml-4">
                                            <p class="font-bold text-gray-900">Ana Lima</p>
                                            <p class="text-gray-600">Belo Horizonte</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <p class="text-lg text-gray-600 mb-6">"Economizei tempo e dinheiro com o AutoRecurso. O processo é rápido e eficiente. Já recomendei para vários amigos e todos ficaram satisfeitos!"</p>
                                    <div class="flex items-center">
                                        <div class="testimonial-avatar">RS</div>
                                        <div class="ml-4">
                                            <p class="font-bold text-gray-900">Roberto Santos</p>
                                            <p class="text-gray-600">Porto Alegre</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>

                <!-- Compare Section -->
                <div class="mt-24">
                    <h2 class="section-title">AutoRecurso vs. Advogado tradicional</h2>
                    <p class="section-subtitle">Menor preço, mais velocidade e simplicidade para o seu escritório</p>
                    <div class="mt-12 compare-wrap">
                        <div class="compare-card">
                            <h3>AutoRecurso</h3>
                            <div class="compare-item compare-yes"><i class="fas fa-check"></i> Geração em minutos com IA</div>
                            <div class="compare-item compare-yes"><i class="fas fa-check"></i> Integração com API Brasil e OCR</div>
                            <div class="compare-item compare-yes"><i class="fas fa-check"></i> PDF final com anexos incluídos</div>
                            <div class="compare-item compare-yes"><i class="fas fa-check"></i> Assinatura com desconto por período</div>
                            <div class="compare-item compare-yes"><i class="fas fa-check"></i> Dashboard e relatórios</div>
                        </div>
                        <div class="compare-card">
                            <h3>Advogado tradicional</h3>
                            <div class="compare-item compare-no"><i class="fas fa-times"></i> Prazo de dias para entrega</div>
                            <div class="compare-item compare-no"><i class="fas fa-times"></i> Sem automação de leitura</div>
                            <div class="compare-item compare-no"><i class="fas fa-times"></i> Anexos organizados manualmente</div>
                            <div class="compare-item compare-no"><i class="fas fa-times"></i> Cobrança por peça (alto custo)</div>
                            <div class="compare-item compare-no"><i class="fas fa-times"></i> Sem gestão centralizada</div>
                        </div>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="mt-24 faq">
                    <h2 class="section-title">Perguntas frequentes</h2>
                    <p class="section-subtitle">Tire suas dúvidas rapidamente</p>
                    <div class="mt-8">
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                Como funcionam os descontos por período?
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">Os planos aplicam desconto conforme a duração escolhida. Quanto maior o número de meses, maior o desconto mensal efetivo.</div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                Posso usar com minha equipe?
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">Sim. Você pode convidar usuários e compartilhar o fluxo de trabalho do escritório.</div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                O sistema lê CNH, Notificação e CRLV?
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">Sim. Utilizamos OCR/IA e, quando possível, leitura de QRCode para preencher automaticamente os campos.</div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button">
                                É possível cancelar?
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">Sim. Você pode cancelar a qualquer momento e manterá o acesso até o fim do período contratado.</div>
                        </div>
                    </div>
                </div>

                <!-- Final CTA -->
                <div class="mt-24 text-center">
                    <h2 class="section-title">Pronto para escalar seu escritório?</h2>
                    <p class="section-subtitle">Assine um plano e transforme a produtividade do seu time.</p>

                    <div class="mt-8">
                        <a href="{{ route('subscription.index') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-xl font-bold rounded-lg shadow-lg hover:bg-blue-700 transition-colors duration-300 transform hover:scale-105">
                            Assinar Plano
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-left">
                        © {{ date('Y') }} AutoRecurso - Todos os direitos reservados
                    </div>

                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                        Desenvolvido com <i class="fas fa-heart text-red-500"></i> no Brasil
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky CTA -->
        <div id="stickyCta" class="sticky-cta" aria-hidden="true">
            <div class="inner">
                <div class="title">Pronto para escalar seu escritório?</div>
                <div class="actions">
                    <a href="#planos" class="cta-button secondary"><i class="fas fa-tags"></i> Ver Planos</a>
                    <a href="{{ route('subscription.index') }}" class="cta-button primary"><i class="fas fa-rocket"></i> Assinar Plano</a>
                </div>
            </div>
        </div>

        <!-- Chat Widget (simples) -->
        <button id="chatButton" class="chat-button" aria-label="Abrir chat"><i class="fas fa-comments"></i></button>
        <div id="chatBox" class="chat-box" role="dialog" aria-modal="true" aria-labelledby="chatTitle">
            <header id="chatTitle">Atendimento</header>
            <div class="content">
                <span>Como podemos ajudar?</span>
                <a href="mailto:suporte@autorecurso.com"><i class="fas fa-envelope"></i> suporte@autorecurso.com</a>
                <a href="https://wa.me/5551981281898" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> Falar no WhatsApp</a>
            </div>
        </div>

        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <script>
            const swiper = new Swiper('.testimonial-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 1,
                    },
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 2,
                    },
                }
            });

            // Sticky CTA logic
            const sticky = document.getElementById('stickyCta');
            const showAfter = 500; // px
            window.addEventListener('scroll', () => {
                if (window.scrollY > showAfter) {
                    sticky.classList.add('show');
                    sticky.setAttribute('aria-hidden', 'false');
                } else {
                    sticky.classList.remove('show');
                    sticky.setAttribute('aria-hidden', 'true');
                }
            });

            // FAQ accordion
            document.querySelectorAll('.faq-question').forEach(btn => {
                btn.addEventListener('click', () => {
                    const item = btn.closest('.faq-item');
                    item.classList.toggle('active');
                });
            });

            // Chat widget
            const chatBtn = document.getElementById('chatButton');
            const chatBox = document.getElementById('chatBox');
            let chatOpen = false;
            chatBtn.addEventListener('click', () => {
                chatOpen = !chatOpen;
                chatBox.style.display = chatOpen ? 'block' : 'none';
            });
        </script>
    </body>
</html>

