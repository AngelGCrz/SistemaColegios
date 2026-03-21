@extends('layouts.app')
@section('title', 'Mi Panel')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Hola, {{ auth()->user()->nombre }} 👋</h1>
    <p class="text-gray-500 text-sm">Bienvenido a tu panel de estudiante</p>
</div>

{{-- Avisos recientes --}}
@if($avisos->isNotEmpty())
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Avisos recientes</h2>
    <div class="space-y-2">
        @foreach($avisos as $aviso)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-medium text-gray-800 text-sm">{{ $aviso->titulo }}</h3>
                    <p class="text-gray-600 text-xs mt-1">{{ Str::limit($aviso->contenido, 120) }}</p>
                </div>
                <span class="text-xs text-gray-400">{{ $aviso->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Info de matrícula --}}
@if($matricula)
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-xs text-gray-400 uppercase">Sección</p>
        <p class="text-lg font-bold text-gray-800">{{ $matricula->seccion->nombreCompleto() }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-xs text-gray-400 uppercase">Periodo</p>
        <p class="text-lg font-bold text-gray-800">{{ $matricula->periodo->nombre }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-xs text-gray-400 uppercase">Estado</p>
        <p class="text-lg font-bold {{ $matricula->estado === 'activa' ? 'text-green-600' : 'text-gray-600' }}">
            {{ ucfirst($matricula->estado) }}
        </p>
    </div>
</div>
@endif

{{-- Tareas pendientes --}}
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Tareas Pendientes</h2>
    @forelse($tareasRecientes as $tarea)
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-2 flex justify-between items-center">
        <div>
            <h3 class="font-medium text-gray-800 text-sm">{{ $tarea->titulo }}</h3>
            <p class="text-xs text-gray-400">{{ $tarea->cursoSeccion->curso->nombre }} · Entrega: {{ $tarea->fecha_limite ? $tarea->fecha_limite->format('d/m/Y') : '—' }}</p>
        </div>
        <a href="{{ route('alumno.tareas') }}" class="text-xs text-blue-600 hover:underline">Ver</a>
    </div>
    @empty
    <div class="text-center py-6 text-gray-400 text-sm">No tienes tareas pendientes 🎉</div>
    @endforelse
</div>

{{-- Accesos rápidos --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <a href="{{ route('alumno.notas') }}" class="bg-white border rounded-xl p-4 text-center hover:shadow-md transition">
        <div class="text-2xl mb-1">📊</div>
        <span class="text-sm font-medium text-gray-700">Mis Notas</span>
    </a>
    <a href="{{ route('alumno.tareas') }}" class="bg-white border rounded-xl p-4 text-center hover:shadow-md transition">
        <div class="text-2xl mb-1">📝</div>
        <span class="text-sm font-medium text-gray-700">Tareas</span>
    </a>
    <a href="{{ route('mensajes.inbox') }}" class="bg-white border rounded-xl p-4 text-center hover:shadow-md transition">
        <div class="text-2xl mb-1">✉️</div>
        <span class="text-sm font-medium text-gray-700">Mensajes</span>
    </a>
    <a href="#" class="bg-white border rounded-xl p-4 text-center hover:shadow-md transition">
        <div class="text-2xl mb-1">🎒</div>
        <span class="text-sm font-medium text-gray-700">Mi Perfil</span>
    </a>
</div>
@endsection
