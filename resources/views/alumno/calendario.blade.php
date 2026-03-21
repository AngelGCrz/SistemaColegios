@extends('layouts.app')
@section('title', 'Calendario de Tareas')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Calendario de Tareas</h1>
</div>

@php
    $inicioMes = $fecha->copy()->startOfMonth();
    $finMes = $fecha->copy()->endOfMonth();
    $diaSemanaInicio = $inicioMes->dayOfWeek; // 0=domingo
    $diasEnMes = $fecha->daysInMonth;
    $mesAnterior = $fecha->copy()->subMonth();
    $mesSiguiente = $fecha->copy()->addMonth();
@endphp

<div class="bg-white rounded-xl shadow-sm border overflow-hidden max-w-4xl">
    {{-- Navigation --}}
    <div class="flex items-center justify-between px-6 py-4 border-b">
        <a href="{{ route('alumno.calendario', ['mes' => $mesAnterior->month, 'anio' => $mesAnterior->year]) }}"
           class="p-2 hover:bg-gray-100 rounded-lg text-gray-600">&larr;</a>
        <h2 class="text-lg font-semibold text-gray-800 capitalize">{{ $fecha->translatedFormat('F Y') }}</h2>
        <a href="{{ route('alumno.calendario', ['mes' => $mesSiguiente->month, 'anio' => $mesSiguiente->year]) }}"
           class="p-2 hover:bg-gray-100 rounded-lg text-gray-600">&rarr;</a>
    </div>

    {{-- Days header --}}
    <div class="grid grid-cols-7 text-center text-xs font-medium text-gray-500 border-b bg-gray-50">
        @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $dia)
        <div class="py-2">{{ $dia }}</div>
        @endforeach
    </div>

    {{-- Calendar grid --}}
    <div class="grid grid-cols-7">
        {{-- Empty cells before first day --}}
        @for($i = 0; $i < $diaSemanaInicio; $i++)
        <div class="min-h-[80px] border-b border-r bg-gray-50"></div>
        @endfor

        {{-- Days --}}
        @for($dia = 1; $dia <= $diasEnMes; $dia++)
        @php
            $esHoy = now()->year == $anio && now()->month == $mes && now()->day == $dia;
            $tareasDelDia = $tareasPorDia->get($dia, collect());
        @endphp
        <div class="min-h-[80px] border-b border-r p-1 {{ $esHoy ? 'bg-blue-50' : '' }}">
            <span class="text-xs font-medium {{ $esHoy ? 'text-blue-600' : 'text-gray-500' }}">{{ $dia }}</span>
            @foreach($tareasDelDia as $tarea)
            @php
                $entregada = $tarea->entregas->isNotEmpty();
            @endphp
            <div class="mt-1 px-1.5 py-0.5 rounded text-xs truncate {{ $entregada ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}"
                 title="{{ $tarea->titulo }} — {{ $tarea->cursoSeccion->curso->nombre }}">
                {{ Str::limit($tarea->titulo, 15) }}
            </div>
            @endforeach
        </div>
        @endfor

        {{-- Empty cells after last day --}}
        @php $celdasRestantes = 7 - (($diaSemanaInicio + $diasEnMes) % 7); @endphp
        @if($celdasRestantes < 7)
        @for($i = 0; $i < $celdasRestantes; $i++)
        <div class="min-h-[80px] border-b border-r bg-gray-50"></div>
        @endfor
        @endif
    </div>
</div>

{{-- Legend --}}
<div class="mt-4 flex gap-4 text-xs text-gray-500">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-100 inline-block"></span> Pendiente</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 inline-block"></span> Entregada</span>
</div>
@endsection
