@extends('layouts.app')
@section('title', 'Asistencia - Seleccionar')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Registrar Asistencia</h1>

<form method="POST" action="{{ route('docente.asistencia.registrar') }}" class="bg-white rounded-xl shadow-sm border p-6 max-w-lg space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sección</label>
        <select name="seccion_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Seleccionar...</option>
            @foreach($secciones as $s)
                <option value="{{ $s->id }}">{{ $s->grado->nivel->nombre }} - {{ $s->grado->nombre }} "{{ $s->nombre }}"</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
        <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
    </div>

    <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
        Cargar Lista
    </button>
</form>
@endsection
