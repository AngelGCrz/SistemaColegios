@extends('layouts.app')

@section('title', 'Biblioteca Digital')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Biblioteca Digital</h1>
        <a href="{{ route('admin.biblioteca.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Recurso
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

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

    {{-- Grid de recursos --}}
    @if($recursos->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="mt-3 text-gray-500">No hay recursos digitales aún.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recursos as $recurso)
                <div class="bg-white rounded-lg shadow hover:shadow-md transition p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            @php
                                $iconColors = [
                                    'documento' => 'text-blue-500',
                                    'video' => 'text-red-500',
                                    'enlace' => 'text-green-500',
                                    'imagen' => 'text-yellow-500',
                                    'audio' => 'text-purple-500',
                                    'otro' => 'text-gray-500',
                                ];
                                $iconPaths = [
                                    'documento' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                                    'video' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                                    'enlace' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
                                    'imagen' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                                    'audio' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z',
                                    'otro' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                                ];
                            @endphp
                            <svg class="w-6 h-6 {{ $iconColors[$recurso->tipo] ?? 'text-gray-500' }} mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPaths[$recurso->tipo] ?? $iconPaths['otro'] }}"/>
                            </svg>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($recurso->tipo) }}
                            </span>
                        </div>
                        @if(!$recurso->publico)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                                Privado
                            </span>
                        @endif
                    </div>

                    <h3 class="font-semibold text-gray-800 mb-1">{{ $recurso->titulo }}</h3>
                    @if($recurso->descripcion)
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $recurso->descripcion }}</p>
                    @endif

                    <div class="flex items-center text-xs text-gray-400 mb-3 space-x-3">
                        @if($recurso->materia)
                            <span>{{ $recurso->materia }}</span>
                        @endif
                        @if($recurso->nivel)
                            <span>{{ ucfirst($recurso->nivel) }}</span>
                        @endif
                        <span>{{ $recurso->descargas }} descargas</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">{{ $recurso->user->nombre ?? '' }} {{ $recurso->user->apellidos ?? '' }}</span>
                        <div class="flex items-center gap-2">
                            @if($recurso->url_externa)
                                <a href="{{ $recurso->url_externa }}" target="_blank" rel="noopener noreferrer"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">Abrir</a>
                            @endif
                            @if($recurso->archivo_path)
                                <a href="{{ route('admin.biblioteca.descargar', $recurso) }}"
                                   class="text-green-600 hover:text-green-800 text-sm font-medium">Descargar</a>
                            @endif
                            <form action="{{ route('admin.biblioteca.destroy', $recurso) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este recurso?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Eliminar</button>
                            </form>
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
