@extends('layouts.app')
@section('title', 'Tareas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Tareas</h1>
        <p class="text-gray-500 text-sm">{{ $cursoSeccion->curso->nombre }} — {{ $cursoSeccion->seccion->nombreCompleto() }}</p>
    </div>
    <a href="{{ route('docente.tareas.create', $cursoSeccion) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Nueva Tarea
    </a>
</div>

@forelse($tareas as $tarea)
<div class="bg-white rounded-xl shadow-sm border p-4 mb-3">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="font-semibold text-gray-800">{{ $tarea->titulo }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($tarea->descripcion, 100) }}</p>
            <div class="flex gap-4 mt-2 text-xs text-gray-400">
                @if($tarea->fecha_limite)
                <span>Entrega: {{ $tarea->fecha_limite->format('d/m/Y') }}</span>
                @endif
                <span>Nota máx: {{ $tarea->puntaje_maximo }}</span>
                @if($tarea->publicada)
                <span class="text-green-600">● Publicada</span>
                @else
                <span class="text-yellow-600">● Borrador</span>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('docente.tareas.entregas', $tarea) }}" class="px-3 py-1 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                Entregas ({{ $tarea->entregas_count ?? $tarea->entregas->count() }})
            </a>
            <a href="{{ route('docente.tareas.edit', [$cursoSeccion, $tarea]) }}" class="px-3 py-1 text-xs bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100">Editar</a>
            <form method="POST" action="{{ route('docente.tareas.publicar', [$cursoSeccion, $tarea]) }}" class="inline">
                @csrf @method('PATCH')
                <button class="px-3 py-1 text-xs {{ $tarea->publicada ? 'bg-gray-50 text-gray-600' : 'bg-blue-50 text-blue-700' }} rounded-lg hover:opacity-80">
                    {{ $tarea->publicada ? 'Despublicar' : 'Publicar' }}
                </button>
            </form>
            <form method="POST" action="{{ route('docente.tareas.destroy', [$cursoSeccion, $tarea]) }}" onsubmit="return confirm('¿Eliminar esta tarea?')">
                @csrf @method('DELETE')
                <button class="px-3 py-1 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100">Eliminar</button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="text-center py-12 text-gray-400">No hay tareas creadas para este curso.</div>
@endforelse
@endsection
