@extends('layouts.app')
@section('title', 'Nuevo Mensaje')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Nuevo Mensaje</h1>
</div>

<form method="POST" action="{{ route('mensajes.store') }}" class="bg-white rounded-xl shadow-sm border p-6 max-w-2xl">
    @csrf

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destinatario</label>
            <select name="destinatario_id" required class="w-full border rounded-lg px-3 py-2 text-sm @error('destinatario_id') border-red-500 @enderror">
                <option value="">Seleccionar...</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" @selected(old('destinatario_id') == $usuario->id)>
                    {{ $usuario->apellidos }}, {{ $usuario->nombre }} ({{ ucfirst($usuario->rol) }})
                </option>
                @endforeach
            </select>
            @error('destinatario_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
            <input type="text" name="asunto" value="{{ old('asunto') }}" required
                   class="w-full border rounded-lg px-3 py-2 text-sm @error('asunto') border-red-500 @enderror">
            @error('asunto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
            <textarea name="contenido" rows="6" required class="w-full border rounded-lg px-3 py-2 text-sm @error('contenido') border-red-500 @enderror">{{ old('contenido') }}</textarea>
            @error('contenido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Enviar</button>
        <a href="{{ route('mensajes.inbox') }}" class="px-6 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
    </div>
</form>
@endsection
