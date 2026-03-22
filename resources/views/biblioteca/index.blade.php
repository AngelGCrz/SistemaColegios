@extends('layouts.app')

@section('title', 'Biblioteca Digital')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Biblioteca Digital</h1>

    {{-- Filtros --}}
    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por título..."
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <select name="tipo" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Todos los tipos</option>
                    @foreach(['documento', 'video', 'enlace', 'imagen', 'audio', 'otro'] as $t)
                        <option value="{{ $t }}" @selected(request('tipo') === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="materia" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Todas las materias</option>
                    @foreach($materias as $m)
                        <option value="{{ $m }}" @selected(request('materia') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition text-sm">
                    Filtrar
                </button>
            </div>
        </div>
    </form>

    @if($recursos->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="mt-3 text-gray-500">No hay recursos disponibles aún.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recursos as $recurso)
                <div class="bg-white rounded-lg shadow hover:shadow-md transition p-5">
                    <div class="flex items-center mb-3">
                        @php
                            $iconColors = [
                                'documento' => 'text-blue-500', 'video' => 'text-red-500',
                                'enlace' => 'text-green-500', 'imagen' => 'text-yellow-500',
                                'audio' => 'text-purple-500', 'otro' => 'text-gray-500',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 {{ $iconColors[$recurso->tipo] ?? 'text-gray-500' }}">
                            {{ ucfirst($recurso->tipo) }}
                        </span>
                        @if($recurso->materia)
                            <span class="ml-2 text-xs text-gray-400">{{ $recurso->materia }}</span>
                        @endif
                    </div>

                    <h3 class="font-semibold text-gray-800 mb-1">{{ $recurso->titulo }}</h3>
                    @if($recurso->descripcion)
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $recurso->descripcion }}</p>
                    @endif

                    <div class="flex items-center justify-between mt-3">
                        <span class="text-xs text-gray-400">{{ $recurso->created_at->diffForHumans() }}</span>
                        <div class="flex items-center gap-2">
                            @if($recurso->url_externa)
                                <a href="{{ $recurso->url_externa }}" target="_blank" rel="noopener noreferrer"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">Abrir</a>
                            @endif
                            @if($recurso->archivo_path)
                                <a href="{{ route('biblioteca.descargar', $recurso) }}"
                                   class="text-green-600 hover:text-green-800 text-sm font-medium">Descargar</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $recursos->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
