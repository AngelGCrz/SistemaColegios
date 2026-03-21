@extends('layouts.app')
@section('title', 'Nuevo Plan - Super Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.planes.index') }}" class="text-sm text-primary-600 hover:underline">&larr; Volver a Planes</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Crear Nuevo Plan</h1>
</div>

<form method="POST" action="{{ route('superadmin.planes.store') }}" class="max-w-2xl">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="ej: basico"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mensual ($) *</label>
                <input type="number" step="0.01" name="precio_mensual" value="{{ old('precio_mensual') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Anual ($) *</label>
                <input type="number" step="0.01" name="precio_anual" value="{{ old('precio_anual') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Máx. Alumnos *</label>
                <input type="number" name="max_alumnos" value="{{ old('max_alumnos') }}" required min="1"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Características (una por línea)</label>
            <textarea name="caracteristicas" rows="5" placeholder="Gestión de notas&#10;Asistencias&#10;Aula virtual"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('caracteristicas') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Orden de visualización *</label>
            <input type="number" name="orden" value="{{ old('orden', 0) }}" required min="0"
                   class="w-32 border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>

    <div class="flex justify-end mt-6">
        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium">
            Crear Plan
        </button>
    </div>
</form>
@endsection
