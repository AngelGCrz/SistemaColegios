@extends('layouts.app')
@section('title', 'Nuevo Aviso')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.avisos.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nuevo Aviso</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="POST" action="{{ route('admin.avisos.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm @error('titulo') border-red-500 @enderror"
                           placeholder="Ej: Reunión de padres de familia">
                    @error('titulo')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                    <textarea name="contenido" id="contenido" rows="5" required
                              class="w-full border rounded-lg px-3 py-2 text-sm @error('contenido') border-red-500 @enderror"
                              placeholder="Escriba el contenido del aviso...">{{ old('contenido') }}</textarea>
                    @error('contenido')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="destinatario" class="block text-sm font-medium text-gray-700 mb-1">Destinatarios</label>
                        <select name="destinatario" id="destinatario" required
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="todos" @selected(old('destinatario') === 'todos')>Todos</option>
                            <option value="docentes" @selected(old('destinatario') === 'docentes')>Solo Docentes</option>
                            <option value="alumnos" @selected(old('destinatario') === 'alumnos')>Solo Alumnos</option>
                            <option value="padres" @selected(old('destinatario') === 'padres')>Solo Padres</option>
                        </select>
                    </div>

                    <div>
                        <label for="seccion_id" class="block text-sm font-medium text-gray-700 mb-1">Sección (opcional)</label>
                        <select name="seccion_id" id="seccion_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">Todas las secciones</option>
                            @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" @selected(old('seccion_id') == $seccion->id)>
                                {{ $seccion->nombreCompleto() }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                    Publicar Aviso
                </button>
                <a href="{{ route('admin.avisos.index') }}" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
