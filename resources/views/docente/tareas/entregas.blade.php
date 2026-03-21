@extends('layouts.app')
@section('title', 'Entregas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Entregas: {{ $tarea->titulo }}</h1>
    <p class="text-gray-500 text-sm">Fecha límite: {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y') : '—' }} · Puntaje máx: {{ $tarea->puntaje_maximo }}</p>
</div>

<form method="POST" action="{{ route('docente.tareas.calificar', $tarea) }}">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Alumno</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha Entrega</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Archivo</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600 w-32">Nota</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Comentario</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($entregas as $entrega)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium text-gray-800">
                        {{ $entrega->alumno->user->apellidos }}, {{ $entrega->alumno->user->nombre }}
                    </td>
                    <td class="px-4 py-2 text-center text-gray-500">
                        {{ $entrega->fecha_entrega->format('d/m/Y H:i') }}
                        @if($tarea->fecha_limite && $entrega->fecha_entrega->gt($tarea->fecha_limite))
                        <span class="ml-1 text-xs text-red-500">Tardía</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($entrega->archivo)
                        <a href="{{ route('archivo.descargar', ['tipo' => 'entrega', 'id' => $entrega->id]) }}" class="text-blue-600 hover:underline text-xs">Ver archivo</a>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        <input type="hidden" name="entregas[{{ $entrega->id }}][id]" value="{{ $entrega->id }}">
                        <input type="number" name="entregas[{{ $entrega->id }}][calificacion]"
                               value="{{ $entrega->calificacion }}"
                               min="0" max="{{ $tarea->puntaje_maximo }}" step="0.5"
                               class="w-20 border rounded px-2 py-1 text-center text-sm">
                    </td>
                    <td class="px-4 py-2">
                        <input type="text" name="entregas[{{ $entrega->id }}][comentario]"
                               value="{{ $entrega->comentario_docente }}"
                               placeholder="Comentario..."
                               class="w-full border rounded px-2 py-1 text-sm">
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">Ningún alumno ha entregado esta tarea aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entregas->isNotEmpty())
    <div class="mt-4 flex justify-between items-center">
        <a href="{{ route('docente.tareas.index', $tarea->cursoSeccion) }}" class="text-sm text-gray-500 hover:text-gray-700">Volver</a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            Guardar Calificaciones
        </button>
    </div>
    @endif
</form>
@endsection
