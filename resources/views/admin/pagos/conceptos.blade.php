@extends('layouts.app')
@section('title', 'Conceptos de Pago')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pagos.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Conceptos de Pago</h1>
        </div>
    </div>

    {{-- Formulario crear concepto --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Nuevo Concepto</h2>
        <form method="POST" action="{{ route('admin.pagos.conceptos.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" id="nombre" required value="{{ old('nombre') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm @error('nombre') border-red-500 @enderror"
                       placeholder="Ej: Matrícula, Pensión Marzo...">
                @error('nombre')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="w-36">
                <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto (S/)</label>
                <input type="number" name="monto" id="monto" step="0.01" min="0" required value="{{ old('monto') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm @error('monto') border-red-500 @enderror">
                @error('monto')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                Agregar
            </button>
        </form>
    </div>

    {{-- Lista de conceptos --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Concepto</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Monto</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($conceptos as $concepto)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $concepto->nombre }}</td>
                    <td class="px-4 py-3 text-center">S/ {{ number_format($concepto->monto, 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 text-xs rounded {{ $concepto->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $concepto->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-8 text-gray-400">No hay conceptos de pago configurados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
