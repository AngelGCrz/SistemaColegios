@extends('layouts.app')

@section('title', 'Exportar Datos')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Exportar Datos a Excel</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Alumnos --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-700">Alumnos</h2>
            </div>
            <p class="text-sm text-gray-500 mb-4">Exportar listado completo de alumnos con sus datos personales.</p>
            <a href="{{ route('admin.exportar.alumnos') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar Excel
            </a>
        </div>

        {{-- Notas --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-700">Notas</h2>
            </div>
            <p class="text-sm text-gray-500 mb-3">Exportar notas de los alumnos por periodo.</p>
            <form action="{{ route('admin.exportar.notas') }}" method="GET" class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Periodo</label>
                    <select name="periodo_id" class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach($periodos as $p)
                            <option value="{{ $p->id }}" @selected($periodoActivo && $p->id === $periodoActivo->id)>
                                {{ $p->nombre }} {{ $p->anio }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar
                </button>
            </form>
        </div>

        {{-- Asistencia --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-700">Asistencia</h2>
            </div>
            <p class="text-sm text-gray-500 mb-3">Exportar registro de asistencia por rango de fechas.</p>
            <form action="{{ route('admin.exportar.asistencia') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Desde</label>
                        <input type="date" name="fecha_desde" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar
                </button>
            </form>
        </div>

        {{-- Pagos --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-700">Pagos</h2>
            </div>
            <p class="text-sm text-gray-500 mb-3">Exportar registro de pagos y estado de cuenta.</p>
            <form action="{{ route('admin.exportar.pagos') }}" method="GET" class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Estado</label>
                    <select name="estado" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Todos</option>
                        <option value="pagado">Pagado</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="vencido">Vencido</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
