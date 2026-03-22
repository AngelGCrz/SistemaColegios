@extends('layouts.app')

@section('title', 'Importar Alumnos')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Importar Alumnos desde CSV</h1>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Instrucciones</h2>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 mb-4">
            <li>Descargue la plantilla CSV haciendo clic en el botón de abajo.</li>
            <li>Complete los datos de los alumnos en la plantilla. Las columnas <strong>nombre</strong>, <strong>apellidos</strong> y <strong>email</strong> son obligatorias.</li>
            <li>Suba el archivo CSV completado para previsualizar los datos.</li>
            <li>Revise los datos y confirme la importación.</li>
        </ol>

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.importar.plantilla') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar Plantilla
            </a>
        </div>

        <hr class="my-4">

        <h2 class="text-lg font-semibold text-gray-700 mb-4">Subir Archivo</h2>

        <form action="{{ route('admin.importar.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV</label>
                <input type="file" name="archivo" accept=".csv,.txt"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                       required>
                @error('archivo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Previsualizar
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Formato del CSV</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left font-medium text-gray-600">Columna</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-600">Requerida</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-600">Descripción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="px-3 py-2 font-mono">nombre</td><td class="px-3 py-2 text-green-600">Sí</td><td class="px-3 py-2 text-gray-500">Nombre del alumno</td></tr>
                    <tr><td class="px-3 py-2 font-mono">apellidos</td><td class="px-3 py-2 text-green-600">Sí</td><td class="px-3 py-2 text-gray-500">Apellidos del alumno</td></tr>
                    <tr><td class="px-3 py-2 font-mono">email</td><td class="px-3 py-2 text-green-600">Sí</td><td class="px-3 py-2 text-gray-500">Correo electrónico (único)</td></tr>
                    <tr><td class="px-3 py-2 font-mono">dni</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-500">Documento de identidad</td></tr>
                    <tr><td class="px-3 py-2 font-mono">telefono</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-500">Teléfono de contacto</td></tr>
                    <tr><td class="px-3 py-2 font-mono">codigo_alumno</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-500">Código único (se genera si está vacío)</td></tr>
                    <tr><td class="px-3 py-2 font-mono">fecha_nacimiento</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-500">Formato: YYYY-MM-DD</td></tr>
                    <tr><td class="px-3 py-2 font-mono">genero</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-500">M o F</td></tr>
                    <tr><td class="px-3 py-2 font-mono">direccion</td><td class="px-3 py-2 text-gray-400">No</td><td class="px-3 py-2 text-gray-500">Dirección del alumno</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
