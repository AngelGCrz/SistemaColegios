@extends('layouts.app')
@section('title', 'Mi Suscripción')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Mi Suscripción</h1>
    <p class="text-gray-500">Gestiona el plan de tu colegio</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Estado actual --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Plan Actual</h2>
            @if($suscripcion)
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Plan</span>
                        <span class="font-semibold text-primary-700">{{ $suscripcion->plan->nombre }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Estado</span>
                        @php
                            $colorEstado = match($suscripcion->estado) {
                                'activa' => 'green',
                                'trial' => 'blue',
                                'vencida' => 'red',
                                default => 'gray',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $colorEstado }}-100 text-{{ $colorEstado }}-700">
                            {{ ucfirst($suscripcion->estado) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ciclo</span>
                        <span>{{ ucfirst($suscripcion->ciclo) }}</span>
                    </div>
                    @if($suscripcion->estado === 'trial')
                        <div class="flex justify-between">
                            <span class="text-gray-500">Trial hasta</span>
                            <span class="text-orange-600 font-medium">{{ $suscripcion->trial_hasta?->format('d/m/Y') }}</span>
                        </div>
                        <div class="mt-2 p-3 bg-orange-50 rounded-lg text-xs text-orange-700">
                            Te quedan <strong>{{ $suscripcion->diasRestantes() }} días</strong> de prueba gratuita.
                        </div>
                    @else
                        <div class="flex justify-between">
                            <span class="text-gray-500">Vence</span>
                            <span>{{ $suscripcion->fecha_fin?->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Máx. alumnos</span>
                        <span>{{ number_format($suscripcion->plan->max_alumnos) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Alumnos actuales</span>
                        <span>{{ $colegio->alumnos()->count() }}</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400">Sin suscripción activa</p>
            @endif
        </div>
    </div>

    {{-- Planes disponibles --}}
    <div class="lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $suscripcion ? 'Cambiar Plan' : 'Elige un Plan' }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-{{ min(count($planes), 3) }} gap-4" x-data="{ selectedPlan: '{{ $suscripcion?->plan_id ?? ($planes->first()?->id ?? '') }}', ciclo: 'mensual' }">
            @foreach($planes as $plan)
            <div @click="selectedPlan = '{{ $plan->id }}'"
                 :class="selectedPlan == '{{ $plan->id }}' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'"
                 class="border-2 rounded-xl p-5 cursor-pointer transition {{ $suscripcion?->plan_id === $plan->id ? 'ring-2 ring-primary-200' : '' }}">
                <h3 class="text-lg font-bold text-gray-800">{{ $plan->nombre }}</h3>
                <div class="mt-2">
                    <span class="text-2xl font-bold text-primary-600" x-show="ciclo === 'mensual'">${{ number_format($plan->precio_mensual, 2) }}</span>
                    <span class="text-2xl font-bold text-primary-600" x-show="ciclo === 'anual'" x-cloak>${{ number_format($plan->precio_anual, 2) }}</span>
                    <span class="text-sm text-gray-400" x-text="ciclo === 'mensual' ? '/mes' : '/año'"></span>
                </div>
                <div class="text-sm text-gray-500 mt-1">Hasta {{ number_format($plan->max_alumnos) }} alumnos</div>
                @if($plan->caracteristicas)
                <ul class="mt-3 space-y-1 text-xs text-gray-600">
                    @foreach($plan->caracteristicas as $car)
                    <li class="flex items-center">
                        <svg class="w-3.5 h-3.5 text-green-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $car }}
                    </li>
                    @endforeach
                </ul>
                @endif
                @if($suscripcion?->plan_id === $plan->id)
                    <div class="mt-3 text-xs text-primary-600 font-medium">Plan actual</div>
                @endif
            </div>
            @endforeach

            {{-- Formulario de pago --}}
            <div class="md:col-span-{{ min(count($planes), 3) }} mt-4">
                <form method="POST" action="{{ route('suscripcion.procesar') }}" class="bg-white rounded-xl shadow-sm border p-6">
                    @csrf
                    <input type="hidden" name="plan_id" :value="selectedPlan">

                    <div class="flex items-center space-x-4 mb-4">
                        <label class="text-sm font-medium text-gray-700">Ciclo de facturación:</label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="ciclo" value="mensual" x-model="ciclo"
                                   class="text-primary-600 focus:ring-primary-500">
                            <span class="text-sm">Mensual</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="ciclo" value="anual" x-model="ciclo"
                                   class="text-primary-600 focus:ring-primary-500">
                            <span class="text-sm">Anual <span class="text-green-600 font-medium">(ahorra ~17%)</span></span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 font-bold text-lg">
                        {{ $suscripcion ? 'Actualizar Plan' : 'Activar Plan' }}
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">Se procesará el pago de forma segura.</p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
