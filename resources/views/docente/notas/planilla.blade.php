@extends('layouts.app')
@section('title', 'Planilla de Notas')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Planilla de Notas</h1>
        <p class="text-gray-500 text-sm">{{ $cursoSeccion->curso->nombre }} — {{ $bimestre->nombre }}</p>
    </div>
    <a href="{{ route('docente.notas.seleccionar') }}" class="text-sm text-blue-600 hover:underline">Cambiar curso</a>
</div>

<form method="POST" action="{{ route('docente.notas.guardar', [$cursoSeccion, $bimestre]) }}">
    @csrf

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-8">#</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Alumno</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600 w-32">Nota (0-20)</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($matriculas as $index => $matricula)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-800">
                            {{ $matricula->alumno->user->apellidos }}, {{ $matricula->alumno->user->nombre }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="hidden" name="notas[{{ $index }}][matricula_id]" value="{{ $matricula->id }}">
                            <input type="number" name="notas[{{ $index }}][nota]"
                                   value="{{ $notasExistentes[$matricula->id] ?? '' }}"
                                   min="0" max="20" step="0.5"
                                   class="w-20 text-center border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex justify-end">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            Guardar Notas
        </button>
    </div>
</form>
@endsection
