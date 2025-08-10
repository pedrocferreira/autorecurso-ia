<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-sm border-r border-gray-200/50 w-64 min-h-screen flex flex-col fixed z-30 shadow-lg">
    <!-- Logo com design melhorado -->
    <div class="flex items-center h-16 px-6 border-b border-gray-100/50 bg-gradient-to-r from-blue-50 to-indigo-50">
        <a href="{{ route('dashboard') }}" class="flex items-center group">
            <div class="relative">
                <x-application-logo class="block h-9 w-auto group-hover:scale-110 transition-transform duration-200" />
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            </div>
            <span class="ml-3 text-xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors duration-200">
                Auto<span class="text-blue-600">Recurso</span>
            </span>
        </a>
    </div>
    
    <!-- Navigation Links com animações -->
    <div class="flex-1 flex flex-col justify-between py-6">
        <ul class="space-y-2 px-4">
            <li>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="group">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-500 transition-colors duration-200">
                            <i class="fas fa-home text-blue-600 group-hover:text-white transition-colors duration-200"></i>
                        </div>
                        <span class="font-medium">{{ __('Painel') }}</span>
                    </div>
                </x-nav-link>
            </li>
            
            <li>
                <x-nav-link :href="route('appeals.index')" :active="request()->routeIs('appeals.*')" class="group">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-500 transition-colors duration-200">
                            <i class="fas fa-file-alt text-green-600 group-hover:text-white transition-colors duration-200"></i>
                        </div>
                        <span class="font-medium">{{ __('Recursos') }}</span>
                    </div>
                </x-nav-link>
            </li>
            
            <li>
                <x-nav-link :href="route('credits.packages')" :active="request()->routeIs('credits.*')" class="group">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-500 transition-colors duration-200">
                            <i class="fas fa-coins text-yellow-600 group-hover:text-white transition-colors duration-200"></i>
                        </div>
                        <span class="font-medium">{{ __('Créditos') }}</span>
                    </div>
                </x-nav-link>
            </li>
            
            <!-- Separador visual -->
            <li class="pt-4">
                <div class="border-t border-gray-200/50"></div>
            </li>
            
            <!-- Links secundários -->
            <li>
                <a href="{{ route('tickets.create') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group">
                    <div class="w-6 h-6 bg-gray-100 rounded-md flex items-center justify-center mr-3 group-hover:bg-blue-100 transition-colors duration-200">
                        <i class="fas fa-plus text-gray-500 group-hover:text-blue-600 transition-colors duration-200 text-xs"></i>
                    </div>
                    <span>Nova Multa</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('appeals.create_new') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 group">
                    <div class="w-6 h-6 bg-gray-100 rounded-md flex items-center justify-center mr-3 group-hover:bg-green-100 transition-colors duration-200">
                        <i class="fas fa-rocket text-gray-500 group-hover:text-green-600 transition-colors duration-200 text-xs"></i>
                    </div>
                    <span>Gerar Recurso</span>
                </a>
            </li>
            
            @if(Auth::user()->is_admin)
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all duration-200 group">
                    <div class="w-6 h-6 bg-gray-100 rounded-md flex items-center justify-center mr-3 group-hover:bg-purple-100 transition-colors duration-200">
                        <i class="fas fa-cog text-gray-500 group-hover:text-purple-600 transition-colors duration-200 text-xs"></i>
                    </div>
                    <span>Painel Admin</span>
                </a>
            </li>
            @endif
        </ul>
        
        <!-- User Dropdown melhorado -->
        <div class="px-4 py-4 border-t border-gray-100/50">
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="w-full flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150 border border-gray-200/50 shadow-sm">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900 truncate">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="ml-2">
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="text-sm text-gray-900 font-medium">{{ Auth::user()->name }}</div>
                            <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <i class="fas fa-user mr-2 text-gray-400"></i> {{ __('Perfil') }}
                        </x-dropdown-link>
                        
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="flex items-center text-red-600 hover:text-red-700">
                                <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                
                <!-- Créditos disponíveis -->
                <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-coins text-yellow-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Créditos</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600">{{ Auth::user()->credits }}</span>
                    </div>
                    <a href="{{ route('credits.packages') }}" class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-2 px-3 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-plus mr-1"></i> Comprar
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Espaço para o conteúdo ao lado da sidebar -->
<div class="w-64 flex-shrink-0"></div> 