@extends('layouts.app')
@section('title', 'Avisos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Avisos</h1>
    <button onclick="document.getElementById('nuevo-aviso').classList.toggle('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Nuevo Aviso
    </button>
</div>

{{-- Formulario crear aviso --}}
<div id="nuevo-aviso" class="hidden bg-white rounded-xl shadow-sm border p-6 mb-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.avisos.store') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required class="w-full border rounded-lg px-3 py-2 text-sm @error('titulo') border-red-500 @enderror">
                @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                <textarea name="contenido" rows="4" required class="w-full border rounded-lg px-3 py-2 text-sm @error('contenido') border-red-500 @enderror">{{ old('contenido') }}</textarea>
                @error('contenido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destinatarios</label>
                <select name="destinatario" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="todos">Todos</option>
                    <option value="docentes">Solo Docentes</option>
                    <option value="alumnos">Solo Alumnos</option>
                    <option value="padres">Solo Padres</option>
                </select>
            </div>
        </div>
        <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Publicar Aviso</button>
    </form>
</div>

<div class="space-y-3">
    @forelse($avisos as $aviso)
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $aviso->titulo }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $aviso->contenido }}</p>
                <div class="flex gap-3 mt-2 text-xs text-gray-400">
                    <span>{{ $aviso->created_at->format('d/m/Y H:i') }}</span>
                    <span class="px-2 py-0.5 rounded bg-gray-100">{{ ucfirst($aviso->destinatario) }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.avisos.destroy', $aviso) }}" onsubmit="return confirm('¿Eliminar aviso?')">
                @csrf @method('DELETE')
                <button class="text-xs text-red-500 hover:underline">Eliminar</button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-12 text-gray-400">No hay avisos publicados.</div>
    @endforelse
</div>

@if($avisos->hasPages())
<div class="mt-4">{{ $avisos->links() }}</div>
@endif
@endsection
