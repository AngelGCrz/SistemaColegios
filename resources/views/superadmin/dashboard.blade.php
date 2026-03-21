@extends('layouts.app')
@section('title', 'Dashboard - Super Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Panel Super-Admin</h1>
    <p class="text-gray-500">Visión general de la plataforma</p>
</div>

{{-- Cards de resumen --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Total Colegios</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_colegios']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Activos</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['colegios_activos']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">En Trial</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['colegios_trial']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Vencidos</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($stats['colegios_vencidos']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500">Ingresos del Mes</p>
        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['ingresos_mes'], 2) }}</p>
    </div>
</div>

{{-- Colegios recientes --}}
<div class="bg-white rounded-xl shadow-sm border">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Colegios Recientes</h2>
        <a href="{{ route('superadmin.colegios.index') }}" class="text-sm text-primary-600 hover:underline">Ver todos</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Colegio</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Plan</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Estado</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Registrado</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($colegiosRecientes as $colegio)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <div class="font-medium text-gray-800">{{ $colegio->nombre }}</div>
                        <div class="text-xs text-gray-400">{{ $colegio->email }}</div>
                    </td>
                    <td class="px-6 py-3">
                        @if($colegio->suscripcionActiva?->plan)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-700">
                                {{ $colegio->suscripcionActiva->plan->nombre }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($colegio->activo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Activo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-gray-500">{{ $colegio->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('superadmin.colegios.show', $colegio) }}" class="text-primary-600 hover:underline text-sm">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">No hay colegios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
