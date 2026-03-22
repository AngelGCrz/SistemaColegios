<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registra tu Colegio - Sistema Colegios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-8 px-4">
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Registra tu Colegio</h1>
            <p class="text-gray-500 mt-2">Comienza con 30 días de prueba gratuita. Sin tarjeta de crédito.</p>
        </div>

        {{-- Errores --}}
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registro.store') }}" x-data="{ step: 1 }">
            @csrf

            {{-- Step indicators --}}
            <div class="flex items-center justify-center mb-8 space-x-4">
                <button type="button" @click="step = 1"
                        :class="step >= 1 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 rounded-full text-sm font-bold transition">1</button>
                <div class="w-12 h-0.5" :class="step >= 2 ? 'bg-primary-600' : 'bg-gray-200'"></div>
                <button type="button" @click="step = 2"
                        :class="step >= 2 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 rounded-full text-sm font-bold transition">2</button>
                <div class="w-12 h-0.5" :class="step >= 3 ? 'bg-primary-600' : 'bg-gray-200'"></div>
                <button type="button" @click="step = 3"
                        :class="step >= 3 ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500'"
                        class="w-8 h-8 rounded-full text-sm font-bold transition">3</button>
            </div>

            {{-- Step 1: Datos del Colegio --}}
            <div x-show="step === 1" x-cloak class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Datos del Colegio</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Colegio *</label>
                        <input type="text" name="colegio_nombre" value="{{ old('colegio_nombre') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email del Colegio</label>
                            <input type="email" name="colegio_email" value="{{ old('colegio_email') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" name="colegio_telefono" value="{{ old('colegio_telefono') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="colegio_direccion" value="{{ old('colegio_direccion') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Contacto *</label>
                            <input type="text" name="contacto_nombre" value="{{ old('contacto_nombre') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email de Contacto *</label>
                            <input type="email" name="contacto_email" value="{{ old('contacto_email') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de Contacto</label>
                        <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" @click="step = 2" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium">
                        Siguiente &rarr;
                    </button>
                </div>
            </div>

            {{-- Step 2: Plan --}}
            <div x-show="step === 2" x-cloak class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Elige tu Plan</h2>
                <div class="grid grid-cols-1 md:grid-cols-{{ min(count($planes), 3) }} gap-4">
                    @foreach($planes as $plan)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only"
                               @checked(old('plan_id') == $plan->id || $loop->first) required>
                        <div class="border-2 rounded-xl p-5 transition peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-gray-300">
                            <h3 class="text-lg font-bold text-gray-800">{{ $plan->nombre }}</h3>
                            <div class="text-2xl font-bold text-primary-600 mt-2">
                                ${{ number_format($plan->precio_mensual, 2) }}<span class="text-sm font-normal text-gray-400">/mes</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Hasta {{ number_format($plan->max_alumnos) }} alumnos</div>
                            @if($plan->caracteristicas)
                            <ul class="mt-3 space-y-1 text-sm text-gray-600">
                                @foreach($plan->caracteristicas as $car)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $car }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="flex justify-between mt-6">
                    <button type="button" @click="step = 1" class="text-gray-500 hover:text-gray-700 font-medium">
                        &larr; Anterior
                    </button>
                    <button type="button" @click="step = 3" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium">
                        Siguiente &rarr;
                    </button>
                </div>
            </div>

            {{-- Step 3: Cuenta Administrador --}}
            <div x-show="step === 3" x-cloak class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Crea tu cuenta de Administrador</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input type="text" name="admin_nombre" value="{{ old('admin_nombre') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                            <input type="text" name="admin_apellidos" value="{{ old('admin_apellidos') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                            <input type="password" name="admin_password" required minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                            <input type="password" name="admin_password_confirmation" required minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <button type="button" @click="step = 2" class="text-gray-500 hover:text-gray-700 font-medium">
                        &larr; Anterior
                    </button>
                    <button type="submit" class="bg-primary-600 text-white px-8 py-3 rounded-lg hover:bg-primary-700 font-bold text-lg">
                        Comenzar Prueba Gratuita
                    </button>
                </div>
            </div>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-primary-600 hover:underline font-medium">Inicia Sesión</a>
        </p>
    </div>
</body>
</html>
