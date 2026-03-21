@extends('layouts.app')
@section('title', 'Panel de Padre')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Bienvenido, {{ auth()->user()->nombre }}</h1>
    <p class="text-gray-500 text-sm">Panel de padre/apoderado</p>
</div>

{{-- Avisos --}}
@if($avisos->isNotEmpty())
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Avisos</h2>
    @foreach($avisos as $aviso)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-2">
        <h3 class="font-medium text-gray-800 text-sm">{{ $aviso->titulo }}</h3>
        <p class="text-gray-600 text-xs mt-1">{{ Str::limit($aviso->contenido, 120) }}</p>
        <span class="text-xs text-gray-400">{{ $aviso->created_at->diffForHumans() }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Hijos --}}
<h2 class="text-lg font-semibold text-gray-700 mb-3">Mis Hijos</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($hijos as $alumno)
    @php $matricula = $alumno->matriculaActiva(); @endphp
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                {{ substr($alumno->user->nombre, 0, 1) }}
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">{{ $alumno->user->nombre }} {{ $alumno->user->apellidos }}</h3>
                @if($matricula)
                <p class="text-xs text-gray-400">{{ $matricula->seccion->nombreCompleto() }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('padre.notas', $alumno) }}" class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">📊 Notas</a>
            <a href="{{ route('padre.asistencia', $alumno) }}" class="px-3 py-1.5 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition">✅ Asistencia</a>
            <a href="{{ route('padre.pagos', $alumno) }}" class="px-3 py-1.5 text-xs bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition">💰 Pagos</a>
        </div>
    </div>
    @empty
    <div class="col-span-2 text-center py-8 text-gray-400">No hay alumnos vinculados a tu cuenta.</div>
    @endforelse
</div>
@endsection
