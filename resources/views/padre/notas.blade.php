@extends('layouts.app')
@section('title', 'Notas de ' . $alumno->user->nombre)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Notas de {{ $alumno->user->nombre }} {{ $alumno->user->apellidos }}</h1>
    @if($matricula)
    <p class="text-gray-500 text-sm">{{ $matricula->periodo->nombre ?? '' }} · {{ $matricula->seccion->nombreCompleto() ?? '' }}</p>
    @endif
</div>

@foreach($cursos as $cursoSeccion)
<div class="bg-white rounded-xl shadow-sm border mb-4 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b">
        <h3 class="font-semibold text-gray-800">{{ $cursoSeccion->curso->nombre }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    @foreach($bimestres as $bimestre)
                    <th class="px-4 py-2 text-center font-medium text-gray-600">{{ $bimestre->nombre }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-center font-bold text-gray-700 bg-gray-50">Promedio</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php $total = 0; $count = 0; @endphp
                    @foreach($bimestres as $bimestre)
                    @php
                        $nota = $notas->where('curso_seccion_id', $cursoSeccion->id)->where('bimestre_id', $bimestre->id)->first();
                    @endphp
                    <td class="px-4 py-3 text-center">
                        @if($nota)
                        <span class="text-lg font-bold {{ $nota->nota >= 14 ? 'text-green-600' : ($nota->nota >= 11 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $nota->nota }}
                        </span>
                        <span class="block text-xs text-gray-400">{{ $nota->nota_letra }}</span>
                        @php $total += $nota->nota; $count++; @endphp
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="px-4 py-3 text-center bg-gray-50">
                        @if($count > 0)
                        <span class="text-lg font-bold text-blue-600">{{ number_format($total / $count, 1) }}</span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach

<div class="flex justify-between items-center mt-4">
    <a href="{{ route('padre.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
    @if($matricula)
    <a href="{{ route('boleta.pdf', $matricula) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
        📄 Descargar Boleta
    </a>
    @endif
</div>
@endsection
