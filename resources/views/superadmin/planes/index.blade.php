@extends('layouts.app')
@section('title', 'Planes de Suscripción - Super Admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Planes de Suscripción</h1>
        <p class="text-gray-500">Configura los planes disponibles para colegios</p>
    </div>
    <a href="{{ route('superadmin.planes.create') }}"
       class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 text-sm font-medium">
        + Nuevo Plan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($planes as $plan)
    <div class="bg-white rounded-xl shadow-sm border {{ !$plan->activo ? 'opacity-60' : '' }}">
        <div class="p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xl font-bold text-gray-800">{{ $plan->nombre }}</h3>
                @if(!$plan->activo)
                    <span class="text-xs px-2 py-0.5 rounded bg-gray-200 text-gray-500">Inactivo</span>
                @endif
            </div>

            <div class="mb-4">
                <div class="text-3xl font-bold text-primary-600">
                    ${{ number_format($plan->precio_mensual, 2) }}
                    <span class="text-sm font-normal text-gray-400">/mes</span>
                </div>
                <div class="text-sm text-gray-500">
                    ${{ number_format($plan->precio_anual, 2) }}/año
                </div>
            </div>

            <div class="text-sm text-gray-600 space-y-1 mb-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Hasta <strong>{{ number_format($plan->max_alumnos) }}</strong> alumnos
                </div>
                @if($plan->caracteristicas)
                    @foreach($plan->caracteristicas as $car)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $car }}
                    </div>
                    @endforeach
                @endif
            </div>

            <div class="flex items-center justify-between pt-4 border-t text-sm">
                <span class="text-gray-400">{{ $plan->suscripciones_count }} suscripciones</span>
                <a href="{{ route('superadmin.planes.edit', $plan) }}" class="text-primary-600 hover:underline font-medium">Editar</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($planes->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <p class="text-gray-400 mb-4">No hay planes configurados.</p>
        <a href="{{ route('superadmin.planes.create') }}" class="text-primary-600 hover:underline font-medium">Crear el primer plan</a>
    </div>
@endif
@endsection
