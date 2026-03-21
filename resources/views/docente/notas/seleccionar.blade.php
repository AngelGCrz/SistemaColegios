@extends('layouts.app')
@section('title', 'Seleccionar Curso - Notas')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Registrar Notas</h1>
<p class="text-gray-500 mb-6">Seleccione el curso, sección y bimestre para registrar notas.</p>

<form method="GET" id="form-seleccion" class="bg-white rounded-xl shadow-sm border p-6 max-w-lg space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Curso - Sección</label>
        <select id="curso_seccion" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Seleccionar...</option>
            @foreach($cursoSecciones as $cs)
                <option value="{{ $cs->id }}">
                    {{ $cs->curso->nombre }} — {{ $cs->seccion->grado->nivel->nombre }} {{ $cs->seccion->grado->nombre }} "{{ $cs->seccion->nombre }}"
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bimestre</label>
        <select id="bimestre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Seleccionar...</option>
            @foreach($bimestres as $b)
                <option value="{{ $b->id }}">{{ $b->nombre }}</option>
            @endforeach
        </select>
    </div>

    <button type="button" onclick="irAPlanilla()"
            class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
        Ir a la planilla
    </button>
</form>

@push('scripts')
<script>
function irAPlanilla() {
    const cs = document.getElementById('curso_seccion').value;
    const b = document.getElementById('bimestre').value;
    if (cs && b) {
        window.location.href = `/docente/notas/${cs}/${b}`;
    }
}
</script>
@endpush
@endsection
