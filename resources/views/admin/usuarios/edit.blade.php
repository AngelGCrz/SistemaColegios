@extends('layouts.app')
@section('title', 'Editar Usuario')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Usuario</h1>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña (dejar vacío para mantener)</label>
                <input type="password" name="password" minlength="8"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <input type="text" value="{{ ucfirst($usuario->rol) }}" disabled
                       class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                <input type="text" name="dni" value="{{ old('dni', $usuario->dni) }}" maxlength="20"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" maxlength="20"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $usuario->activo))
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">Usuario activo</span>
            </label>
        </div>

        <div class="flex items-center justify-between pt-4 border-t">
            <a href="{{ route('admin.usuarios.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
