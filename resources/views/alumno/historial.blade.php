@extends('layouts.app')
@section('title', 'Historial de Entregas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Historial de Entregas</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Tarea</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Curso</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha Entrega</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Calificación</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Archivo</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Comentario</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($entregas as $entrega)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $entrega->tarea->titulo }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $entrega->tarea->cursoSeccion->curso->nombre }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $entrega->fecha_entrega->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-center">
                    @if($entrega->calificacion !== null)
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $entrega->calificacion >= ($entrega->tarea->puntaje_maximo * 0.6) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $entrega->calificacion }}/{{ $entrega->tarea->puntaje_maximo }}
                    </span>
                    @else
                    <span class="text-gray-400 text-xs">Pendiente</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($entrega->archivo)
                    <a href="{{ route('archivo.descargar', ['tipo' => 'entrega', 'id' => $entrega->id]) }}" class="text-blue-600 hover:underline text-xs">Descargar</a>
                    @else
                    <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $entrega->comentario_docente ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-8 text-gray-400">No tienes entregas registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($entregas->hasPages())
<div class="mt-4">{{ $entregas->links() }}</div>
@endif
@endsection
