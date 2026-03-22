@extends('layouts.app')
@section('title', 'Pagos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión de Pagos</h1>
    <a href="{{ route('admin.pagos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Registrar Pago
    </a>
</div>

<div x-data="{ tab: 'pagos' }">
    <div class="flex gap-2 mb-4">
        <button @click="tab='pagos'" :class="tab==='pagos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium transition">Pagos</button>
        <button @click="tab='conceptos'" :class="tab==='conceptos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium transition">Conceptos de Pago</button>
    </div>

    {{-- Lista de Pagos --}}
    <div x-show="tab==='pagos'">
        <div class="flex gap-3 mb-4">
            <form method="GET" class="flex gap-2">
                <select name="estado" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendiente</option>
                    <option value="pagado" @selected(request('estado') === 'pagado')>Pagado</option>
                </select>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Alumno</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Concepto</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Monto</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pagos as $pago)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $pago->alumno->user->apellidos }}, {{ $pago->alumno->user->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $pago->conceptoPago->nombre ?? $pago->descripcion }}</td>
                        <td class="px-4 py-3 text-center font-medium">S/ {{ number_format($pago->monto, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 text-xs rounded {{ $pago->estado === 'pagado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($pago->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($pago->estado === 'pendiente')
                            <form method="POST" action="{{ route('admin.pagos.pagado', $pago) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="fecha_pago" value="{{ date('Y-m-d') }}">
                                <button class="text-xs text-green-600 hover:underline">Marcar Pagado</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400">{{ $pago->fecha_pago?->format('d/m/Y') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-gray-400">No hay pagos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pagos->hasPages())
        <div class="mt-4">{{ $pagos->links() }}</div>
        @endif
    </div>

    {{-- Conceptos de Pago --}}
    <div x-show="tab==='conceptos'">
        <form method="POST" action="{{ route('admin.pagos.conceptos.store') }}" class="flex gap-2 mb-4 flex-wrap">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre del concepto" required class="border rounded-lg px-3 py-2 text-sm flex-1">
            <input type="number" name="monto" placeholder="Monto" step="0.01" required class="border rounded-lg px-3 py-2 text-sm w-32">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Agregar</button>
        </form>
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3">Concepto</th>
                        <th class="text-center px-4 py-3">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($conceptos as $concepto)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $concepto->nombre }}</td>
                        <td class="px-4 py-2 text-center">S/ {{ number_format($concepto->monto, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
