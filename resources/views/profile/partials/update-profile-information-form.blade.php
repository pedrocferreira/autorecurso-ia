<section x-data="{
    formatCpf(value) {
        let v = value.replace(/\D/g, '');
        v = v.substring(0, 11); // Limita a 11 dígitos
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return v;
    },
    formatPhone(value) {
        let v = value.replace(/\D/g, '');
        v = v.substring(0, 11); // Limita a 11 dígitos (para (XX) XXXXX-XXXX)
        if (v.length <= 2) {
            v = v.replace(/(\d{0,2})/, '($1');
        } else if (v.length <= 6) {
            v = v.replace(/(\d{2})(\d{0,4})/, '($1) $2');
        } else if (v.length <= 10) {
            v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else { // v.length === 11
            v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        }
        return v;
    }
}">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- CPF --}}
        <div>
            <x-input-label for="cpf" :value="__('CPF')" />
            <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" 
                          :value="old('cpf', $user->cpf)" 
                          x-on:input="$event.target.value = formatCpf($event.target.value)" 
                          required autocomplete="off" />
            <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
        </div>

        {{-- Categoria CNH --}}
        <div>
            <x-input-label for="cnh_category" :value="__('Categoria da CNH')" />
            <select id="cnh_category" name="cnh_category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">{{ __('Selecione a categoria...') }}</option>
                <option value="A" {{ old('cnh_category', $user->cnh_category) == 'A' ? 'selected' : '' }}>A - Motocicleta</option>
                <option value="B" {{ old('cnh_category', $user->cnh_category) == 'B' ? 'selected' : '' }}>B - Automóvel</option>
                <option value="AB" {{ old('cnh_category', $user->cnh_category) == 'AB' ? 'selected' : '' }}>AB - Moto e Automóvel</option>
                <option value="C" {{ old('cnh_category', $user->cnh_category) == 'C' ? 'selected' : '' }}>C - Caminhão</option>
                <option value="AC" {{ old('cnh_category', $user->cnh_category) == 'AC' ? 'selected' : '' }}>AC - Moto e Caminhão</option>
                <option value="D" {{ old('cnh_category', $user->cnh_category) == 'D' ? 'selected' : '' }}>D - Ônibus</option>
                <option value="E" {{ old('cnh_category', $user->cnh_category) == 'E' ? 'selected' : '' }}>E - Carreta</option>
                <option value="ACC" {{ old('cnh_category', $user->cnh_category) == 'ACC' ? 'selected' : '' }}>ACC - Autorização para Conduzir Ciclomotor</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('cnh_category')" />
        </div>

        {{-- Endereço CNH --}}
        <div>
            <x-input-label for="cnh_address" :value="__('Endereço (conforme CNH)')" />
            <textarea id="cnh_address" name="cnh_address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required autocomplete="off">{{ old('cnh_address', $user->cnh_address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('cnh_address')" />
        </div>

        {{-- Telefone --}}
        <div>
            <x-input-label for="phone" :value="__('Telefone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
                          :value="old('phone', $user->phone)" 
                          x-on:input="$event.target.value = formatPhone($event.target.value)" 
                          required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
