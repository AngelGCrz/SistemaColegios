@extends('layouts.app')
@section('title', 'Colegios - Super Admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Colegios</h1>
        <p class="text-gray-500">Gestiona los colegios de la plataforma</p>
    </div>
    <a href="{{ route('superadmin.colegios.create') }}"
       class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 text-sm font-medium">
        + Nuevo Colegio
    </a>
</div>

{{-- Filtros --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, email..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
            <select name="estado" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
                <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Plan</label>
            <select name="plan" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Todos</option>
                @foreach($planes as $plan)
                    <option value="{{ $plan->slug }}" @selected(request('plan') === $plan->slug)>{{ $plan->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm font-medium">
            Filtrar
        </button>
        @if(request()->hasAny(['buscar', 'estado', 'plan']))
            <a href="{{ route('superadmin.colegios.index') }}" class="text-sm text-gray-500 hover:underline py-2">Limpiar</a>
        @endif
    </form>
</div>

{{-- Tabla --}}
<div class="bg-white rounded-xl shadow-sm border">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Colegio</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Contacto</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Plan</th>
                    <th class="text-center px-6 py-3 font-medium text-gray-500">Alumnos</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">Estado</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($colegios as $colegio)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <div class="font-medium text-gray-800">{{ $colegio->nombre }}</div>
                        <div class="text-xs text-gray-400">{{ $colegio->subdominio ? $colegio->subdominio . '.' . config('app.domain') : $colegio->email }}</div>
                    </td>
                    <td class="px-6 py-3">
                        <div class="text-gray-700">{{ $colegio->contacto_nombre }}</div>
                        <div class="text-xs text-gray-400">{{ $colegio->contacto_email }}</div>
                    </td>
                    <td class="px-6 py-3">
                        @if($colegio->suscripcionActiva?->plan)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-700">
                                {{ $colegio->suscripcionActiva->plan->nombre }}
                            </span>
                            @if($colegio->suscripcionActiva->estado === 'trial')
                                <span class="text-xs text-orange-500 block mt-0.5">Trial · {{ $colegio->suscripcionActiva->diasRestantes() }} días</span>
                            @endif
                        @else
                            <span class="text-gray-400">Sin plan</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-center text-gray-700">{{ $colegio->alumnos_count }}</td>
                    <td class="px-6 py-3">
                        @if($colegio->activo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Activo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right space-x-2">
                        <a href="{{ route('superadmin.colegios.show', $colegio) }}" class="text-primary-600 hover:underline">Ver</a>
                        <a href="{{ route('superadmin.colegios.edit', $colegio) }}" class="text-gray-600 hover:underline">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">No se encontraron colegios.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($colegios->hasPages())
        <div class="px-6 py-4 border-t">{{ $colegios->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
