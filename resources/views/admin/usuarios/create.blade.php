@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nuevo Usuario</h1>

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select name="rol" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Seleccionar...</option>
                    @foreach(['admin','docente','alumno','padre'] as $r)
                        <option value="{{ $r }}" @selected(old('rol') === $r)>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                <input type="text" name="dni" value="{{ old('dni') }}" maxlength="20"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" maxlength="20"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t">
            <a href="{{ route('admin.usuarios.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Cancelar</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                Crear Usuario
            </button>
        </div>
    </form>
</div>
@endsection
