@extends('layouts.app')
@section('title', 'Mis Tareas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Mis Tareas</h1>
</div>

@forelse($tareas as $tarea)
@php
    $entrega = $tarea->mi_entrega ?? $tarea->entregas->first();
    $vencida = $tarea->fecha_limite && $tarea->fecha_limite->isPast() && !$entrega;
@endphp
<div class="bg-white rounded-xl shadow-sm border p-4 mb-3 {{ $vencida ? 'border-red-200' : '' }}">
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-gray-800">{{ $tarea->titulo }}</h3>
                @if($entrega && $entrega->calificacion !== null)
                <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Calificada: {{ $entrega->calificacion }}/{{ $tarea->puntaje_maximo }}</span>
                @elseif($entrega)
                <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-700">Entregada</span>
                @elseif($vencida)
                <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-700">Vencida</span>
                @else
                <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-700">Pendiente</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ $tarea->cursoSeccion->curso->nombre }} · Entrega: {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y') : 'Sin fecha' }}</p>
            @if($tarea->descripcion)
            <p class="text-sm text-gray-600 mt-2">{{ $tarea->descripcion }}</p>
            @endif
            @if($tarea->archivo_adjunto)
            <a href="{{ route('archivo.descargar', ['tipo' => 'tarea', 'id' => $tarea->id]) }}" class="inline-block mt-2 text-xs text-blue-600 hover:underline">📎 Archivo adjunto</a>
            @endif
        </div>
    </div>

    {{-- Formulario de entrega --}}
    @if(!$entrega && !$vencida)
    <div class="mt-3 pt-3 border-t" x-data="{ open: false }">
        <button @click="open = !open" class="text-sm text-blue-600 hover:underline">Entregar tarea</button>
        <form x-show="open" x-transition method="POST" action="{{ route('alumno.tareas.entregar', $tarea) }}" enctype="multipart/form-data" class="mt-3 space-y-3">
            @csrf
            <div>
                <textarea name="contenido" rows="3" placeholder="Escribe tu respuesta..." class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div>
                <input type="file" name="archivo" class="text-sm file:mr-4 file:py-1 file:px-3 file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 file:rounded">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Enviar</button>
        </form>
    </div>
    @endif

    {{-- Retroalimentación --}}
    @if($entrega && $entrega->comentario_docente)
    <div class="mt-3 pt-3 border-t">
        <p class="text-xs text-gray-500"><strong>Comentario del docente:</strong> {{ $entrega->comentario_docente }}</p>
    </div>
    @endif
</div>
@empty
<div class="text-center py-12 text-gray-400">No tienes tareas asignadas.</div>
@endforelse
@endsection
