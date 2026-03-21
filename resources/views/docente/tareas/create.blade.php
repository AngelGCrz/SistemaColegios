@extends('layouts.app')
@section('title', 'Crear Tarea')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Nueva Tarea</h1>
    <p class="text-gray-500 text-sm">{{ $cursoSeccion->curso->nombre }} — {{ $cursoSeccion->seccion->nombreCompleto() }}</p>
</div>

<form method="POST" action="{{ route('docente.tareas.store', $cursoSeccion) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 max-w-2xl">
    @csrf

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required
                   class="w-full border rounded-lg px-3 py-2 text-sm @error('titulo') border-red-500 @enderror">
            @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('descripcion') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha límite de entrega</label>
                <input type="date" name="fecha_limite" value="{{ old('fecha_limite') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Puntaje máximo</label>
                <input type="number" name="puntaje_maximo" value="{{ old('puntaje_maximo', 20) }}" min="1" max="100" step="1"
                       class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Archivo adjunto (opcional)</label>
            <input type="file" name="archivo_adjunto"
                   class="w-full border rounded-lg px-3 py-2 text-sm file:mr-4 file:py-1 file:px-3 file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 file:rounded">
            <p class="text-xs text-gray-400 mt-1">PDF, Word, imagen. Máx 10MB.</p>
        </div>

        <div class="flex items-center gap-2">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="publicada" value="1" {{ old('publicada') ? 'checked' : '' }} class="sr-only peer">
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
            <span class="text-sm text-gray-700">Publicar inmediatamente</span>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Crear Tarea</button>
        <a href="{{ route('docente.tareas.index', $cursoSeccion) }}" class="px-6 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
    </div>
</form>
@endsection
