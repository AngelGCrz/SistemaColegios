@extends('layouts.app')
@section('title', 'Editar ' . $colegio->nombre . ' - Super Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.colegios.show', $colegio) }}" class="text-sm text-primary-600 hover:underline">&larr; Volver al detalle</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Editar: {{ $colegio->nombre }}</h1>
</div>

<form method="POST" action="{{ route('superadmin.colegios.update', $colegio) }}" class="space-y-6 max-w-3xl">
    @csrf @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos del Colegio</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $colegio->nombre) }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subdominio *</label>
                <div class="flex items-center">
                    <input type="text" name="subdominio" value="{{ old('subdominio', $colegio->subdominio) }}" required maxlength="50"
                           pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?" placeholder="mi-colegio"
                           class="flex-1 border rounded-l-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r-lg text-sm text-gray-500">.{{ config('app.domain') }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Solo letras minúsculas, números y guiones.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $colegio->email) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $colegio->telefono) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $colegio->direccion) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Contacto</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Contacto</label>
                <input type="text" name="contacto_nombre" value="{{ old('contacto_nombre', $colegio->contacto_nombre) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Contacto</label>
                <input type="email" name="contacto_email" value="{{ old('contacto_email', $colegio->contacto_email) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Contacto</label>
                <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono', $colegio->contacto_telefono) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <label class="flex items-center space-x-3">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" @checked(old('activo', $colegio->activo))
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="text-sm font-medium text-gray-700">Colegio Activo</span>
        </label>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('superadmin.colegios.show', $colegio) }}"
           class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 font-medium">Cancelar</a>
        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium">
            Guardar Cambios
        </button>
    </div>
</form>
@endsection
