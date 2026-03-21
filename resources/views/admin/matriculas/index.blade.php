@extends('layouts.app')
@section('title', 'Matrículas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Matrículas</h1>
    <button onclick="document.getElementById('nueva-matricula').classList.toggle('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Nueva Matrícula
    </button>
</div>

{{-- Formulario nueva matrícula --}}
<div id="nueva-matricula" class="hidden bg-white rounded-xl shadow-sm border p-6 mb-6 max-w-xl">
    <form method="POST" action="{{ route('admin.matriculas.store') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alumno</label>
                <select name="alumno_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Seleccionar alumno...</option>
                    @foreach($alumnos as $alumno)
                    <option value="{{ $alumno->id }}">{{ $alumno->user->apellidos }}, {{ $alumno->user->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                    <select name="periodo_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}" @selected($periodo->activo)>{{ $periodo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sección</label>
                    <select name="seccion_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Seleccionar...</option>
                        @foreach($secciones as $seccion)
                        <option value="{{ $seccion->id }}">{{ $seccion->nombreCompleto() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Matricular</button>
    </form>
</div>

{{-- Filtros --}}
<div class="flex gap-3 mb-4">
    <form method="GET" class="flex gap-2">
        <select name="periodo_id" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm">
            @foreach($periodos as $periodo)
            <option value="{{ $periodo->id }}" @selected($periodo->id == request('periodo_id', $periodoActivo->id ?? ''))>{{ $periodo->nombre }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Alumno</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Sección</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($matriculas as $matricula)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $matricula->alumno->user->apellidos }}, {{ $matricula->alumno->user->nombre }}</td>
                <td class="px-4 py-3 text-center">{{ $matricula->seccion->nombreCompleto() }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 text-xs rounded {{ $matricula->estado === 'activa' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($matricula->estado) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $matricula->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-8 text-gray-400">No hay matrículas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($matriculas->hasPages())
<div class="mt-4">{{ $matriculas->links() }}</div>
@endif
@endsection
