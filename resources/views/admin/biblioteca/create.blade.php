@extends('layouts.app')

@section('title', 'Nuevo Recurso Digital')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nuevo Recurso Digital</h1>

    <form action="{{ route('admin.biblioteca.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required
                   class="w-full rounded-lg border-gray-300">
            @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full rounded-lg border-gray-300">{{ old('descripcion') }}</textarea>
            @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <select name="tipo" required class="w-full rounded-lg border-gray-300">
                    <option value="documento" @selected(old('tipo') === 'documento')>Documento</option>
                    <option value="video" @selected(old('tipo') === 'video')>Video</option>
                    <option value="enlace" @selected(old('tipo') === 'enlace')>Enlace</option>
                    <option value="imagen" @selected(old('tipo') === 'imagen')>Imagen</option>
                    <option value="audio" @selected(old('tipo') === 'audio')>Audio</option>
                    <option value="otro" @selected(old('tipo') === 'otro')>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Materia</label>
                <input type="text" name="materia" value="{{ old('materia') }}"
                       class="w-full rounded-lg border-gray-300" placeholder="Ej: Matemáticas">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel</label>
                <select name="nivel" class="w-full rounded-lg border-gray-300">
                    <option value="">Todos</option>
                    <option value="inicial" @selected(old('nivel') === 'inicial')>Inicial</option>
                    <option value="primaria" @selected(old('nivel') === 'primaria')>Primaria</option>
                    <option value="secundaria" @selected(old('nivel') === 'secundaria')>Secundaria</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Archivo (máx. 10 MB)</label>
            <input type="file" name="archivo"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            @error('archivo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL Externa</label>
            <input type="url" name="url_externa" value="{{ old('url_externa') }}"
                   class="w-full rounded-lg border-gray-300" placeholder="https://...">
            @error('url_externa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center">
            <input type="hidden" name="publico" value="0">
            <input type="checkbox" name="publico" value="1" id="publico" checked
                   class="rounded border-gray-300 text-blue-600">
            <label for="publico" class="ml-2 text-sm text-gray-700">Visible para todos los usuarios del colegio</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Recurso
            </button>
            <a href="{{ route('admin.biblioteca.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
