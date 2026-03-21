@extends('layouts.app')
@section('title', 'Estado de Cuenta')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.pagos.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Estado de Cuenta</h1>
            <p class="text-sm text-gray-500">{{ $alumno->user->apellidos }}, {{ $alumno->user->nombre }}</p>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Total General</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">S/ {{ number_format($totalPendiente + $totalPagado, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-green-600 uppercase tracking-wider">Total Pagado</p>
            <p class="text-2xl font-bold text-green-600 mt-1">S/ {{ number_format($totalPagado, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-red-600 uppercase tracking-wider">Total Pendiente</p>
            <p class="text-2xl font-bold text-red-600 mt-1">S/ {{ number_format($totalPendiente, 2) }}</p>
        </div>
    </div>

    {{-- Tabla de movimientos --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-600">Detalle de Pagos</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Concepto</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Monto</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Estado</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Fecha Pago</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Método</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Observación</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($pagos as $pago)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $pago->conceptoPago->nombre ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">S/ {{ number_format($pago->monto, 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($pago->estado === 'pagado')
                        <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-700">Pagado</span>
                        @elseif($pago->estado === 'anulado')
                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-500">Anulado</span>
                        @else
                        <span class="px-2 py-0.5 text-xs rounded bg-red-100 text-red-700">Pendiente</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $pago->fecha_pago?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ ucfirst($pago->metodo_pago ?? '—') }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $pago->observacion ?? '' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">No hay pagos registrados para este alumno.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
