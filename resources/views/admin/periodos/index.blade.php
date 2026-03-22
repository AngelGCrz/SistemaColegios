@extends('layouts.app')
@section('title', 'Periodos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Periodos Académicos</h1>
    <button onclick="document.getElementById('crear-periodo').classList.toggle('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Nuevo Periodo
    </button>
</div>

{{-- Formulario crear --}}
<div id="crear-periodo" class="hidden bg-white rounded-xl shadow-sm border p-6 mb-6 max-w-xl">
    <form method="POST" action="{{ route('admin.periodos.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" placeholder="Ej: Periodo 2025" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <input type="number" name="anio" placeholder="{{ date('Y') }}" min="2020" max="2099" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" required class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Crear</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha Inicio</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha Fin</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($periodos as $periodo)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $periodo->nombre }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $periodo->fecha_inicio->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-center">
                    @if($periodo->activo)
                    <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Activo</span>
                    @else
                    <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-500">Inactivo</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if(!$periodo->activo)
                    <form method="POST" action="{{ route('admin.periodos.activar', $periodo) }}" class="inline">
                        @csrf @method('PATCH')
                        <button class="text-xs text-blue-600 hover:underline">Activar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-400">No hay periodos creados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
