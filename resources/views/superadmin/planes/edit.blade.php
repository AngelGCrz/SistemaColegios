@extends('layouts.app')
@section('title', 'Editar Plan - Super Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.planes.index') }}" class="text-sm text-primary-600 hover:underline">&larr; Volver a Planes</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar: {{ $plan->nombre }}</h1>
</div>

<form method="POST" action="{{ route('superadmin.planes.update', $plan) }}" class="max-w-2xl">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $plan->nombre) }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" value="{{ $plan->slug }}" disabled
                       class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Mensual ($) *</label>
                <input type="number" step="0.01" name="precio_mensual" value="{{ old('precio_mensual', $plan->precio_mensual) }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Anual ($) *</label>
                <input type="number" step="0.01" name="precio_anual" value="{{ old('precio_anual', $plan->precio_anual) }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Máx. Alumnos *</label>
                <input type="number" name="max_alumnos" value="{{ old('max_alumnos', $plan->max_alumnos) }}" required min="1"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Características (una por línea)</label>
            <textarea name="caracteristicas" rows="5"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">{{ old('caracteristicas', is_array($plan->caracteristicas) ? implode("\n", $plan->caracteristicas) : $plan->caracteristicas) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden *</label>
                <input type="number" name="orden" value="{{ old('orden', $plan->orden) }}" required min="0"
                       class="w-32 border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex items-end">
                <label class="flex items-center space-x-3">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $plan->activo))
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm font-medium text-gray-700">Plan Activo</span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-6 space-x-3">
        <a href="{{ route('superadmin.planes.index') }}"
           class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 font-medium">Cancelar</a>
        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium">
            Guardar Cambios
        </button>
    </div>
</form>
@endsection
