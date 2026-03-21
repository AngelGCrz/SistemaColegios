@extends('layouts.app')
@section('title', 'Mis Cursos')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Mis Cursos</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($cursoSecciones as $cs)
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h3 class="font-bold text-gray-800 text-lg">{{ $cs->curso->nombre }}</h3>
        <p class="text-sm text-gray-500 mb-3">{{ $cs->seccion->grado->nivel->nombre }} - {{ $cs->seccion->grado->nombre }} "{{ $cs->seccion->nombre }}"</p>
        <p class="text-sm text-gray-600 mb-4">{{ $cs->seccion->matriculas->where('estado', 'activa')->count() }} alumnos</p>
        <div class="flex space-x-2">
            <a href="{{ route('docente.tareas.index', $cs) }}" class="text-sm px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">Tareas</a>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center text-gray-500 py-12">
        No tiene cursos asignados.
    </div>
    @endforelse
</div>
@endsection
