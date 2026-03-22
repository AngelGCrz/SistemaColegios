@extends('layouts.app')

@section('title', 'Previsualizar Importación')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Previsualizar Importación</h1>

    <div class="flex items-center gap-4 mb-6">
        <span class="text-sm text-gray-600">
            {{ count($validated) }} registros encontrados
        </span>
        @if($totalErrors > 0)
            <span class="text-sm text-red-600 font-medium">
                {{ $totalErrors }} con errores (serán omitidos)
            </span>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto mb-6">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left font-medium text-gray-600">#</th>
                    @foreach($headers as $h)
                        <th class="px-3 py-2 text-left font-medium text-gray-600">{{ ucfirst($h) }}</th>
                    @endforeach
                    <th class="px-3 py-2 text-left font-medium text-gray-600">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($validated as $row)
                    <tr class="{{ !empty($row['_errors']) ? 'bg-red-50' : '' }}">
                        <td class="px-3 py-2 text-gray-500">{{ $row['_row'] }}</td>
                        @foreach($headers as $h)
                            <td class="px-3 py-2">{{ $row[$h] ?? '' }}</td>
                        @endforeach
                        <td class="px-3 py-2">
                            @if(empty($row['_errors']))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                            @else
                                @foreach($row['_errors'] as $err)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-1 mb-1">{{ $err }}</span>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center gap-4">
        <form action="{{ route('admin.importar.store') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Confirmar Importación ({{ count($validated) - $totalErrors }} alumnos)
            </button>
        </form>

        <a href="{{ route('admin.importar.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            Cancelar
        </a>
    </div>
</div>
@endsection
