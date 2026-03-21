@extends('layouts.app')
@section('title', 'Dashboard - Administrador')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-500">Bienvenido, {{ auth()->user()->nombreCompleto() }}</p>
</div>

{{-- Cards de resumen --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Total Alumnos --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Alumnos</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalAlumnos) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Matrículas Activas --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Matrículas Activas</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($matriculasActivas) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Asistencia Hoy --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Registros Hoy</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($asistenciaHoy) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Pagos del Mes --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Cobrado este mes</p>
                <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($pagosDelMes, 2) }}</p>
                <p class="text-xs text-orange-600">Pendiente: S/ {{ number_format($pagosPendientes, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Accesos rápidos --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('admin.matriculas.create') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition flex items-center space-x-4">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <div>
            <p class="font-medium text-gray-800">Nueva Matrícula</p>
            <p class="text-sm text-gray-500">Registrar alumno</p>
        </div>
    </a>

    <a href="{{ route('admin.pagos.create') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition flex items-center space-x-4">
        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
        </div>
        <div>
            <p class="font-medium text-gray-800">Registrar Pago</p>
            <p class="text-sm text-gray-500">Cobrar pensión</p>
        </div>
    </a>

    <a href="{{ route('admin.avisos.create') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition flex items-center space-x-4">
        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        </div>
        <div>
            <p class="font-medium text-gray-800">Nuevo Aviso</p>
            <p class="text-sm text-gray-500">Comunicar a padres</p>
        </div>
    </a>
</div>
@endsection
