@extends('layouts.app')
@section('title', 'Pagos de ' . $alumno->user->nombre)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Pagos de {{ $alumno->user->nombre }} {{ $alumno->user->apellidos }}</h1>
</div>

{{-- Resumen --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-xs text-gray-400 uppercase">Total Pendiente</p>
        <p class="text-2xl font-bold text-red-600">S/ {{ number_format($totalPendiente, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <p class="text-xs text-gray-400 uppercase">Total Pagado</p>
        <p class="text-2xl font-bold text-green-600">S/ {{ number_format($pagos->where('estado', 'pagado')->sum('monto'), 2) }}</p>
    </div>
</div>

{{-- Tabla de pagos --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Concepto</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Monto</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha Pago</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($pagos as $pago)
            <tr>
                <td class="px-4 py-2 text-gray-800">{{ $pago->conceptoPago->nombre ?? $pago->descripcion }}</td>
                <td class="px-4 py-2 text-center font-medium">S/ {{ number_format($pago->monto, 2) }}</td>
                <td class="px-4 py-2 text-center">
                    @if($pago->estado === 'pagado')
                    <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Pagado</span>
                    @else
                    <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-700">Pendiente</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-center text-gray-500">{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-8 text-gray-400">No hay pagos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="{{ route('padre.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
</div>
@endsection
