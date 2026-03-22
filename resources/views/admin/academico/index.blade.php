@extends('layouts.app')
@section('title', 'Gestión Académica')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestión Académica</h1>
    <p class="text-gray-500 text-sm">Niveles, grados, secciones, cursos y asignaciones</p>
</div>

<div x-data="{ tab: '{{ $activeTab }}' }" class="space-y-4">
    {{-- Tabs --}}
    <div class="flex gap-2">
        <button @click="tab='niveles'" :class="tab==='niveles' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium transition">Niveles y Grados</button>
        <button @click="tab='secciones'" :class="tab==='secciones' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium transition">Secciones</button>
        <button @click="tab='cursos'" :class="tab==='cursos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium transition">Cursos</button>
        <button @click="tab='asignaciones'" :class="tab==='asignaciones' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-medium transition">Asignaciones</button>
    </div>

    {{-- Niveles y Grados --}}
    <div x-show="tab==='niveles'" class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Niveles</h2>
        </div>
        <form method="POST" action="{{ route('admin.academico.niveles.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="text" name="nombre" placeholder="Ej: Primaria" required class="border rounded-lg px-3 py-2 text-sm flex-1">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Agregar</button>
        </form>
        @foreach($niveles as $nivel)
        <div class="mb-4 border rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">{{ $nivel->nombre }}</h3>
            <div class="ml-4 space-y-1">
                @foreach($nivel->grados as $grado)
                <div class="text-sm text-gray-600 flex justify-between items-center">
                    <span>{{ $grado->nombre }}</span>
                </div>
                @endforeach
            </div>
            <form method="POST" action="{{ route('admin.academico.grados.store') }}" class="flex gap-2 mt-2 ml-4">
                @csrf
                <input type="hidden" name="nivel_id" value="{{ $nivel->id }}">
                <input type="text" name="nombre" placeholder="Ej: 1er Grado" required class="border rounded px-2 py-1 text-sm flex-1">
                <button type="submit" class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200">+ Grado</button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Secciones --}}
    <div x-show="tab==='secciones'" class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold mb-4">Secciones</h2>
        <form method="POST" action="{{ route('admin.academico.secciones.store') }}" class="flex gap-2 mb-4 flex-wrap">
            @csrf
            <select name="grado_id" required class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Grado...</option>
                @foreach($niveles as $nivel)
                <optgroup label="{{ $nivel->nombre }}">
                    @foreach($nivel->grados as $grado)
                    <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                    @endforeach
                </optgroup>
                @endforeach
            </select>
            <select name="periodo_id" required class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Periodo...</option>
                @foreach($periodos as $periodo)
                <option value="{{ $periodo->id }}" {{ $periodo->activo ? 'selected' : '' }}>{{ $periodo->nombre }}</option>
                @endforeach
            </select>
            <input type="text" name="nombre" placeholder="Ej: A" required class="border rounded-lg px-3 py-2 text-sm w-32">
            <input type="number" name="capacidad" placeholder="Capacidad" required class="border rounded-lg px-3 py-2 text-sm w-28">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Crear</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-2">Sección</th>
                        <th class="text-center px-4 py-2">Capacidad</th>
                        <th class="text-center px-4 py-2">Matriculados</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($secciones as $seccion)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $seccion->nombreCompleto() }}</td>
                        <td class="px-4 py-2 text-center">{{ $seccion->capacidad ?? '—' }}</td>
                        <td class="px-4 py-2 text-center">{{ $seccion->matriculas_count ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Cursos --}}
    <div x-show="tab==='cursos'" class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold mb-4">Cursos</h2>
        <form method="POST" action="{{ route('admin.academico.cursos.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="text" name="nombre" placeholder="Ej: Matemáticas" required class="border rounded-lg px-3 py-2 text-sm flex-1">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Agregar</button>
        </form>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            @foreach($cursos as $curso)
            <div class="border rounded-lg p-3 text-sm">
                <span class="font-medium text-gray-800">{{ $curso->nombre }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Asignaciones (Curso → Sección → Docente) --}}
    <div x-show="tab==='asignaciones'" class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold mb-4">Asignar Curso a Sección</h2>
        <form method="POST" action="{{ route('admin.academico.asignaciones.store') }}" class="flex gap-2 mb-4 flex-wrap">
            @csrf
            <select name="curso_id" required class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Curso...</option>
                @foreach($cursos as $curso)
                <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                @endforeach
            </select>
            <select name="seccion_id" required class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Sección...</option>
                @foreach($secciones as $seccion)
                <option value="{{ $seccion->id }}">{{ $seccion->nombreCompleto() }}</option>
                @endforeach
            </select>
            <select name="docente_id" required class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Docente...</option>
                @foreach($docentes as $docente)
                <option value="{{ $docente->id }}">{{ $docente->user->apellidos }}, {{ $docente->user->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Asignar</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-2">Curso</th>
                        <th class="text-left px-4 py-2">Sección</th>
                        <th class="text-left px-4 py-2">Docente</th>
                        <th class="text-center px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($asignaciones as $asig)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $asig->curso->nombre }}</td>
                        <td class="px-4 py-2">{{ $asig->seccion->nombreCompleto() }}</td>
                        <td class="px-4 py-2">{{ $asig->docente->user->apellidos ?? '—' }}, {{ $asig->docente->user->nombre ?? '' }}</td>
                        <td class="px-4 py-2 text-center">
                            <form method="POST" action="{{ route('admin.academico.asignaciones.destroy', $asig) }}" onsubmit="return confirm('¿Eliminar asignación?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
