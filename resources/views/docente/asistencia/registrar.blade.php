@extends('layouts.app')
@section('title', 'Registrar Asistencia')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Registrar Asistencia</h1>
    <p class="text-gray-500 text-sm">Fecha: {{ $fecha->format('d/m/Y') }}</p>
</div>

<form method="POST" action="{{ route('docente.asistencia.guardar') }}">
    @csrf
    <input type="hidden" name="seccion_id" value="{{ $seccionId }}">
    <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-8">#</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Alumno</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Presente</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Falta</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Tardanza</th>
                        <th class="text-center px-4 py-3 font-medium text-gray-600">Justificada</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($matriculas as $index => $matricula)
                    @php $estadoActual = $asistenciasExistentes[$matricula->id] ?? 'presente'; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-800">
                            {{ $matricula->alumno->user->apellidos }}, {{ $matricula->alumno->user->nombre }}
                        </td>
                        <input type="hidden" name="asistencias[{{ $index }}][matricula_id]" value="{{ $matricula->id }}">
                        @foreach(['presente','falta','tardanza','justificada'] as $estado)
                        <td class="px-4 py-2 text-center">
                            <input type="radio"
                                   name="asistencias[{{ $index }}][estado]"
                                   value="{{ $estado }}"
                                   @checked($estadoActual === $estado)
                                   class="w-4 h-4 text-blue-600">
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex justify-between items-center">
        <a href="{{ route('docente.asistencia.seleccionar') }}" class="text-sm text-gray-500 hover:text-gray-700">Volver</a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
            Guardar Asistencia
        </button>
    </div>
</form>
@endsection
