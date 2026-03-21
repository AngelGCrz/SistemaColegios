@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
    <h1 class="text-2xl font-bold text-gray-800">Usuarios</h1>
    <a href="{{ route('admin.usuarios.create') }}"
       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo Usuario
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
    <select name="rol" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <option value="">Todos los roles</option>
        @foreach(['admin','docente','alumno','padre'] as $r)
            <option value="{{ $r }}" @selected(request('rol') === $r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, email..."
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:ring-2 focus:ring-blue-500 outline-none">
    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Filtrar</button>
</form>

{{-- Tabla --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Rol</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($usuarios as $usuario)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-xs">
                                {{ strtoupper(substr($usuario->nombre, 0, 1) . substr($usuario->apellidos, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $usuario->nombreCompleto() }}</p>
                                @if($usuario->dni)
                                    <p class="text-xs text-gray-500">DNI: {{ $usuario->dni }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            @if($usuario->rol === 'admin') bg-red-100 text-red-700
                            @elseif($usuario->rol === 'docente') bg-blue-100 text-blue-700
                            @elseif($usuario->rol === 'alumno') bg-green-100 text-green-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            {{ ucfirst($usuario->rol) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $usuario->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-blue-600 hover:underline text-sm mr-3">Editar</a>
                        @if($usuario->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No se encontraron usuarios.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $usuarios->withQueryString()->links() }}
</div>
@endsection
