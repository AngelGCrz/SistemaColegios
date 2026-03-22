@extends('layouts.app')
@section('title', 'Reportes Avanzados')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">📊 Reportes Avanzados</h1>
    <p class="text-gray-500 text-sm">Visualización de datos del colegio con gráficos interactivos</p>
</div>

{{-- Selector de periodo --}}
<div class="mb-6 flex items-center gap-4">
    <label class="text-sm font-medium text-gray-700">Periodo:</label>
    <select id="periodoSelect" class="rounded-lg border-gray-300 text-sm">
        @foreach($periodos as $p)
            <option value="{{ $p->id }}" {{ $periodoActivo && $periodoActivo->id === $p->id ? 'selected' : '' }}>
                {{ $p->nombre }}
            </option>
        @endforeach
    </select>
    <button onclick="cargarTodos()" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700 transition">
        Actualizar
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Promedio de notas por curso --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Promedio de Notas por Curso</h3>
        <canvas id="chartNotasCurso" height="250"></canvas>
    </div>

    {{-- Rendimiento general --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Distribución de Rendimiento</h3>
        <canvas id="chartRendimiento" height="250"></canvas>
    </div>

    {{-- Asistencia mensual --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Asistencia Mensual</h3>
        <canvas id="chartAsistencia" height="250"></canvas>
    </div>

    {{-- Pagos mensuales --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Pagos Mensuales (S/.)</h3>
        <canvas id="chartPagos" height="250"></canvas>
    </div>

    {{-- Matrículas por nivel --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Matrículas por Nivel</h3>
        <canvas id="chartMatriculas" height="250"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const charts = {};

    const colores = {
        azul: 'rgba(37, 99, 235, 0.8)',
        verde: 'rgba(16, 185, 129, 0.8)',
        amarillo: 'rgba(245, 158, 11, 0.8)',
        rojo: 'rgba(239, 68, 68, 0.8)',
        morado: 'rgba(139, 92, 246, 0.8)',
        gris: 'rgba(107, 114, 128, 0.8)',
    };

    const coloresBg = {
        azul: 'rgba(37, 99, 235, 0.15)',
        verde: 'rgba(16, 185, 129, 0.15)',
        amarillo: 'rgba(245, 158, 11, 0.15)',
        rojo: 'rgba(239, 68, 68, 0.15)',
    };

    function getPeriodoId() {
        return document.getElementById('periodoSelect').value;
    }

    async function fetchData(url, params = {}) {
        const query = new URLSearchParams(params).toString();
        const fullUrl = url + (query ? '?' + query : '');
        const res = await fetch(fullUrl, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        return res.json();
    }

    function destroyChart(id) {
        if (charts[id]) { charts[id].destroy(); }
    }

    async function cargarNotasPorCurso() {
        const data = await fetchData('/admin/reportes/api/notas-por-curso', { periodo_id: getPeriodoId() });
        destroyChart('chartNotasCurso');
        charts['chartNotasCurso'] = new Chart(document.getElementById('chartNotasCurso'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Promedio',
                    data: data.data,
                    backgroundColor: colores.azul,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, max: 20 } },
                plugins: { legend: { display: false } }
            }
        });
    }

    async function cargarRendimiento() {
        const data = await fetchData('/admin/reportes/api/rendimiento-general', { periodo_id: getPeriodoId() });
        destroyChart('chartRendimiento');
        charts['chartRendimiento'] = new Chart(document.getElementById('chartRendimiento'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: [colores.verde, colores.azul, colores.amarillo, colores.rojo],
                }]
            },
            options: { responsive: true }
        });
    }

    async function cargarAsistencia() {
        const data = await fetchData('/admin/reportes/api/asistencia-mensual', { anio: new Date().getFullYear() });
        destroyChart('chartAsistencia');
        charts['chartAsistencia'] = new Chart(document.getElementById('chartAsistencia'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Presente', data: data.datasets.presente, borderColor: colores.verde, backgroundColor: coloresBg.verde, fill: true, tension: 0.3 },
                    { label: 'Falta', data: data.datasets.falta, borderColor: colores.rojo, backgroundColor: coloresBg.rojo, fill: true, tension: 0.3 },
                    { label: 'Tardanza', data: data.datasets.tardanza, borderColor: colores.amarillo, backgroundColor: coloresBg.amarillo, fill: true, tension: 0.3 },
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    async function cargarPagos() {
        const data = await fetchData('/admin/reportes/api/pagos-mensual', { anio: new Date().getFullYear() });
        destroyChart('chartPagos');
        charts['chartPagos'] = new Chart(document.getElementById('chartPagos'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Cobrados', data: data.pagados, backgroundColor: colores.verde, borderRadius: 4 },
                    { label: 'Pendientes', data: data.pendientes, backgroundColor: colores.rojo, borderRadius: 4 },
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    async function cargarMatriculas() {
        const data = await fetchData('/admin/reportes/api/matriculas-por-nivel', { periodo_id: getPeriodoId() });
        destroyChart('chartMatriculas');
        charts['chartMatriculas'] = new Chart(document.getElementById('chartMatriculas'), {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: [colores.azul, colores.verde, colores.amarillo, colores.morado, colores.rojo],
                }]
            },
            options: { responsive: true }
        });
    }

    function cargarTodos() {
        cargarNotasPorCurso();
        cargarRendimiento();
        cargarAsistencia();
        cargarPagos();
        cargarMatriculas();
    }

    // Cargar al inicio
    document.addEventListener('DOMContentLoaded', cargarTodos);
</script>
@endpush
