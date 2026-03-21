@extends('layouts.app')
@section('title', 'Asistencia de ' . $alumno->user->nombre)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Asistencia de {{ $alumno->user->nombre }} {{ $alumno->user->apellidos }}</h1>
</div>

{{-- Resumen --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $resumen['presentes'] }}</p>
        <p class="text-xs text-gray-400">Presente</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-2xl font-bold text-red-600">{{ $resumen['faltas'] }}</p>
        <p class="text-xs text-gray-400">Faltas</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-2xl font-bold text-yellow-600">{{ $resumen['tardanzas'] }}</p>
        <p class="text-xs text-gray-400">Tardanzas</p>
    </div>
</div>

{{-- Detalle --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Fecha</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Observación</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($asistencias as $asistencia)
            <tr>
                <td class="px-4 py-2 text-gray-800">{{ $asistencia->fecha->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-center">
                    @switch($asistencia->estado)
                        @case('presente')
                            <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Presente</span>
                            @break
                        @case('falta')
                            <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-700">Falta</span>
                            @break
                        @case('tardanza')
                            <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-700">Tardanza</span>
                            @break
                        @case('justificada')
                            <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-700">Justificada</span>
                            @break
                    @endswitch
                </td>
                <td class="px-4 py-2 text-gray-500 text-xs">{{ $asistencia->observacion ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center py-8 text-gray-400">No hay registros de asistencia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="{{ route('padre.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
</div>
@endsection
